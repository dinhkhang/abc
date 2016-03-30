<?php
App::uses('AppShell', 'Shell');

class CronLotoShell extends AppShell
{
    public $uses = array('LotoAnalytic', 'Region', 'DailyResult');

    public function main()
    {
        $today = (int) date('Ymd');
        $yestrday = (int) date('Ymd', strtotime('-1 day'));

        // get data from DailyResult by today
        $data_results = $this->DailyResult->find('all', array(
            'conditions' => array(
                'date' => $today
            ),
            'fields' => array('lotos', 'region')
        ));

        // get data from LotoAnalytic by yestrday
        $data_results = $this->DailyResult->find('all', array(
            'conditions' => array(
                'date' => $today
            ),
            'fields' => array('lotos', 'region')
        ));

        // loop data and update
        foreach($list_all_result AS $prize) {

        }
    }

    protected function createUp() {

    }

    protected function updateUp() {

    }

    protected function updateDown() {

    }
}
/*
// thực hiện crontab hàng ngày, phân tích thống kê tất cả các số từ 00 - 99
loto_analytics:[{
   _id: < ObjectId_number_analytic_id > ,
   date: 20160105, // kiểu int có dạng Ymd
   region_code: 'mienbac', // vùng miền
   loto: '00',
   luck_count: 100, // số lần về
   luck_first_count: 100, // số lần về số đầu
   luck_last_count: 100, // số lần về số cuối
   gan_span: 13, // biên độ gan cực đại của cặp số
   created: "2015-01-01 00:00:00",
   modified: "2015-01-01 00:00:00",
}]
*/