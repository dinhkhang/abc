<?php

class ReportDailyShell extends AppShell {

    public $uses = array('ReportDailyAccessLogin');
    public $os_name = array(
        'WEB',
        'WAP',
        'ANDROID',
        'IOS',
    );
    public $offset = '-1 hour';

    public function main() {

        App::uses('TrackingLog', 'Model');

        $raw_type = $this->args[0];
        $types = explode(',', $raw_type);
        if (empty($types)) {

            $this->out(__('type arg is invalid'));
            exit();
        }

        $start = !empty($this->args[1]) ? $this->args[1] : date('Y-m-d H:i:s', strtotime($this->offset));
        $end = !empty($this->args[2]) ? $this->args[2] : date('Y-m-d H:i:s', strtotime($this->offset));

        $year = date('Y', strtotime($start));
        $month = date('m', strtotime($start));
        $day = date('d', strtotime($start));
        $date = date('Y-m-d', strtotime($start));
        if ($date != date('Y-m-d', strtotime($end))) {

            $this->out(__('start date %s was not the same end date %s', $start, $end));
            exit();
        }

        $hour_start = (int) date('H', strtotime($start));
        $hour_end = (int) date('H', strtotime($end));
        if ($hour_start > $hour_end) {

            $this->out(__('start hour %s was greater than end hour %s', $start, $end));
            exit();
        }

        foreach ($types as $type) {

            $model_pattern = 'tracking_' . $type . '_%s_%s_%s';
            $model_name = sprintf($model_pattern, $year, $month, $day);

            $TrackingLog = new AppModel(array(
                'table' => $model_name,
            ));

            $defaultArrayHourly = array_map(function() {
                return 0;
            }, range(0, 23));

            $checkExists = $this->ReportDailyAccessLogin->checkExist($date);
            if ($checkExists) {

                $save_data = $checkExists['ReportDailyAccessLogin'];
            } else {

                $save_data = array(
                    'date' => new MongoDate(strtotime($date)),
                    'wap_access_daily' => 0,
                    'web_access_daily' => 0,
                    'android_access_daily' => 0,
                    'ios_access_daily' => 0,
                    'android_login_daily' => 0,
                    'ios_login_daily' => 0,
                    'wap_access_hourly' => $defaultArrayHourly,
                    'web_access_hourly' => $defaultArrayHourly,
                    'android_access_hourly' => $defaultArrayHourly,
                    'ios_access_hourly' => $defaultArrayHourly,
                    'android_login_hourly' => $defaultArrayHourly,
                    'ios_login_hourly' => $defaultArrayHourly
                );

                $this->ReportDailyAccessLogin->create();
            }

            foreach ($this->os_name as $os_name) {

                $field_daily = sprintf('%s_%s_daily', strtolower($os_name), $type);
                for ($i = $hour_start; $i <= $hour_end; $i++) {

                    $field_hourly = sprintf('%s_%s_hourly', strtolower($os_name), $type);
                    $options = array();
                    // chỉ với action = access thì mới lọc theo screen_code=splash
                    if ($type == 'access') {

                        $options['conditions']['screen_code'] = 'splash';
                    } elseif ($type == 'login') {

                        $options['conditions']['screen_code'] = 'login';
                    }
                    $save_data[$field_hourly][$i] = $this->countHourly($TrackingLog, $os_name, $date, $i, $options);
                }
                $save_data[$field_daily] = array_sum($save_data[$field_hourly]);
                $this->out($field_daily . ': ' . $save_data[$field_daily]);
            }

            $this->ReportDailyAccessLogin->save($save_data);
        }
    }

    protected function countHourly($Model, $os_name, $date, $hour, $options = array()) {

        $begin = $date . ' ' . $hour . ':00:00';
        $end = $date . ' ' . $hour . ':59:59';

        $default_options = array(
            'conditions' => array(
                'os_name' => $os_name,
                'created' => array(
                    '$gte' => new MongoDate(strtotime($begin)),
                    '$lte' => new MongoDate(strtotime($end)),
                ),
            ),
            'fields' => 'id',
        );
        $options = Hash::merge($options, $default_options);

        $count = $Model->find('count', $options);
        $this->out('Date: ' . $date . ' | Hour: ' . $hour . ' | Count: ' . $count);
        return $count;
    }

    public function fix() {

        if (empty($this->args[0]) || empty($this->args[1])) {

            $this->out('start_date or end_date is empty');
            exit();
        }

        $date_start = $this->args[0];
        $date_end = $this->args[1];

        $start = new DateTime($date_start);
        $end = new DateTime($date_end);
        $end = $end->modify('+1 day');

        $interval = new DateInterval('P1D');
        $daterange = new DatePeriod($start, $interval, $end);
        if (count($daterange) <= 0) {

            $this->out('start_date > end_date');
            exit();
        }

        foreach ($daterange as $date) {

            $cmd = sprintf('"%sConsole%scake" %s "%s" "%s" "%s" 2>&1', APP, DS, 'ReportDaily', 'access,login', $date->format('Y-m-d') . ' 00:00:00', $date->format('Y-m-d') . ' 23:59:59');
            $output = $return_var = null;
            exec($cmd, $output, $return_var);
            if (!empty($return_var)) {

                $this->out(__('Execute cmd %s was failed', $cmd));
                $this->logAnyFile(__('Execute cmd %s was failed', $cmd), __CLASS__ . '_' . __FUNCTION__);
                $this->logAnyFile($output, __CLASS__ . '_' . __FUNCTION__);
            } else {

                $this->out(__('Execute cmd %s was successful', $cmd));
            }
        }
    }

}
