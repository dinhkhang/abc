<?php
App::uses('Shell', 'Console');
App::uses('AppShell', 'Console/Command');

class ReportDailyAccessShell extends AppShell
{
    const LIMIT_RECORD = 10000;
    const SERVICE_CODE = 'HALO';
    const DEBUG = false;

    public $uses = array('TrackingKpiAccess');
    
    private $hour;
    private $nice_hour;
    private $date;
    private $access_hourly_count;
    private $subscriber_hourly;
    private $subscriber_hourly_count;
    private $channel_list = array('WEB','WAP','APP');

    private $tracking_data;

    private $default_data = array(
        'access_count'  =>  0,
        'subcriber_count'  =>  0,
    );

    private $default_hourly_data = array(
        'access_count'  =>  0,
        'subcriber_count'  =>  0,
    );

    public function main(){
        $this->hour = date('H');
        $this->nice_hour = $this->format_hour($this->hour);
        $this->execute_report();
    }

    protected function execute_report(){
        $tracking_model = new AppModel();
        $start_time = null;
        $end_time = null;

        $this->setInit(&$tracking_model, &$kpi_access_model, &$start_time, &$end_time);

        // $datas: tong so tracking_access trong 1h, bao gom ca 3 kenh
        $datas = $this->get_data($tracking_model, $start_time, $end_time);

        // tracking total record hourly
        $this->access_hourly_count = count($datas);

        // tinh toán lượt đăng ký, gia hạn, tự hủy, hệ thống hủy, mua câu hỏi, gia hạn thành công thất bại
        if (is_array($datas) && count($datas)) {

            $this->subscriber_hourly = array();
            $this->subscriber_hourly_count = 0;

            $subscribers = array();
            foreach ($datas as $data) {

                $key = '';
                if ( $this->record_is_valid( $data[$tracking_model->name], $key ) ) {
                    $this->process_data( $data[$tracking_model->name], $key );
                }

            }

            // $this->save_data()

            $this->subscriber_hourly_count = count($subscribers);

        }
    }

    protected function get_data($model, $start_time, $end_time)
    {
        $get_done = false;
        $page = 0;
        $return_data = array();
        while (!$get_done) {
            $options = array(
                'limit' => self::LIMIT_RECORD,
                'offset' => $page * self::LIMIT_RECORD,
                'conditions' => array(
                    'created' => array(
                        '$gte' => $start_time,
                        '$lte' => $end_time,
                    )
                )
            );

            $datas = $model->find('all', $options);
            if ($datas) {
                $page++;
                $return_data = array_merge($return_data, $datas);
                sleep(1);
            } else {
                $get_done = TRUE;
            }
        }
        return $return_data;
    }

    protected function process_data( $data, $key )
    {
        tracking_access_2015_07_04: [{
            _id: < ObjectId_tracing_access_id > ,
            host: "127.0.0.1",
            client_ip: "222.222.222",
            screen_code: "home",
            path: "/apache_pb.gif",
            referrer: "http://www.example.com/start.html",
            user_agent: "Mozilla/4.08 [en] (Win98; I ;Nav)",
            os_name: "Android",
            os_version: "5.1",
            payload: "",
            visitor_username: 'xxxxxxxxxxxxxxx',
            mobile: '849xxxxxxxx',
            token: "xxxxxxxxxxxxxxxxxxxxx",
            channel: "WAP", // WEB, WAP, APP
            action: "OTHER", // mặc định là OTHER, LOGIN, ...
            service_code: "GAMEQUIZ",
            distributor_code: 'D1',
            distribution_channel_code: "DC1",
            created: "2015-01-01 00:00:00", // thời điểm tạo
            modified: "2015-01-01 00:00:00", // thời điểm chỉnh sửa
        }]
        $this->access_hourly_count ++;
        $this->tracking_data[$key] ++;
        $subscriber = $data['mobile'];
        if( !empty( $subscriber ) ){
            if( !in_array($subscriber, $this->subscribers) ){
                //array_push($this->subscribers, $subscriber);
                $this->subscriber_hourly_count ++;
            }
        }

        $this->tracking_data = array();
        $channel = $data['channel'];
    }

    protected function save_data(){
        if($this->tracking_data){

            foreach ($this->tracking_data as $data) {
                //lấy dữ liệu cũ
                $option_old_data = array(
                    'conditions'    =>  array(
                        'date' => date('Ymd'),
                        'channel' => $data['channel']
                    )
                );
                $old_data = $this->TrackingKpiAccess->find('first', $option_old_data);

                $pre_save = array(
                    'date' => date('Ymd'),
                );


                if(!empty($old_data)){
                    $this->TrackingKpiAccess->id = new MongoId($old_data['TrackingKpiAccess']['id']);
                }else{
                    $this->TrackingKpiAccess->create();
                }

                $this->build_hourly_data($old_data, $data, $pre_save);

            }

        }

        private function build_hourly_data($old_data, &$data, &$pre_save){
            // kiểu giờ, đầu tiên sẽ xóa giờ đó, rồi cộng dồn, nếu không có dữ liệu cũ thì tạo mới
            if ($old_data) {
                // kiểm tra nếu có dữ liệu giờ rồi
                if (isset($old_data['TrackingKpiAccess']['hourly'][$this->nice_hour])) {

                    $old_hour_data = $old_data['TrackingKpiAccess']['hourly'][$this->nice_hour];

                    // xóa kết quả đã cộng dồn ở ngoài, chỉ xóa kết quả kiểu đếm lượt
                    $new_hour_data = array(
                        'access_count' => $this->access_hourly_count,
                        'subscriber_count' => $this->subscriber_hourly_count,
                    );

                    $old_data['TrackingKpiAccess']['access_count'] -= $old_hour_data['access_count'];
                    $old_data['TrackingKpiAccess']['access_count'] += $new_hour_data['access_count'];

                    $old_data['TrackingKpiAccess']['subscriber_count'] -= $old_hour_data['subscriber_count'];
                    $old_data['TrackingKpiAccess']['subscriber_count'] += $new_hour_data['subscriber_count'];
                    
                }

                $data = $old_data['TrackingKpiAccess'];

            } else {
                $pre_save['hourly'][$this->nice_hour] = array(
                    'access_count' => $this->access_hourly_count;
                    'subscriber_count' => $this->subscriber_hourly_count;
                ); // save lần đầu tiên vào 0h
                
            }
        }
    }

    /** 
    * Kiểm tra xem bản ghi có thông tin channel hay không. nếu không có thì bỏ qua
    * $key: trả về 1 trong các giá trị: WAP, WEB, APP
    * Khởi tạo dữ liệu lưu vào daily_kpi_accesses tương ứng với các channel
    */
    private function record_is_valid($data, &$key)
    {
        if ( !isset($data['channel']) || !empty($data['channel']) ) {
            return false;
        }

        if(!in_array($data['channel'], $this->channel_list)){
            return false;
        }

        $key = $data['channel']; //WAP WEB APP

        if ( array_key_exists($key, $this->tracking_data) == false ) {
            //$this->tracking_data[$key] = $this->default_var;
            $this->tracking_data[$key]['channel'] = $data['channel'];
            $this->tracking_data[$key]['service_code'] = self::SERVICE_CODE;
        }
        return true;
    }

    protected function format_hour()
    {
        return str_pad($this->hour, 2, 0, STR_PAD_LEFT);
    }

    protected function setInit(&$tracking_model, &$start_time, &$end_time)
    {
        // lấy thông tin từ bảng ngày và bảng tracking_accesses
        $tracking_model->useTable = 'tracking_accesses_' . date('Y_m_d');
        // for get data hourly
        $start_time = new MongoDate(strtotime(date('d-m-Y ' . $this->hour . ':00:00')));
        $end_time = new MongoDate(strtotime(date('d-m-Y ' . $this->hour . ':59:59')));
        
        //$this->date = date('d-m-Y');
        $this->out('From: ' . date('d-m-Y H:i:s', $start_time->sec) . ' to: ' . date('d-m-Y H:i:s', $end_time->sec));
        //$this->active_players = $this->Player->find('count', array('conditions' => array('status' => self::STATUS_SUCCESS)));
        //$this->total_players = $this->Player->find('count');
    }

    public function hourly()
    {
        // kiểm tra giá trị đầu vào
        $today_condition = isset($this->args[0]) ? $this->args[0] : date("d-m-Y");
        $last_week_condition = date('d-m-Y', strtotime('-7 days', strtotime($today_condition)));
        $this->out('To date: ' . $today_condition);
        $this->out('Last 7 days: ' . $last_week_condition);
        $today = $this->DailyReport->find('all', array('conditions' => array(
                'date' => array(
                    '$gte' => new MongoDate(strtotime($today_condition . ' 00:00:00')),
                    '$lte' => new MongoDate(strtotime($today_condition . ' 23:59:59')),
                )
        )));
        $lastweek = $this->DailyReport->find('all', array('conditions' => array(
                'date' => array(
                    '$gte' => new MongoDate(strtotime($last_week_condition . ' 00:00:00')),
                    '$lte' => new MongoDate(strtotime($last_week_condition . ' 23:59:59')),
                )
        )));
        if($lastweek) {
            foreach ($lastweek AS $k => $date) {
                $lastweek[$date['DailyReport']['package']][$date['DailyReport']['channel']] = $date['DailyReport'];
                unset($lastweek[$k]);
            }
        }
        if ($today) {
            foreach ($today AS $k => $date) {
                $today[$date['DailyReport']['package']][$date['DailyReport']['channel']] = $date['DailyReport'];
                unset($today[$k]);
            }
            $this->Report->executeReportHourly($today, $lastweek);
        } else {
            $this->out('An Error Has Occurred, Got No Record From DB.');
        }
        $this->teardown();
    }

    public function daily()
    {
        // kiểm tra giá trị đầu vào
        $date_condition = isset($this->args[0]) ? $this->args[0] : date("d-m-Y");
        $month_condition = date('m', strtotime($date_condition));
        $month = $this->DailyReport->find('all', array('conditions' => array(
                'date' => array(
                    '$gte' => new MongoDate(strtotime(date("1-{$month_condition}-Y") . ' 00:00:00')),
                    '$lte' => new MongoDate(strtotime(date("t-{$month_condition}-Y") . ' 23:59:59')),
                )
        )));
        $distributor_day = $this->DistributorDailyReport->find('all', array('conditions' => array(
                'date' => array(
                    '$gte' => new MongoDate(strtotime($date_condition . ' 00:00:00')),
                    '$lte' => new MongoDate(strtotime($date_condition . ' 23:59:59')),
                )
        )));
        if($month) {
            $list_channel = $this->_getDistributorList();
            foreach ($month AS $k => $date) {
                $day_report = date('d/m/Y', $date['DailyReport']['date']->sec);
                $month[$day_report][$date['DailyReport']['package']][$date['DailyReport']['channel']] = $date['DailyReport'];
                unset($month[$k]);
            }
            foreach ($distributor_day AS $k => $date) {
                $channel_name = $list_channel[$date['DistributorDailyReport']['distribution_channel_code']];
                $distributor_day[$channel_name][$date['DistributorDailyReport']['package']][$date['DistributorDailyReport']['channel']] = $date['DistributorDailyReport'];
                unset($distributor_day[$k]);
            }
            $this->Report->executeReportDaily($month, $distributor_day, $date_condition, $list_channel);
        } else {
            $this->out('An Error Has Occurred, Got No Record From DB.');
        }
        $this->teardown();
    }
    
}
