<?php

class ReportTask extends Shell
{

    const EMAIL_CONFIG = 'hivietnam';

    public $uses = array('DailyReportEmail');

    public function executeReportDaily($month, $distributor_day, $date_condition, $list_channel)
    {
        $template = Configure::read('sysconfig.GameQuiz.Report.Daily.Email_template');
        $contacts = Configure::read('sysconfig.GameQuiz.Report.Daily.Receiver');
        $subject = '[GameQuiz - HaloVN] Báo Cáo Ngày: ' . date('d-m-Y', strtotime($date_condition));

        $revenueThisMonth = $this->accumulatedRevenue(
            new MongoDate(strtotime(date('01-m-Y 00:00:00'))), new MongoDate(strtotime(date('d-m-Y 23:59:59')))
        );
        $revenueLastMonth = $this->accumulatedRevenue(
            new MongoDate(strtotime(date('d-m-Y', strtotime('first day of last month')) . ' 00:00:00')), new MongoDate(strtotime(date('d-m-Y', strtotime('last day of last month')) . ' 23:59:59'))
        );
        $revenueThisYear = $this->accumulatedRevenue(
            new MongoDate(strtotime(date('01-01-Y 00:00:00'))), new MongoDate(strtotime(date('d-m-Y 23:59:59')))
        );
        
        $view_var = array(
            'month' => $month,
            'distributor_day' => $distributor_day,
            'revenue_this_month' => $revenueThisMonth,
            'revenue_last_month' => $revenueLastMonth,
            'revenue_this_year' => $revenueThisYear,
            'phones' => $this->duplicateRegister($date_condition , $list_channel),
            'list_channel' => $list_channel
        );
        $this->sendMail($view_var, $template, $contacts, $subject);
    }

    public function executeReportHourly($datas, $lastweek)
    {
        $template = Configure::read('sysconfig.GameQuiz.Report.Hourly.Email_template');
        $contacts = Configure::read('sysconfig.GameQuiz.Report.Hourly.Receiver');
        $subject = '[GameQuiz - HaloVN] Báo Cáo Giờ: ' . date('d-m-Y H:i:s');
        $view_var = array(
            'data' => $datas,
            'lastweek' => $lastweek
        );
        $this->sendMail($view_var, $template, $contacts, $subject);
    }

    protected function sendMail($view_var, $template, $contacts, $subject)
    {
        // setup
        App::uses('CakeEmail', 'Network/Email');
        $Email = new CakeEmail();
        $Email->config(self::EMAIL_CONFIG);
        $Email->template($template);
        $Email->emailFormat('html')->subject($subject)->to($contacts);
        $Email->viewVars($view_var);
        $status = 1;
        $message = $message_variables = '';
        $EmailConfig = new EmailConfig();

        // send mail
        try {
            if ($Email->send()) {
                $this->out('Email was be sent! SUCCESS');
            } else {
                $this->out('Email wasnt be sent! FAIL');
            }
        } catch (Exception $ex) {
            $status = 0;
            $message = $ex->getMessage();
            $message_variables = $ex->getTraceAsString();
        }

        // save result to db
        $this->DailyReportEmail->save(array(
            'to' => $contacts,
            'from' => $EmailConfig->hivietnam['username'],
            'action' => 'DAILY',
            'subject' => $subject,
            'detail' => '', // need confirm
            'status' => $status,
            'message' => $message,
            'message_variables' => $message_variables,
        ));
    }

    public function accumulatedRevenue($start_date, $end_date)
    {
        $options = array();
        $options['conditions']['aggregate'][] = array(
            '$match' => array(
                'date' => array(
                    '$gte' => $start_date,
                    '$lte' => $end_date,
                )
            )
        );
        $options['conditions']['aggregate'][] = array(
            '$group' => array(
                '_id' => NULL,
                'revenue' => array(
                    '$sum' => '$revenue',
                ),
                'real_revenue' => array(
                    '$sum' => '$real_revenue',
                ),
                'distributor_revenue' => array(
                    '$sum' => '$distributor_revenue',
                ),
            ),
        );
        $this->loadModel('DailyReport');
        $results = $this->DailyReport->find('first', $options);
        return isset($results['DailyReport']) ? $results['DailyReport'] : '';
    }

    public function duplicateRegister($date_condition, $list_channel)
    {
        // $date_condition chưa làm j
        $options = array();
        $options['conditions']['aggregate'][] = array(
            '$match' => array(
                'status' => 1,
                'distribution_channel_code' => array('$in' => array_keys($list_channel))
            )
        );
        $options['conditions']['aggregate'][] = array(
            '$group' => array(
                '_id' => array(
                    'phone' => '$phone',
                    'distribution_channel_code' => '$distribution_channel_code'
                ),
                'count' => array(
                    '$sum' => 1
                ),
            ),
        );
        $model = new AppModel(false, 'mo_dks');
        $results = $model->find('all', $options);
        $return = array();
        if ($results) {
            foreach ($results AS $item) {
                $return[] = array(
                    'phone' => $item['AppModel']['_id']['phone'],
                    'channel' => $item['AppModel']['_id']['distribution_channel_code'],
                    'count' => $item['AppModel']['count']
                );
            }
        }
        return $return;
    }
}
