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
    private $subs_list;
    private $count_access_data;

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

        //$datas: tat ca ban ghi trong tracking_access

        // tinh toán lượt đăng ký, gia hạn, tự hủy, hệ thống hủy, mua câu hỏi, gia hạn thành công thất bại
        if (is_array($datas) && count($datas)) {

            foreach ($datas as $data) {
                //voi moi ban ghi trong datas:
                $key = '';
                /** 
                * Kiểm tra xem bản ghi có thông tin channel hay không. nếu không có thì bỏ qua
                * $key: trả về 1 trong các giá trị: WAP, WEB, APP
                * Khởi tạo dữ liệu lưu vào daily_kpi_accesses tương ứng với các channel
                * khoi tao mang $this->tracking_data 
                * $this->tracking_data[$key]['channel'] = '';
                * $this->tracking_data[$key]['service_code'] = '';
                */
                $this->subs_list = array();
                $this->count_access_data = array();
                if ( $this->record_is_valid( $data[$tracking_model->name], $key ) ) {
                    $this->process_data( $data[$tracking_model->name], $key );
                }

            }

            if (count($this->tracking_data)) {
                // dếm số thuê bao theo gói và kênh hiện tại, được xử lý sau cùng
                //$this->_countPlayer();
                // cộng dồn vào dữ liệu cũ
                $this->save_data(); 
            } else {
                //$this->out('No data, no push db');
            }

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

        if($data['channel'] == $key){
            if( !isset( $this->count_access_data[$key] ) ){
                $this->count_access_data[$key] = 0;
            }
            $this->count_access_data[$key] += 1;

            if( !empty($data['mobile']) ){
                if(!in_array($data['mobile'], $this->subs_list[$key])){
                    array_push($this->subs_list[$key], $data['mobile']);
                }
            }
        }

    }

    protected function save_data(){
        //đến đây tracking_data đã có thể set:
        //$tracking_data['WAP']['channel'] = 'WAP'
        //$tracking_data['WAP']['service_code'] = 'HALO'
        //$tracking_data['WAP']['access_count'] = $this->tracking_data['access_count']
        //$tracking_data['WAP']['subscriber'] = count($this->subs_list['WAP'])
        if($this->tracking_data){

            foreach ($this->tracking_data as $data) {
                //lấy dữ liệu cũ
                // $data. vd:   $tracking_data['WAP']['channel'] = 'WAP'
                //              $tracking_data['WAP']['service_code'] = 'HALO'
                //Tìm bản ghi tương ứng vs channel, ngày trong bảng daily_kpi_tracking
                $option_old_data = array(
                    'conditions'    =>  array(
                        'date' => date('Ymd'),
                        'channel' => $data['channel']
                    )
                );
                $old_data = $this->TrackingKpiAccess->find('first', $option_old_data);

                $today = date('Ymd');
                $pre_save = array(
                    'date' => $today * 1,
                );


                if(!empty($old_data)){
                    $this->TrackingKpiAccess->id = new MongoId($old_data['TrackingKpiAccess']['id']);
                }else{
                    $this->TrackingKpiAccess->create();
                }

                $this->build_data($old_data, $data, $pre_save);

                $save['TrackingKpiAccess'] = array_merge($pre_save, $data);

                // save db
                $this->TrackingKpiAccess->save($save);

            }

        }

        private function build_data($old_data, &$data, &$pre_save){
            $channel = $data['channel'];
            if ($old_data) {

                    $old_data['TrackingKpiAccess']['access_count'] = $this->count_access_data[$channel];

                    $old_data['TrackingKpiAccess']['subscriber_count'] = count($this->subs_list[$channel]);
                    
                $data = $old_data['TrackingKpiAccess'];

            } else {
                $pre_save['access_count'] = $this->count_access_data[$channel];
                $pre_save['subscriber_count'] = count($this->subs_list[$channel]);
                // save lần đầu tiên vào 0h
                
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
        $start_time = new MongoDate(strtotime(date('d-m-Y 00:00:00')));
        $end_time = new MongoDate(strtotime(date('d-m-Y ' . $this->hour . ':59:59')));
          
    }
    
}
