<?php
App::uses('Shell', 'Console');
App::uses('AppShell', 'Console/Command');

class QuizReportMailShell extends AppShell
{

    public $uses = array('DailyReport', 'DistributorDailyReport', 'DistributionChannel');
    public $tasks = array('Report');

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

    public function startup()
    {
        parent::startup();
        $this->out('Start: ' . date('d-m-y H:i:s'));
    }

    public function main()
    {
        $this->teardown();
    }

    public function teardown()
    {
        $this->out('End: ' . date('d-m-y H:i:s'));
    }

    protected function _getDistributorList()
    {
        return $this->DistributionChannel->find('list', array(
                'fields' => array('code', 'name'),
                'conditions' => array('status' => Configure::read('sysconfig.App.constants.STATUS_APPROVED'))
        ));
    }
    
    private function __getDateStartMonth() 
    {
        $thisMonth = date('m');
        return date('d-m-Y');
    }
}
