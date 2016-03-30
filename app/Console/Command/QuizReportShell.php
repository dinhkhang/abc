<?php

/**
 * Chú ý mở comment hàm sleep(59)
 */
class QuizReportShell extends AppShell
{

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

    public $uses = array('DailyReport', 'Player');
    public $has_error = FALSE;
    public $big_data = array();
    private $list_key = array();
    private $mapping_field = array('G1' => 'package_day', 'G7' => 'package_week');
    private $date; // type date, eg: d-m-Y
    private $hour; // giờ xử lý
    private $type = 1; // daily : 0 or hourly : 1 (default)
    private $charge_players = array(); // nhóm thuê bao có phát sinh cước
    private $active_players = 0; // nhóm thuê bao kích hoạt
    private $total_players = 0; // tổng thuê bao
    private $tracking_record = ''; // kiểm soát tổng số bản ghi để so sánh vs db thật để debug
    private $player_register = array(); // lưu thuê bao đăng ký
    private $player_arrears = array(); // lưu thuê bao truy thu
    private $player_renew = array(); // lưu thuê bao gia hạn
    public $default_var = array(// giá trị mặc định
        'total_players' => 0,
        'active' => 0,
        'register' => 0,
        'register_free' => 0,
        'register_charge' => 0,
        'deactive' => 0,
        'self_deactive' => 0,
        'system_deactive' => 0,
        'today_deactive' => 0,
        'buy_question' => 0,
        'arrears' => 0, // đếm lượt truy thu
        'arrears_count' => 0, // đếm thuê bao truy thu
        'arrears_success' => 0,
        'arrears_fail' => 0,
        'renew' => 0, // đếm lượt gia hạn
        'renew_count' => 0, // đếm thuê bao gia hạn
        'renew_success' => 0,
        'renew_fail' => 0,
        'revenue' => 0,
        'real_revenue' => 0,
        'distributor_revenue' => 0,
        'renew_revenue' => 0,
        'arrears_revenue' => 0,
        'register_revenue' => 0,
        'buy_question_revenue' => 0,
        'total_player_play' => 0,
        'total_player_play_charge' => 0,
        'total_player_play_active' => 0,
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

    /**
     * report hourly: Console\cake QuizReport [01-10-2015 12]
     * report daily: Console\cake QuizReport Daily [01-10-2015]
     */
    public function main()
    {
        $this->out('Start: ' . date('d-m-Y H:i:s'));
        $this->hour = date('H');
        // kiểm tra kiểu ngày hay kiểu giờ
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
			sleep(58);
			$this->out('Sleeping after 59s: ' . date('d-m-Y H:i:s'));
            $this->_execute();
            $this->out('Type hourly. Hour: ' . $this->hour . ' - Date: ' . $this->date);
        }
        $this->big_data = array();
        /*
          // for test
          for ($j = 1; $j <= 3; $j++) {
          for ($i = 0; $i <= 23; $i++) {
          $this->args[0] = str_pad($j, 2, 0, STR_PAD_LEFT) . '-11-2015';
          $this->args[1] = str_pad($i, 2, 0, STR_PAD_LEFT);
          $this->_execute();
          // xóa dữ liệu big_data
          $this->big_data = array();

          if(self::DEBUG) {
          // thông báo tổng số bản ghi
          $this->out('Total record checked in mo table: ' . array_sum($this->tracking_record['mo']));
          foreach($this->tracking_record['mo'] AS $hour => $num) {
          $this->out($hour . ': ' . $num);
          }
          $this->out('Total record checked in mo table: ' . array_sum($this->tracking_record['charge']));
          foreach($this->tracking_record['charge'] AS $hour => $num) {
          $this->out($hour . ': ' . $num);
          }
          }
          }
          }
         */
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

    protected function _execute()
    {
        if(isset($this->args[1])) {
            $this->hour = str_pad($this->args[1], 2, 0, STR_PAD_LEFT);
        }
        $model = new AppModel();
        $charge = new AppModel();
        $date_min = $date_max = NULL;
        $this->_setInit($charge, $model, $date_min, $date_max);

        $datas = $this->_getData($model, $date_min, $date_max);
        $charges = $this->_getData($charge, $date_min, $date_max);
        // tracking total record hourly
        $this->tracking_record['mo'][str_pad($this->hour, 2, 0, STR_PAD_LEFT)] = count($datas);
        $this->tracking_record['charge'][str_pad($this->hour, 2, 0, STR_PAD_LEFT)] = count($charges);

        // tinh toán lượt đăng ký, gia hạn, tự hủy, hệ thống hủy, mua câu hỏi, gia hạn thành công thất bại
        if (is_array($datas) && count($datas)) {
            foreach ($datas AS $data) {
                $key = '';
                if ($this->__validateRecord($data[$model->name], $key)) {
                    $this->__calculator($data[$model->name], $key);
                }
            }
        }

        // tính toán doanh thu, distributor_revenue, real_revenue, renew_revenue, register_revenue, buy_question_revenue
        if (is_array($charges) && count($charges)) {
            foreach ($charges AS $data) {
                $key = '';
                if ($this->__validateRecord($data[$charge->name], $key)) {
                    $this->__calculatorFromCharge($data[$charge->name], $key);
                }
            }
        }
        $this->out($model->useTable . ' records: ' . count($datas));
        $this->out($charge->useTable . ' records: ' . count($charges));
        
        if (count($this->big_data)) {
            // dếm số thuê bao theo gói và kênh hiện tại, được xử lý sau cùng
            $this->_countPlayer();
            // cộng dồn vào dữ liệu cũ
            $this->__standardData(); // chuẩn hóa dữ liệu mặc định
            $this->_pushData(); // nhồi data
        } else {
            $this->out('No data, no push db');
        }
    }

    protected function _countPlayer()
    {
        foreach ($this->list_key AS $k => $v) {
            if (isset($this->mapping_field[$v['package']])) {
                $this->big_data[$k]['total_player_play'] = $this->Player->find('count', array(
                    'conditions' => array(
                        $this->mapping_field[$v['package']] . '.status' => self::STATUS_SUCCESS,
                        'channel_play' => $v['channel'],
                    )
                ));
                $this->big_data[$k]['total_player_play_active'] = $this->Player->find('count', array(
                    'conditions' => array(
                        'status' => self::STATUS_SUCCESS,
                        $this->mapping_field[$v['package']] . '.status' => self::STATUS_SUCCESS,
                        'channel_play' => $v['channel'],
                    )
                ));
            } else {
                $this->big_data[$k]['total_player_play'] = $this->big_data[$k]['total_player_play_active'] = 0;
            }
        }
    }

    protected function _getData($model, $date_min, $date_max)
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
                    )
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

    // check record has package & channel
    private function __validateRecord($data, &$key)
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
                $this->big_data[$key]['register_revenue'] += $data['amount'];
                $this->big_data[$key]['distributor_revenue'] += $distributor_revenue;
                break;
            case self::ACTION_BUY_QUESTION:
                $this->big_data[$key]['buy_question_revenue'] += $data['amount'];
                $this->big_data[$key]['distributor_revenue'] += $distributor_revenue;
                break;
            case self::ACTION_RENEW:
                // nếu gia hạn trước 3h sáng thì tính là gia hạn, ngược lại là truy thu
                if ($data['modified']->sec < strtotime($this->date . ' 03:00:00')) {
                    $this->big_data[$key]['renew_revenue'] += $data['amount'];
                    $this->big_data[$key]['distributor_revenue'] += $distributor_revenue;
                } else {
                    $this->big_data[$key]['arrears_revenue'] += $data['amount'];
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

    protected function _pushData()
    {
        $save = array();
        foreach ($this->big_data AS $data) {
            if (!isset($data['channel'])) {
                continue;
            }
            // lấy thông tin dữ liệu cũ
            $old_data = $this->DailyReport->find('first', array('conditions' => array(
                    'date' => new MongoDate(strtotime($this->date)),
                    'channel' => $data['channel'],
                    'package' => $data['package']
            )));
            $pre_save = array(
                'date' => new MongoDate(strtotime($this->date)),
                'channel' => $data['channel'],
                'package' => $data['package'],
                'service_code' => 'S01'
            );
            if (isset($old_data['DailyReport']['id'])) {
                $this->DailyReport->id = new MongoId($old_data['DailyReport']['id']);
            } else {
                $this->DailyReport->create();
            }
            $this->__buildDataHourly($old_data, $data, $pre_save);
            $save['DailyReport'] = array_merge($pre_save, $data);

            // save db
            if ($this->has_error === FALSE && $this->DailyReport->save($save)) {
                $this->out('Report saved successfull. '
                    . 'Hour: ' . str_pad($this->hour, 2, 0, STR_PAD_LEFT) . ', '
                    . 'Channel: ' . $data['channel'] . ', '
                    . 'Package: ' . $data['package']);
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
            if (isset($old_data['DailyReport']['hourly'][str_pad($this->hour, 2, 0, STR_PAD_LEFT)])) {
                $this->out('DB has data at ' . str_pad($this->hour, 2, 0, STR_PAD_LEFT) . '!');
                $old_hour_data = $old_data['DailyReport']['hourly'][str_pad($this->hour, 2, 0, STR_PAD_LEFT)];
                // xóa kết quả đã cộng dồn ở ngoài, chỉ xóa kết quả kiểu đếm lượt
                foreach ($old_data['DailyReport'] AS $key => $value) {
                    if (array_key_exists($key, $this->default_var_count) && array_key_exists($key, $old_hour_data)) {
                        $old_data['DailyReport'][$key] = $value - $old_hour_data[$key]; // dangerous
                    }
                }
            }
            $old_data['DailyReport']['hourly'][str_pad($this->hour, 2, 0, STR_PAD_LEFT)] = $data;
            // cộng dồn kết quả
            foreach ($old_data['DailyReport'] AS $key => $value) {
                // kiểm đếm lượt thì cộng dồn
                if (array_key_exists($key, $this->default_var_count)) {
                    $old_data['DailyReport'][$key] = $value + $data[$key];
                }
                // các chỉ số lấy từ bảng player thì thay mới
                if (array_key_exists($key, $this->default_var_player)) {
                    $old_data['DailyReport'][$key] = $data[$key];
                }
                // các chỉ số lấy từ bảng đếm thì chỉ lấy max
                if (array_key_exists($key, $this->default_var_count_player)) {
                    $old_data['DailyReport'][$key] = max(array($old_data['DailyReport'][$key], $data[$key]));
                }
            }
            $data = $old_data['DailyReport'];
            if (self::DEBUG) {
                $hour = str_pad($this->hour, 2, 0, STR_PAD_LEFT);
                $this->out('Data of hour: ' . $hour);
//                pr($data['hourly'][$hour]);
                $this->out('Number mo record of hour: ' . $hour . ': ' . $this->tracking_record['mo'][$hour]);
                $this->out('Number charge record of hour: ' . $hour . ': ' . $this->tracking_record['charge'][$hour]);
            }
        } else {
            $pre_save['hourly'][str_pad($this->hour, 2, 0, STR_PAD_LEFT)] = $data; // save lần đầu tiên vào 0h
            if (self::DEBUG) {
                $hour = str_pad($this->hour, 2, 0, STR_PAD_LEFT);
                $this->out('Data of hour: ' . $hour);
//                pr($data);
                $this->out('Number mo record of hour: ' . $hour . ': ' . $this->tracking_record['mo'][$hour]);
                $this->out('Number charge record of hour: ' . $hour . ': ' . $this->tracking_record['charge'][$hour]);
            }
        }
    }
}
