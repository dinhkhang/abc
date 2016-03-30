<?php

App::uses('Component', 'Controller');
App::uses('VinaCommon', 'Lib');
App::uses('MyCurl', 'Lib');

/**
 * @property VinaCommon VinaCommon
 */
class VinaComponent extends Component {

    public $controller = null;

    public function initialize(\Controller $controller) {
        parent::initialize($controller);

        $this->controller = $controller;
    }

    /**
     * sendSMS
     * Thực hiện gọi sms service để gửi Mt
     * 
     * @param string $msisdn
     * @param string $content
     * 
     * @return int
     */
    public function sendSMS($msisdn, $content) {

        $this->controller->sms_raw_response = '';
        $this->controller->sms_sent_url = '';
        $this->controller->sms_error = array();

        $curl = new MyCurl();
        $url = VinaCommon::makeSmsUrl($msisdn, $content);
        $this->controller->sms_sent_url = $url;

        // thực hiện lưu lại đường dẫn gửi mt
        $this->logAnyFile($url, __CLASS__ . '_' . __FUNCTION__);

        try {

            $response = $curl->get($url);
            $this->controller->sms_raw_response = isset($response->body) ? $response->body : '';
            if (isset($response->body)) {

                return ($response->body == 0) ? 1 : 0;
            }

            return 0;
        } catch (Exception $ex) {

            $this->controller->logAnyFile('Throw exception when send sms to msisdn "' . $msisdn . '", error detail:', $this->controller->log_file_name);
            $this->controller->logAnyFile($ex->getMessage(), $this->controller->log_file_name);

            $this->controller->sms_error['curl_exception'] = $ex->getMessage();
            $this->controller->sms_error['curl_error'] = $curl->error();

            return 0;
        }
    }

    protected function parseSMSResponse($raw_response) {

        $extract = explode(':', $raw_response);
        if (empty($extract)) {

            return 0;
        }
    }

    /**
     * isRegisteredPackage
     * kiểm tra xem content_service nói chung (content_service, player) đã đăng ký gói cước nào khác chưa?
     * Do luật chỉ được 1 trong các gói B1, B7, B30 hoặc 1 trong các gói G1, G7, G30
     *
     * @param array $content_service
     * @param string $model_name
     * 
     * @return bool
     */
    public function isRegisteredPackage($content_service, $model_name) {

        $packages = !empty($content_service[$model_name]['packages']) ?
                $content_service[$model_name]['packages'] : array();

        if (empty($packages)) {

            return false;
        }

        foreach ($packages as $pkg) {

            // nếu gói package thuộc type trả phí và có status là 1: đã đăng ký, 4: gia hạn thất bại
            if (
                    (
                    $pkg['status'] == 1 ||
                    $pkg['status'] == 4
                    ) &&
                    in_array($pkg['type'], array(PACKAGE_PREMIUM, PACKAGE_GAMEQUIZ))
            ) {

                return $pkg['package'];
            }
        }

        return false;
    }

    /**
     * setRegisterPackage
     * Thực hiện lưu giá trị register package vào content_service nói chung (content_serivce, player)
     *
     * @param array $content_service
     * @param string $model_name - Tên model ví dụ: ContentService, Player
     * @param array $package
     * @param string $channel - Tên kênh channel đăng ký
     * @param int $is_free
     * 
     * @throws Exception
     */
    public function setRegisterPackage(&$content_service, $model_name, $package, $channel, $is_free) {

        $packages = !empty($content_service[$model_name]['packages']) ?
                $content_service[$model_name]['packages'] : array();

        $free_day = Configure::read('vina.free_day');
        $packagename = $package['code'];

        // thực hiện giả lập charge
        $charge_emu = !empty($content_service[$model_name]['charge_emu']) ?
                $content_service[$model_name]['charge_emu'] : array();

        // lấy ra tên gói cước đang được đăng ký
        $registeredPackage = $this->isRegisteredPackage($content_service, $model_name);

        // nếu gói package đăng ký có kiểu type khác với PACKAGE_FREE
        if ($package['type'] != PACKAGE_FREE) {

            // nếu đăng ký lần được được miễn phí
            if ($is_free) {

                // thời điểm charge_at sẽ tính từ 00:00:00 của ngày
                $charge_at = VinaCommon::getBeginOfMongoDate(strtotime($free_day));
                $expire_at = VinaCommon::getExpriedDateByPackage($charge_at, $packagename, $charge_emu);

                // nếu là đăng ký lần đầu với gói package khác với PACKAGE_FREE
                // thì thực hiện ghi nhận lại first_register_at
                $content_service[$model_name]['first_register_at'] = new MongoDate();
            } else {

                // thời điểm charge_at sẽ tính từ 00:00:00
                $charge_at = VinaCommon::getChargeDateByPackage(new MongoDate(), $packagename, $charge_emu);

                // thời điểm hết hạn tính đến 23:59:59
                $expire_at = VinaCommon::getExpriedDateByPackage(new MongoDate(), $packagename, $charge_emu);
            }
        }
        // đối với gói package miễn phí, thời gian hết hạn, charge sẽ lấy dựa vào gói package trả phí
        else {
            // thời điểm charge_at sẽ tính từ 00:00:00
            $charge_at = !empty($packages[$registeredPackage]['charge_at']) ?
                    $packages[$registeredPackage]['charge_at'] : null;

            // thời điểm hết hạn tính đến 23:59:59
            $expire_at = !empty($packages[$registeredPackage]['expire_at']) ?
                    $packages[$registeredPackage]['expire_at'] : null;
        }

        // nếu gói package khác với PACKAGE_FREE
        // ghi nhận lại register_at
        if ($package['type'] != PACKAGE_FREE) {

            $content_service[$model_name]['last_register_at'] = new MongoDate();
        }

        // nếu chưa đăng ký package bao giờ, thì khởi tạo
        if (empty($content_service[$model_name]['packages'][$package['code']])) {

            $packages[$package['code']] = array(
                'package' => $package['code'],
                'type' => $package['type'],
                'status' => 1,
                'retry' => 0,
                'modified' => new MongoDate(),
                'created' => new MongoDate(),
                'charge_at' => $charge_at,
                'channel' => $channel,
                'last_register_at' => new MongoDate(),
                'last_unregister_at' => null,
                'last_renew_at' => null,
                'last_retry_renew_at' => null,
                'last_reset_at' => null,
            );
        } else {

            $packages[$package['code']]['status'] = 1;
            $packages[$package['code']]['type'] = $package['type'];
            $packages[$package['code']]['channel'] = $channel;
            $packages[$package['code']]['retry'] = 0;
            $packages[$package['code']]['modified'] = new MongoDate();
            $packages[$package['code']]['charge_at'] = $charge_at;
            $packages[$package['code']]['last_register_at'] = new MongoDate();
        }

        $packages[$package['code']]['expire_at'] = $expire_at;
        $content_service[$model_name]['packages'] = $packages;

        // nếu gói package khác với PACKAGE_FREE
        if ($package['type'] != PACKAGE_FREE) {

            $content_service[$model_name]['status'] = 1;
            $content_service[$model_name]['channel'] = $channel;
            $content_service[$model_name]['last_charge_at'] = new MongoDate();
            $content_service[$model_name]['last_action_at'] = new MongoDate();
        }
    }

    public function setUnRegisterPackage(&$content_service, $model_name, $package, $channel) {

        $content_service[$model_name]['packages'][$package['code']]['status'] = 3;
        $content_service[$model_name]['packages'][$package['code']]['retry'] = 0;
        $content_service[$model_name]['packages'][$package['code']]['unregister_channel'] = $channel;
        $content_service[$model_name]['packages'][$package['code']]['modified'] = new MongoDate();
        $content_service[$model_name]['packages'][$package['code']]['expire_at'] = new MongoDate();
        $content_service[$model_name]['packages'][$package['code']]['last_unregister_at'] = new MongoDate();

        // nếu gói package khác với PACKAGE_FREE
        if ($package['type'] != PACKAGE_FREE) {

            $content_service[$model_name]['status'] = 3;
            $content_service[$model_name]['distributor_code'] = '';
            $content_service[$model_name]['distribution_channel_code'] = '';
            $content_service[$model_name]['last_unregister_at'] = new MongoDate();
            $content_service[$model_name]['last_self_unregister_at'] = new MongoDate();
            $content_service[$model_name]['unregister_channel'] = $channel;
            $content_service[$model_name]['time_last_action'] = new MongoDate();

            // thực hiện xóa giả lập charge_emu
            $content_service[$model_name]['charge_emu'] = null;
            $content_service[$model_name]['last_play_at'] = null;
        }

        // thực hiện xóa bỏ các gói Free Package liên quan
        $this->setUnRegisterFreePackage($content_service, $model_name, $channel);
    }

    /**
     * setUnRegisterFreePackage
     * Thực hiện set thông tin hủy các gói package có type=PACKAGE_FREE
     *
     * @param array $content_service
     */
    protected function setUnRegisterFreePackage(&$content_service, $model_name, $channel) {

        $packages = $content_service[$model_name]['packages'];
        foreach ($packages as $code => $pkg) {

            // nếu package có type là PACKAGE_FREE, thì thực hiện hủy
            if ($pkg['type'] == PACKAGE_FREE) {

                $content_service[$model_name]['packages'][$code]['status'] = 3;
                $content_service[$model_name]['packages'][$code]['retry'] = 0;
                $content_service[$model_name]['packages'][$code]['unregister_channel'] = $channel;
                $content_service[$model_name]['packages'][$code]['modified'] = new MongoDate();
                $content_service[$model_name]['packages'][$code]['expire_at'] = new MongoDate();
                $content_service[$model_name]['packages'][$code]['last_register_at'] = new MongoDate();
                $content_service[$model_name]['packages'][$code]['last_self_unregister_at'] = new MongoDate();
            }
        }
    }

    /**
     * makeChargeXmlRequest
     * tạo ra charge xml request dùng để charge tiền
     * 
     * @param int $transaction_id
     * @param string $msisdn
     * @param type $have_promotion
     * @param int $debit_amount
     * @param type $original_price
     * @param type $reason
     * @param type $packagename
     * @param type $serviceId
     * @param type $note
     * @param type $channel
     * 
     * @return string - xml
     */
    public function makeXmlChargeRequest($transaction_id, $msisdn, $have_promotion, $debit_amount, $original_price, $reason, $packagename, $serviceId, $note = "", $channel = 'WAP') {

        if ($have_promotion) {

            $promotion = 1;
            $debit_amount = 0;
        } else {

            $promotion = 0;
        }

        $conf = Configure::read('vina');
        $service = $conf['service'];
        $username = $conf['charging_proxy']['user'];
        $password = $conf['charging_proxy']['pass'];
        $cp = $conf['cp'];

        /* $xml_data   = '<?xml version="1.0" encoding="utf-8" standalone="yes" ?>'; */
        $xml_data = '<CCGWRequest servicename="' . $service . '" username="' . $username . '" password="' . $password . '">';
        $xml_data .= '<RequestType>1</RequestType>';
        $xml_data .= '<SequenceNumber>' . $transaction_id . '</SequenceNumber>';
        $xml_data .= '<SubId>' . $msisdn . '</SubId>';
        $xml_data .= '<Price>' . $debit_amount . '</Price>';
        $xml_data .= '<Reason>' . $reason . '</Reason>';
        $xml_data .= '<ORIGINALPRICE>' . $original_price . '</ORIGINALPRICE>';
        $xml_data .= '<PROMOTION>' . $promotion . '</PROMOTION>';
        $xml_data .= '<NOTE>' . $note . '</NOTE>';
        $xml_data .= '<CHANNEL>' . $channel . '</CHANNEL>';
        $xml_data .= '<Content>';
        $xml_data .= '<item contenttype="SUBSCRIPTION" subcontenttype="VI" contentid="' . $serviceId . '" contentname="' . $packagename . '" cpname="' . $cp . '" note="" playtype="" contentprice=""/>';
        $xml_data .= '</Content>';
        $xml_data .= '</CCGWRequest>';

        return $xml_data;
    }

    /**
     * charge
     * Thực hiện gọi charge service của vina
     * 
     * @param string $xml_data
     * @param string $charge_url
     * 
     * @return boolean
     */
    public function charge($xml_data, $charge_url = null) {

        if (empty($charge_url)) {

            $charge_url = Configure::read('vina.charging_proxy.url');
        }
        $this->controller->charge_raw_response = '';
        $this->controller->charge_readable_message = '';
        $ch = new MyCurl();
        $ch->headers = array('Content-Type: text/xml');
        try {

            $response = $ch->post($charge_url, $xml_data);
            $this->controller->charge_raw_response = isset($response->body) ? $response->body : '';
            $pretty = $this->parseXmlChargeResponse($response);
            // nếu nhận về mã code, thì lấy về thông điệp tương ứng với mã code
            if (isset($pretty['return']) && strlen($pretty['return'])) {

                $this->controller->charge_readable_message = VinaCommon::getChargeMessageByCode($pretty['return']);
            }
            return $pretty;
        } catch (Exception $ex) {

            $this->controller->logAnyFile('throw a Exception when charging, error detail:', $this->controller->log_file_name);
            $this->controller->logAnyFile($ex->getMessage(), $this->controller->log_file_name);
            $this->controller->logAnyFile('MyCurl error:', $this->controller->log_file_name);
            $this->controller->logAnyFile($ch->error(), $this->controller->log_file_name);

            $this->controller->charge_error['curl_exception'] = $ex->getMessage();
            $this->controller->charge_error['curl_error'] = $ch->error();

            return false;
        }
    }

    /*
     * Parser respone XML from server:
     * <CCGWResponse>
      <Error>Mã lỗi</Error>
      <ErrorDesc>Mô tả lỗi</ErrorDesc>
      <InternalCode></InternalCode>
      <SequenceNumber>1234567890</SequenceNumber>
      <PRICE>15000</PRICE>
      <PROMOTION>0</PROMOTION>
      <NOTE></NOTE>
      </CCGWResponse>
      @return: array
     */

    public function parseXmlChargeResponse($data) {

        $this->controller->charge_error = array();
        try {

            $VAS = new SimpleXMLElement($data);
        } catch (Exception $e) {

            $this->controller->charge_error['xml_respone_error'] = $e->getMessage();
            $this->controller->charge_error['xml_respone'] = $data;

            $this->controller->logAnyFile('Can not parse xml response:', $this->controller->log_file_name);
            $this->controller->logAnyFile($e->getMessage(), $this->controller->log_file_name);
            $this->controller->logAnyFile('raw xml response:', $this->controller->log_file_name);
            $this->controller->logAnyFile($data, $this->controller->log_file_name);

            return false;
        }

        if ($VAS == null) {

            $this->charge_error['xml_respone_error'] = 'invalid xml format';
            $this->charge_error['xml_respone'] = $data;

            $this->controller->logAnyFile('Can not parse xml response:', $this->controller->log_file_name);
            $this->controller->logAnyFile($e->getMessage(), $this->controller->log_file_name);
            $this->controller->logAnyFile('raw xml response:', $this->controller->log_file_name);
            $this->controller->logAnyFile($data, $this->controller->log_file_name);

            return false;
        }

        $response = array();
        $response['request_id'] = isset($VAS->SequenceNumber) ? $VAS->SequenceNumber : '0';
        $response['return'] = isset($VAS->Error) ? (int) $VAS->Error : '';
        $response['error_desc'] = isset($VAS->ErrorDesc) ? $VAS->ErrorDesc : '';
        $response['price'] = isset($VAS->Price) ? $VAS->Price : '';
        $response['promotion'] = isset($VAS->Promotion) ? $VAS->Promotion : '';
        $response['note'] = isset($VAS->Note) ? $VAS->Note : '';
        $response['internal_code'] = isset($VAS->InternalCode) ? $VAS->InternalCode : '';

        return $response;
    }

    /**
     * responseError
     * định dạng xml trả về vina khi vina gọi api
     * 
     * @param int $error_no
     * @param string $message
     * @param array $fields - các trường cần điền đủ vào cấu trúc xml
     * 
     * @return string - xml
     */
    public function responseError($error_no, $message, $fields = array()) {

        header("Content-type: text/xml; charset=utf-8");
        $xmlDoc = new DOMDocument();
        $xmlDoc->encoding = "UTF-8";
        $xmlDoc->version = "1.0";

        $root = $xmlDoc->appendChild($xmlDoc->createElement("response"));
        $root->appendChild($xmlDoc->createElement("errorid", $error_no));
        $root->appendChild($xmlDoc->createElement("error_desc", htmlspecialchars($message, ENT_QUOTES)));

        if (!empty($fields)) {

            foreach ($fields as $f) {

                $root->appendChild($xmlDoc->createElement($f, null));
            }
        }

        return $xmlDoc->saveXML();
    }

    /**
     * isFree
     * kiểm tra xem thuê bao có phải lần đầu đăng ký hoặc thuê bao đăng ký lại sau 90 ngày
     * 
     * @param array $content_service
     * @param string $model_name
     * @param string $packagename
     * 
     * @return bool 
     */
    public function isFree($content_service, $model_name, $packagename) {

        // nếu đăng ký lần đầu tiên
        if (empty($content_service[$model_name]['first_register_at'])) {

            return true;
        }

        $free_reset_period = Configure::read('vina.free_reset_period');
        // nếu đăng ký lại sau 90 ngày, coi như đăng ký lần đầu tiên
        if (!empty($content_service[$model_name]['packages'][$packagename])) {

            $charge_at = $content_service[$model_name]['packages'][$packagename]['charge_at'];
            $date_charge = date('Y-m-d', $charge_at->sec);
            $date_current = date('Y-m-d');
            $date_diff = VinaCommon::dateDifference($date_charge, $date_current);
            if ($date_diff > $free_reset_period || $content_service[$model_name]['packages'][$packagename]['type'] == PACKAGE_FREE) {

                return true;
            }
        }

        return false;
    }

    /**
     * extractMoContentFromNote
     * trích xuất ra Mo content từ note nhận được từ vina
     * 
     * @param string $note
     * @param string $delimiter
     * 
     * @return string
     */
    public function extractMoContentFromNote($note, $delimiter = '|') {

        $extract = explode($delimiter, $note);
        $content = !empty($extract[1]) ? $extract[1] : '';
        return $content;
    }

    public function getClientIp() {

        $client_ip = !empty($_SERVER['HTTP_X_FORWARDED_FOR']) ?
                $_SERVER['HTTP_X_FORWARDED_FOR'] : $this->controller->request->clientIp();

        return $client_ip;
    }

    public function logSqlQuery($model_name) {

        // thực hiện log lại SQL log - phục vụ cho việc debug
        if (empty($model_name)) {

            $model_name = $this->controller->modelClass;
        }

        $log = $this->controller->$model_name->getDataSource()->getLog(false, false);
        $this->logAnyFile('SQL Query:', $this->controller->log_file_name);
        $this->logAnyFile($log, $this->controller->log_file_name);
    }

    protected function logAnyFile($content, $file_name) {

        CakeLog::config($file_name, array(
            'engine' => 'File',
            'types' => array($file_name),
            'file' => $file_name,
        ));

        $this->log($content, $file_name);
    }

}
