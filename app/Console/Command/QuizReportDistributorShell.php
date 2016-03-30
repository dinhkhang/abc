<?php

/**
 * Chú ý mở comment hàm sleep(59)
 */
class QuizReportDistributorShell extends AppShell
{

    const CHOICE_TYPE = 1; // 1 = old code, 0 = new code
    const DEBUG = 0;
    const LIMIT_RECORD_PER_BLOCK = 10000; // số bản ghi tối đa mỗi lượt truy vấn
    const STATUS_SUCCESS = 1; // trạng thái thành công
    const STATUS_FAIL = 0; // trang thái thất bại
    const ACTION_REGISTER_WAP = 'DK';
    const ACTION_REGISTER_SMS = 'DANG_KY';
    const ACTION_RENEW = 'GIA_HAN';
    const ACTION_UNREGISTER = 'HUY';
    const ACTION_BUY_QUESTION = 'MUA';
    const ACTION_ANSWER_QUESTION = 'TRA_LOI';
    const ACTION_CMS_UNREGISTER = 'HUY_CMS';
    // các gói cước
    const PACKAGE_DAY = 'G1';
    const PACKAGE_WEEK = 'G7';
    const PACKAGE_MONTH = 'G30';

    public $uses = array('DistributorDailyReport', 'Distributor', 'DistributionChannel', 'Player');
    public $has_error = FALSE;
    public $big_data = array();
    private $list_key = array();
    private $mapping_field = array('G1' => 'package_day', 'G7' => 'package_week');
    private $date; // type date, eg: d-m-Y
    private $hour; // type date, eg: d-m-Y
    private $type = 1; // daily : 0 or hourly : 1 (default)
    private $charge_players = array(); // nhóm thuê bao có phát sinh cước
    private $active_players = 0; // nhóm thuê bao kích hoạt
    private $total_players = 0; // tổng thuê bao
    private $tracking_record = ''; // kiểm soát tổng số bản ghi để so sánh vs db thật để debug
    private $player_register = array(); // lưu thuê bao đăng ký
    private $player_arrears = array();
    private $player_renew = array();
    public $default_var = array(
        'total_players' => 0, //
        'active' => 0, //
        'register' => 0, //
        'register_free' => 0, //
        'register_charge' => 0, //
        'deactive' => 0, //
        'self_deactive' => 0, //
        'system_deactive' => 0, //
        'today_deactive' => 0,
        'buy_question' => 0, //
        'arrears' => 0, // đếm lượt truy thu
        'arrears_count' => 0, // đếm thuê bao truy thu
        'arrears_success' => 0, //
        'arrears_fail' => 0, //
        'renew' => 0, // đếm lượt gia hạn
        'renew_count' => 0, // đếm thuê bao gia hạn
        'renew_success' => 0, //
        'renew_fail' => 0, //
        'revenue' => 0, //
        'real_revenue' => 0,
        'distributor_revenue' => 0,
        'renew_revenue' => 0, //
        'arrears_revenue' => 0, //
        'register_revenue' => 0, //
        'buy_question_revenue' => 0, //
        'total_player_play' => 0, //
        'total_player_play_charge' => 0,
        'total_player_play_active' => 0, //
    );
    public $default_var_count_player = array(
        'arrears_count' => 0, // đếm thuê bao truy thu
        'renew_count' => 0, // đếm thuê bao gia hạn
    );
    public $default_var_player = array(
        'total_players' => 0,
        'active' => 0,
        'total_player_play' => 0,
        'total_player_play_charge' => 0,
        'total_player_play_active' => 0,
    );
    // chứa danh sách các trường kiểu đếm lượt
    public $default_var_count = array(
        'register' => 0,
        'register_free' => 0,
        'register_charge' => 0,
        'deactive' => 0,
        'self_deactive' => 0,
        'system_deactive' => 0,
        'buy_question' => 0,
        'arrears' => 0,
        'arrears_success' => 0,
        'arrears_fail' => 0,
        'renew' => 0,
        'renew_success' => 0,
        'renew_fail' => 0,
        'revenue' => 0,
        'real_revenue' => 0,
        'distributor_revenue' => 0,
        'renew_revenue' => 0,
        'arrears_revenue' => 0,
        'register_revenue' => 0,
        'buy_question_revenue' => 0,
    );

    /**
     * Xóa 1 ngày báo cáo
     */
    public function delete()
    {
        if (isset($this->args[0]) && strlen($this->args[0])) {
            $datas = $this->{$this->modelClass}->find('all', array(
                'conditions' => array(
                    'date' => new MongoDate(strtotime($this->args[0] . ' 00:00:00'))
                )
            ));
            foreach ($datas AS $data) {
                $this->{$this->modelClass}->delete(new MongoId($data[$this->modelClass]['id']));
            }
            $this->out('Delete success ' . count($datas) . ' records of ' . $this->args[0]);
        } else {
            $this->out('Cant identify date. Check your command! Eg: QuizReport delete ' . date('d-m-Y'));
        }
    }

    public function main()
    {
        $this->out('Start: ' . date('d-m-Y H:i:s'));
        $this->hour = date('H');
        if (isset($this->args[0]) && !isset($this->args[1])) {
            for ($i = 0; $i <= 23; $i++) {
                $this->args[1] = str_pad($i, 2, 0, STR_PAD_LEFT);
                $this->_execute();
                // xóa dữ liệu big_data
                $this->big_data = array();
                $this->hr();
            }
        } else {
			// Delay the script by 59 seconds
			// mục đích cần đợi 59 giây vì crontab ko thể setup lúc 59 phút 59 giây, mà chỉ có thể setup 59 phút
			// trong 1 phút cuối có thể có nhiều bản ghi được insert nên cần đợi. Rủi ro sẽ là 1 giây
			// kiểm tra kiểu ngày hay kiểu giờ
			sleep(58);
			$this->out('Sleeping after 59s: ' . date('d-m-Y H:i:s'));
            $this->_execute();
            $this->out('Type hourly. Hour: ' . $this->hour . ' - Date: ' . $this->date);
        }
        $this->big_data = array();
        
        // for test
//        for ($j = 2; $j <= 2; $j++) {
//            for ($i = 0; $i <= 23; $i++) {
//                $this->args[0] = str_pad($j, 2, 0, STR_PAD_LEFT) . '-11-2015';
//                $this->args[1] = str_pad($i, 2, 0, STR_PAD_LEFT);
//                $this->_execute();
//                // xóa dữ liệu big_data
//                $this->big_data = array();
//                
//                if(self::DEBUG) {
//                    // thông báo tổng số bản ghi
//                    $this->out('Total record checked in mo table: ' . array_sum($this->tracking_record['mo']));
//                    foreach($this->tracking_record['mo'] AS $hour => $num) {
//                        $this->out($hour . ': ' . $num);
//                    }
//                    $this->out('Total record checked in mo table: ' . array_sum($this->tracking_record['charge']));
//                    foreach($this->tracking_record['charge'] AS $hour => $num) {
//                        $this->out($hour . ': ' . $num);
//                    }
//                }
//            }
//        }
        $this->out('End: ' . date('d-m-Y H:i:s'));
    }

    protected function _setInit(&$charge, &$model, &$date_min, &$date_max)
    {
        // lấy ra giờ cần tính toán
        if (isset($this->args[0])) {
            $day_now = $this->args[0];
            $model->useTable = 'mo_' . date('Y_m_d', strtotime($day_now));
            $charge->useTable = 'charge_' . date('Y_m_d', strtotime($day_now));
            $date_max = new MongoDate(strtotime(date('d-m-Y', strtotime($day_now)) . ' ' . $this->hour . ':59:59'));
            $date_min = new MongoDate(strtotime(date('d-m-Y', strtotime($day_now)) . ' ' . $this->hour . ':00:00'));
        } else {
            $day_now = date('d-m-Y');
            // lấy thông tin từ bảng ngày và bảng charge
            $model->useTable = 'mo_' . date('Y_m_d');
            $charge->useTable = 'charge_' . date('Y_m_d');
            // for get data hourly
            $date_max = new MongoDate(strtotime(date('d-m-Y ' . $this->hour . ':59:59')));
            $date_min = new MongoDate(strtotime(date('d-m-Y ' . $this->hour . ':00:00')));
        }
        $this->date = $day_now;
        $this->out('From: ' . date('d-m-Y H:i:s', $date_min->sec) . ' to: ' . date('d-m-Y H:i:s', $date_max->sec));
        $this->active_players = $this->Player->find('count', array('conditions' => array('status' => self::STATUS_SUCCESS)));
        $this->total_players = $this->Player->find('count');
    }

    /**
     * report hourly: Console\cake QuizReport
     * report daily: Console\cake QuizReport Daily
     */
    protected function _execute()
    {
        if(isset($this->args[1])) {
            $this->hour = str_pad($this->args[1], 2, 0, STR_PAD_LEFT);
        }
        $model = new AppModel();
        $charge = new AppModel();
        $date_min = $date_max = NULL;
        $this->_setInit($charge, $model, $date_min, $date_max);

        // tìm toàn bộ distributor + distributor_channel
        $list_distributor = $this->Distributor->find('all', array('conditions' => array(
                'status' => Configure::read('sysconfig.App.constants.STATUS_APPROVED')
        )));

//        if (self::CHOICE_TYPE) {
            foreach ($list_distributor AS $distributor) {
                $list_distributor_channel = $this->DistributionChannel->find('all', array('conditions' => array(
                        'distributor_code' => $distributor['Distributor']['code'],
                        'status' => Configure::read('sysconfig.App.constants.STATUS_APPROVED')
                )));
                // tạo vòng lặp tính toán
                foreach ($list_distributor_channel AS $distributor_channel) {
                    $this->autoProcess($model, $charge, $date_min, $date_max, $distributor['Distributor'], $distributor_channel['DistributionChannel']);
                }
            }
//        } else {
//            foreach ($list_distributor AS $distributor) {
//                $list_distributor_channel = $this->DistributionChannel->find('all', array('conditions' => array(
//                        'distributor_code' => $distributor['Distributor']['code'],
//                        'status' => Configure::read('sysconfig.App.constants.STATUS_APPROVED')
//                )));
//                foreach ($list_distributor_channel AS $distributionChannel) {
//                    foreach ($this->arrayDefaultPackage AS $package) {
//                        foreach ($this->arrayDefaultChannel AS $channel) {
//                            $this->autoProcessNew($model, $charge, $distributionChannel['DistributionChannel'], $distributor['Distributor'], $date_min, $date_max, $package, $channel);
//                        }
//                    }
//                    // cộng dồn vào dữ liệu cũ
//                    $this->out('Push ' . count($this->big_data) . ' record from collection: ' . $model->useTable);
//                    $this->out('Distributor: ' . $distributor['Distributor']['code'] . ' DistributionChannel: ' . $distributionChannel['DistributionChannel']['code']);
//                    $this->__standardData();
//                    $this->_pushData($distributor['Distributor'], $distributionChannel['DistributionChannel']);
//                    $this->out('Done');
//                    // remove old data
//                    $this->big_data = array();
//                }
//            }
//        }
    }

    protected function _countPlayer($distributor, $distributor_channel)
    {
        foreach ($this->list_key AS $k => $v) {
            if (isset($this->mapping_field[$v['package']])) {
                $this->big_data[$k]['total_player_play'] = $this->Player->find('count', array(
                    'conditions' => array(
                        $this->mapping_field[$v['package']] . '.status' => self::STATUS_SUCCESS,
                        'channel_play' => $v['channel'],
                        'distributor_code' => $distributor['code'],
                        'distribution_channel_code' => $distributor_channel['code'],
                    )
                ));
                $this->big_data[$k]['total_player_play_active'] = $this->Player->find('count', array(
                    'conditions' => array(
                        'status' => self::STATUS_SUCCESS,
                        $this->mapping_field[$v['package']] . '.status' => self::STATUS_SUCCESS,
                        'channel_play' => $v['channel'],
                        'distributor_code' => $distributor['code'],
                        'distribution_channel_code' => $distributor_channel['code'],
                    )
                ));
            } else {
                $this->big_data[$k]['total_player_play'] = $this->big_data[$k]['total_player_play_active'] = 0;
            }
        }
    }

    /**
     * Không sử dụng
     */
    protected function autoProcessNew($model, $charge, $distributionChannel, $distributor, $date_min, $date_max, $package, $channel)
    {
        $default_var = array();
        $default_var['total_players'] = $this->total_players;
        $default_var['active'] = $this->active_players;
        $default_var['channel'] = $channel;
        $default_var['package'] = $package;
        $default_var['register'] = $this->_countMoDb($model, self::ACTION_REGISTER_SMS, $distributionChannel, $distributor, $date_min, $date_max, 'register', $package, $channel);
        $default_var['register_free'] = $this->_countMoDb($model, self::ACTION_REGISTER_SMS, $distributionChannel, $distributor, $date_min, $date_max, 'register', $package, $channel, '', array('amount' => 0));
        $default_var['self_deactive'] = $this->_countMoDb($model, self::ACTION_UNREGISTER, $distributionChannel, $distributor, $date_min, $date_max, 'self_deactive', $package, $channel);
        $default_var['system_deactive'] = $this->_countMoDb($model, self::ACTION_CMS_UNREGISTER, $distributionChannel, $distributor, $date_min, $date_max, 'system_deactive', $package, $channel);
//        $default_var['deactive'] = $default_var['self_deactive'] + $default_var['system_deactive'];
        $default_var['buy_question'] = $this->_countMoDb($model, self::ACTION_BUY_QUESTION, $distributionChannel, $distributor, $date_min, $date_max, 'buy_question', $package, $channel);
        $default_var['renew_success'] = $this->_countMoDb($model, self::ACTION_RENEW, $distributionChannel, $distributor, $date_min, $date_max, 'renew_success', $package, $channel, self::STATUS_SUCCESS);
        $default_var['renew_fail'] = $this->_countMoDb($model, self::ACTION_RENEW, $distributionChannel, $distributor, $date_min, $date_max, 'renew_fail', $package, $channel, self::STATUS_FAIL);
//        $default_var['renew'] = $default_var['renew_success'] + $default_var['renew_fail'];
        $default_var['register_revenue'] = $this->_countChargeDb($charge, self::ACTION_REGISTER_SMS, $distributionChannel, $distributor, $date_min, $date_max, 'register_revenue', $package, $channel, array('register_revenue' => '$amount'));
        $default_var['buy_question_revenue'] = $this->_countChargeDb($charge, self::ACTION_BUY_QUESTION, $distributionChannel, $distributor, $date_min, $date_max, 'buy_question_revenue', $package, $channel, array('buy_question_revenue' => '$amount'));
        $default_var['renew_revenue'] = $this->_countChargeDb($charge, self::ACTION_RENEW, $distributionChannel, $distributor, $date_min, $date_max, 'renew_revenue', $package, $channel, array('renew_revenue' => '$amount'));
//        $default_var['revenue'] = $default_var['register_revenue'] + $default_var['buy_question_revenue'] + $default_var['renew_revenue'];
        $default_var['distributor_revenue'] = $this->_countChargeDb($charge, '', $distributionChannel, $distributor, $date_min, $date_max, 'distributor_revenue', $package, $channel, array('distributor_revenue' => array('$multiply' => array('$amount', '$distributor_sharing', '$distribution_channel_sharing'))));
//        $default_var['real_revenue'] = $default_var['revenue'] - $default_var['distributor_revenue'];
        $this->big_data[$package . $channel] = $default_var;
    }

    protected function getData($model, $date_min, $date_max, $distributor, $distributionChannel)
    {
        $getFullDataDone = FALSE;
        $page = 0;
        $bigData = array();
        while (!$getFullDataDone) {
            $options = array(
                'limit' => self::LIMIT_RECORD_PER_BLOCK,
                'offset' => $page * self::LIMIT_RECORD_PER_BLOCK,
                'conditions' => array(
                    //'status' => 1, // only get success mo
                    'created' => array(
                        '$gte' => $date_min,
                        '$lte' => $date_max,
                    ),
                    'distributor_code' => $distributor['code'],
                    'distribution_channel_code' => $distributionChannel['code']
                )
            );
            // get data
            $datas = $model->find('all', $options);
            if ($datas) {
                $page++;
                $bigData = array_merge($bigData, $datas);
                sleep(1); // ngủ 1s, giảm tải cho server DB
            } else {
                $getFullDataDone = TRUE;
            }
        }
        return $bigData;
    }

    /**
     * 
     * @param type $model
     * @param type $charge
     * @param type $date_min
     * @param type $date_max
     */
    protected function autoProcess($model, $charge, $date_min, $date_max, $distributor, $distributionChannel)
    {
        $datas = $this->getData($model, $date_min, $date_max, $distributor, $distributionChannel);
        $charges = $this->getData($charge, $date_min, $date_max, $distributor, $distributionChannel);
        // tinh toán lượt đăng ký, gia hạn, tự hủy, hệ thống hủy, mua câu hỏi, gia hạn thành công thất bại
        if (is_array($datas) && count($datas)) {
            foreach ($datas AS $data) {
                $key = '';
                if ($this->__validateRecord($data[$model->name], $key, $distributor, $distributionChannel)) {
                    $this->__calculator($data[$model->name], $key);
                }
            }
        }
        
        // tính toán doanh thu, distributor_revenue, real_revenue, renew_revenue, register_revenue, buy_question_revenue
        if (is_array($charges) && count($charges)) {
            foreach ($charges AS $data) {
                $key = '';
                if ($this->__validateRecord($data[$charge->name], $key, $distributor, $distributionChannel)) {
                    $this->__calculatorFromCharge($data[$charge->name], $key);
                }
            }
        }
        $this->out($model->useTable . ' records: ' . count($datas));
        $this->out($charge->useTable . ' records: ' . count($charges));
        
        if (count($this->big_data)) {
            $this->_countPlayer($distributor, $distributionChannel);
            // cộng dồn vào dữ liệu cũ
            $this->__standardData();
            $this->_pushData($distributor, $distributionChannel);
        } else {
            $this->out('No data, no push db');
        }
        // remove old data
        $this->big_data = array();
    }

    // check record has package & channel
    private function __validateRecord($data, &$key, $distributor, $distributor_channel)
    {
        // kiểm tra nếu bản ghi không có thông số gói và kênh thì bỏ qua
        if (!isset($data['package']) || !strlen($data['package'])) {
            return FALSE;
        }
        if (!isset($data['channel']) || !strlen($data['channel'])) {
            return FALSE;
        }
        $key = $data['package'] . $data['channel'];
        if (FALSE == array_key_exists($key, $this->list_key)) {
            $this->list_key[$key] = array(
                'package' => $data['package'],
                'channel' => $data['channel']
            );
        }
        if (FALSE == array_key_exists($key, $this->big_data)) {
            $this->big_data[$key] = $this->default_var;
            $this->big_data[$key]['channel'] = $data['channel'];
            $this->big_data[$key]['package'] = $data['package'];
            
            $this->big_data[$key]['distributor_code'] = $distributor['code'];
            $this->big_data[$key]['distribution_channel_code'] = $distributor_channel['code'];
                
            $this->big_data[$key]['active'] = $this->active_players ? : 0;
            $this->big_data[$key]['total_players'] = $this->total_players ? : 0;
        }
        return TRUE;
    }

    private function __calculatorFromCharge($data, $key)
    {
        // nếu charge không thành công thì bỏ qua
        if ($data['status'] == self::STATUS_FAIL) {
            return;
        }
        // kiểm tra nếu không có giá trị chia sẻ cho distributor thì bỏ qua
        if (!isset($data['distributor_sharing'], $data['distribution_channel_sharing'])) {
            return;
        }
        if((int) $data['amount'] > 0 && isset($data['phone'])) {
            $this->charge_players[$key][] = $data['phone'];
        }
        $distributor_revenue = $data['amount'] * $data['distributor_sharing'] * $data['distribution_channel_sharing'];
        switch ($data['action']) {
            case self::ACTION_REGISTER_WAP:
            case self::ACTION_REGISTER_SMS:
                $this->big_data[$key]['register_revenue'] += $distributor_revenue;
                $this->big_data[$key]['distributor_revenue'] += $distributor_revenue;
                break;
            case self::ACTION_BUY_QUESTION:
                $this->big_data[$key]['buy_question_revenue'] += $distributor_revenue;
                $this->big_data[$key]['distributor_revenue'] += $distributor_revenue;
                break;
            case self::ACTION_RENEW:
                // nếu gia hạn trước 3h sáng thì tính là gia hạn, ngược lại là truy thu
                if ($data['modified']->sec < strtotime($this->date . ' 03:00:00')) {
                    $this->big_data[$key]['renew_revenue'] += $distributor_revenue;
                    $this->big_data[$key]['distributor_revenue'] += $distributor_revenue;
                } else {
                    $this->big_data[$key]['arrears_revenue'] += $distributor_revenue;
                    $this->big_data[$key]['distributor_revenue'] += $distributor_revenue;
                }
                break;
        }
    }

    private function __calculator($data, $key)
    {
        // status = 0 => break unless renew
        switch ($data['action']) {
            case self::ACTION_REGISTER_WAP:
            case self::ACTION_REGISTER_SMS:
                if($data['status'] != 1) {
                    break;
                }
                $this->big_data[$key]['register'] ++;
                if ($data['amount']) {
                    $this->big_data[$key]['register_charge'] ++;
                } else {
                    $this->big_data[$key]['register_free'] ++;
                }
                $this->player_register[$key][] = $data['phone'];
                break;
            case self::ACTION_RENEW:
                // nếu gia hạn trước 3h sáng thì tính là gia hạn, ngược lại là truy thu
                if ($data['modified']->sec < strtotime($this->date . ' 03:00:00')) {
                    if ($data['status'] == self::STATUS_SUCCESS) {
                        $this->big_data[$key]['renew_success'] ++;
                    } elseif ($data['status'] == self::STATUS_FAIL) {
                        $this->big_data[$key]['renew_fail'] ++;
                    }
                    $this->big_data[$key]['renew'] ++;
                    $this->player_renew[$key][] = $data['phone'];
                } else {
                    if ($data['status'] == self::STATUS_SUCCESS) {
                        $this->big_data[$key]['arrears_success'] ++;
                    } elseif ($data['status'] == self::STATUS_FAIL) {
                        $this->big_data[$key]['arrears_fail'] ++;
                    }
                    $this->big_data[$key]['arrears'] ++;
                    $this->player_arrears[$key][] = $data['phone'];
                }
                break;
            case self::ACTION_UNREGISTER:
                if($data['status'] != 1) {
                    break;
                }
                //$this->big_data[$key]['deactive'] ++;
                $this->big_data[$key]['self_deactive'] ++;
                // nếu thuê bao hủy đã đăng ký trong ngày thì tính vào lượt hủy trong ngày
                if (isset($this->player_register[$key]) && in_array($data['phone'], $this->player_register[$key])) {
                    $this->big_data[$key]['today_deactive'] ++;
                }
                break;
            case self::ACTION_CMS_UNREGISTER:
                if($data['status'] != 1) {
                    break;
                }
                //$this->big_data[$key]['deactive'] ++;
                $this->big_data[$key]['system_deactive'] ++;
                // nếu thuê bao hủy đã đăng ký trong ngày thì tính vào lượt hủy trong ngày
                if (isset($this->player_register[$key]) && in_array($data['phone'], $this->player_register[$key])) {
                    $this->big_data[$key]['today_deactive'] ++;
                }
                break;
            case self::ACTION_BUY_QUESTION:
                if($data['status'] != 1) {
                    break;
                }
                $this->big_data[$key]['buy_question'] ++;
                break;
        }
    }

    private function __standardData()
    {
        // add data into field total
        foreach ($this->big_data AS $k => $datas) {
            if (!isset($datas['channel'])) {
                continue;
            }
            $this->big_data[$k]['revenue'] = $datas['renew_revenue'] + $datas['register_revenue'] + $datas['buy_question_revenue'] + $datas['arrears_revenue'];
            $this->big_data[$k]['real_revenue'] = $this->big_data[$k]['revenue'] - $datas['distributor_revenue'];
            $this->big_data[$k]['renew_count'] = isset($this->player_renew[$k]) ? count(array_unique($this->player_renew[$k])) : 0;
            $this->big_data[$k]['arrears_count'] = isset($this->player_arrears[$k]) ? count(array_unique($this->player_arrears[$k])) : 0;
            $this->big_data[$k]['total_player_play_charge'] = isset($this->charge_players[$k]) ? count(array_unique($this->charge_players[$k])) : 0;
            $this->big_data[$k]['deactive'] = $datas['self_deactive'] + $datas['system_deactive'];
        }
    }

    protected function _pushData($distributor, $distributionChannel)
    {
        $save = array();
        foreach ($this->big_data AS $data) {
            if (!isset($data['channel'])) {
                continue;
            }
            $old_data = $this->DistributorDailyReport->find('first', array('conditions' => array(
                    'date' => new MongoDate(strtotime($this->date)),
                    'channel' => $data['channel'],
                    'package' => $data['package'],
                    'distributor_code' => $distributor['code'],
                    'distribution_channel_code' => $distributionChannel['code']
            )));
            $pre_save = array(
                'date' => new MongoDate(strtotime($this->date)),
                'channel' => $data['channel'],
                'package' => $data['package'],
                'service_code' => 'S01',
                'distributor_code' => $distributor['code'],
                'distribution_channel_code' => $distributionChannel['code'],
                'distributor_sharing' => $distributor['sharing'],
                'distribution_channel_sharing' => $distributor['sharing'],
            );
            if (isset($old_data['DistributorDailyReport']['id'])) {
                $this->DistributorDailyReport->id = new MongoId($old_data['DistributorDailyReport']['id']);
            } else {
                $this->DistributorDailyReport->create();
            }
            $this->__buildDataHourly($old_data, $data, $pre_save);
            $save['DistributorDailyReport'] = array_merge($pre_save, $data);

            // save db
            if ($this->has_error === FALSE && $this->DistributorDailyReport->save($save)) {
                $this->out('Report saved successfull. Channel: ' . $data['channel'] . ', Package: ' . $data['package'] .
                    ' Distributor_code' . $distributor['code'] . ' Distribution_channel_code' . $distributionChannel['code']);
            } else {
                $this->out('Report cant save');
            }
        }
    }

    private function __buildDataHourly($old_data, &$data, &$pre_save)
    {
        // kiểu giờ, đầu tiên sẽ xóa giờ đó, rồi cộng dồn, nếu không có dữ liệu cũ thì tạo mới
        if ($old_data) {
            // kiểm tra nếu có dữ liệu giờ rồi
            if (isset($old_data['DistributorDailyReport']['hourly'][str_pad($this->hour, 2, 0, STR_PAD_LEFT)])) {
                $this->out('DB has data at ' . str_pad($this->hour, 2, 0, STR_PAD_LEFT) . '!');
                $old_hour_data = $old_data['DistributorDailyReport']['hourly'][str_pad($this->hour, 2, 0, STR_PAD_LEFT)];
                // xóa kết quả đã cộng dồn ở ngoài, chỉ xóa kết quả kiểu đếm lượt
                foreach ($old_data['DistributorDailyReport'] AS $key => $value) {
                    if (array_key_exists($key, $this->default_var_count) && array_key_exists($key, $old_hour_data)) {
                        $old_data['DistributorDailyReport'][$key] = $value - $old_hour_data[$key]; // dangerous
                    }
                }
            }
            $old_data['DistributorDailyReport']['hourly'][str_pad($this->hour, 2, 0, STR_PAD_LEFT)] = $data;
            // cộng dồn kết quả
            foreach ($old_data['DistributorDailyReport'] AS $key => $value) {
                // kiểm đếm lượt thì cộng dồn
                if (array_key_exists($key, $this->default_var_count)) {
                    $old_data['DistributorDailyReport'][$key] = $value + $data[$key];
                }
                // các chỉ số lấy từ bảng player thì thay mới
                if (array_key_exists($key, $this->default_var_player)) {
                    $old_data['DistributorDailyReport'][$key] = $data[$key];
                }
                // các chỉ số lấy từ bảng đếm thì chỉ lấy max
                if (array_key_exists($key, $this->default_var_count_player)) {
                    $old_data['DistributorDailyReport'][$key] = max(array($old_data['DistributorDailyReport'][$key], $data[$key]));
                }
            }
            $data = $old_data['DistributorDailyReport'];
            if (self::DEBUG) {
                $hour = str_pad($this->hour, 2, 0, STR_PAD_LEFT);
                $this->out('Data of hour: ' . $hour);
                pr($data['hourly'][$hour]);
                $this->out('Number mo record of hour: ' . $hour . ': ' . $this->tracking_record['mo'][$hour]);
                $this->out('Number charge record of hour: ' . $hour . ': ' . $this->tracking_record['charge'][$hour]);
            }
        } else {
            $pre_save['hourly'][str_pad($this->hour, 2, 0, STR_PAD_LEFT)] = $data; // save lần đầu tiên vào 0h
            if (self::DEBUG) {
                $hour = str_pad($this->hour, 2, 0, STR_PAD_LEFT);
                $this->out('Data of hour: ' . $hour);
                pr($data);
                $this->out('Number mo record of hour: ' . $hour . ': ' . $this->tracking_record['mo'][$hour]);
                $this->out('Number charge record of hour: ' . $hour . ': ' . $this->tracking_record['charge'][$hour]);
            }
        }
    }
//    /**
//     * Tinh toan trong bang charge
//     */
//    protected function _countChargeDb($model, $action, $channelCode, $distributorCode, $date_min, $date_max, $field, $package, $channel, $expression, $status = '') {
//        $options = array();
//        $options['conditions']['aggregate'][] = array(
//            '$project' => array_merge($expression, array(
////                $field => '$amount',
//                'modified' => '$modified',
//                'package' => '$package',
//                'channel' => '$channel',
//                'status' => '$status',
//                'action' => '$action',
//                'distributor_code' => '$distributor_code',
//                'distribution_channel_code' => '$distribution_channel_code',
//            ))
//        );
//        $matchOption = array(
//            '$match' => array(
//                'modified' => array(
//                    '$gte' => $date_min,
//                    '$lte' => $date_max,
//                ),
//                'package' => $package,
//                'channel' => $channel,
//                'distributor_code' => $distributorCode['code'],
//                'distribution_channel_code' => $channelCode['code']
//            ),
//        );
//        if ($action) {
//            $matchOption['$match']['action'] = $action;
//        }
//        if ($status) {
//            $matchOption['$match']['status'] = $status;
//        }
//        $options['conditions']['aggregate'][] = $matchOption;
//        $options['conditions']['aggregate'][] = array(
//            '$group' => array(
//                '_id' => '$distribution_channel_code',
//                $field => array(
//                    '$sum' => '$' . $field,
//                ),
//            ),
//        );
//        $results = $model->find('first', $options);
//        return $results ? $results[$model->alias][$field] : 0;
//    }
//
//    /**
//     * Tinh toan trong bang mo
//     */
//    protected function _countMoDb($model, $action, $channelCode, $distributorCode, $date_min, $date_max, $field, $package, $channel, $status = '', $option = array()) {
//        $options = array();
//        $options['conditions']['aggregate'][] = array(
//            '$project' => array(
//                $field => '$' . $field,
//                'modified' => '$modified',
//                'package' => '$package',
//                'channel' => '$channel',
//                'distributor_code' => '$distributor_code',
//                'action' => '$action',
//                'status' => '$status',
//                'distribution_channel_code' => '$distribution_channel_code'
//            )
//        );
//        $matchOption = array(
//            '$match' => array(
//                'modified' => array(
//                    '$gte' => $date_min,
//                    '$lte' => $date_max,
//                ),
//                'package' => $package,
//                'channel' => $channel,
//                'action' => $action,
//                'distributor_code' => $distributorCode['code'],
//                'distribution_channel_code' => $channelCode['code']
//            )
//        );
//        if ($status) {
//            $matchOption['$match']['status'] = $status;
//        }
//        $matchOption['$match'] = array_merge($matchOption['$match'], $option);
//        $options['conditions']['aggregate'][] = $matchOption;
//        $options['conditions']['aggregate'][] = array(
//            '$group' => array(
//                '_id' => '$distribution_channel_code',
//                'count' => array('$sum' => 1)
//            ),
//        );
//        $results = $model->find('first', $options);
//        return $results ? $results[$model->alias]['count'] : 0;
//    }
}
