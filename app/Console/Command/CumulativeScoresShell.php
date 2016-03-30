<?php
/**
 * @todo Cộng dồn điểm vào bảng điểm ngày, tháng, năm, dùng cho game quiz
 * @author phutx <phutx@namviet-corp.vn>
 */
App::uses('AppModel', 'Model');
class CumulativeScoresShell extends AppShell {
    const TABLE_NAME_PREFIX = 'score_';
    
    public function main() {
        // create default array conditions
        $options = array();
        // get today + set option
        $date = $this->__getParamDate($this->args);
        $year = date('Y', strtotime($date));
        // get phone + set option
        $this->__getParamPhone($this->args, $options);
        // prepare data
        $date_model = str_replace('-', '_', $date);
        $this->out('lay du lieu tu bang: ' . self::TABLE_NAME_PREFIX . $date_model);
        $main_model = new AppModel(FALSE, self::TABLE_NAME_PREFIX . $date_model);
        $list_data = $main_model->find('all', $options);
        // decorate data
        $filter_data = $this->__filterData($list_data, $main_model->alias);
        if(!(is_array($filter_data) && count($filter_data) > 0)) {
            $this->out('chua co ban ghi moi trong thoi gian tu: ' . date('d-m-Y H:i:s', $this->start_time)
                    . ' den: ' . date('d-m-Y H:i:s', $this->end_time));
            exit();
        }
        // Cumulative score in day table
        $table_day = self::TABLE_NAME_PREFIX . 'day_' . $year;
        $this->_pushDay($filter_data, $table_day, $options, $date);
        $table_week_month = self::TABLE_NAME_PREFIX . $year;
        $this->_pushWeekMonth($this->__filterDataByWeekMonth($filter_data), $table_week_month, $options, $date);
    }
    
    protected function _getRecordErrorDataDay($table_name, $options, $date) {
        $model = new AppModel(FALSE, $table_name);
        $options['conditions']['day.date'] = array(
            '$eq' => new MongoDate(strtotime($date . ' 00:00:00')),
            '$lte' => new MongoDate(strtotime($date . ' 23:59:59')),
        );
        $object = $model->find('first', $options);
        return $object[$model->alias];
    }
    
    protected function _getRecordErrorDataWeekMonth($table_name, $options, $date) {
        $model = new AppModel(FALSE, $table_name);
        $options['conditions']['week']['index'] = date('W', strtotime($date));
        $options['conditions']['month']['index'] = date('m', strtotime($date));
        $object = $model->find('first', $options);
        return $object[$model->alias];
    }

    private function __getParamPhone($args, &$options) {
        if(isset($args[1]) && strlen($args[1])) {
            $options['conditions']['phone'] = $args[1];
        }
    }
    
    private function __getParamDate($args) {
        // kiểm tra du lieu vao
        if(isset($args[0]) && strlen($args[0])) {
            $today = strtotime($args[0]);
            $date = date('Y-m-d', $today);
            $this->out('cong don trong ngay: ' . $date);
            return $date;
        }
        $this->out('gia tri ngay chua duoc truyen vao. Dung chuong trinh!');
        exit();
    }
    
    private function __filterData($list_data, $model = '') {
        $filter_data = array();
        foreach($list_data AS $data) {
            $date = date('Ymd', $data[$model]['modified']->sec);
            if(isset($filter_data[$data[$model]['phone']][$date]['score'])) {
                $filter_data[$data[$model]['phone']][$date]['score'] += $data[$model]['score'];
                $filter_data[$data[$model]['phone']][$date]['time'] += $data[$model]['question']['time'];
            } else {
                $filter_data[$data[$model]['phone']][$date] = array(
                    'score' => $data[$model]['score'],
                    'time' => $data[$model]['question']['time'],
                    'day' => date('d', $data[$model]['modified']->sec),
                    'week' => date('W', $data[$model]['modified']->sec),
                    'month' => date('m', $data[$model]['modified']->sec),
                );
            }
        }
        return $filter_data;
    }
    
    private function __filterDataByWeekMonth($list_data) {
        $filter_data = array();
        foreach($list_data AS $phone => $datas) {
            foreach($datas AS $date => $data) {
                if(isset($filter_data[$phone]['w'.$data['week']], $filter_data[$phone]['m'.$data['month']])) {
                    $filter_data[$phone]['w'.$data['week']]['score'] += $data['score'];
                    $filter_data[$phone]['w'.$data['week']]['time'] += $data['time'];
                    $filter_data[$phone]['m'.$data['month']]['score'] += $data['score'];
                    $filter_data[$phone]['m'.$data['month']]['time'] += $data['time'];
                } else {
                    $filter_data[$phone]['w'.$data['week']] = array(
                        'index' => $data['week'],
                        'score' => $data['score'],
                        'time' => $data['time']
                    );
                    $filter_data[$phone]['m'.$data['month']] = array(
                        'index' => $data['month'],
                        'score' => $data['score'],
                        'time' => $data['time']
                    );
                }
            }
        }
        return $filter_data;
    }
    
    protected function _pushDay(&$filter_data, $table_name, $options, $date) {
        $this->out('bat dau cong don du lieu vao bang ngay: ' . $table_name);
        $model = new AppModel(FALSE, $table_name);
        foreach($filter_data AS $phone => $datas) {
            foreach($datas AS $date => $data) {
                $old_record = $this->_getRecordErrorDataDay($table_name, $options, $date);
                if($old_record) {
                    $model->id = new MongoId($old_record['id']);
                    $filter_data[$phone][$date]['score'] = (int) $data['score'] - $old_record['score'];
                    $filter_data[$phone][$date]['time'] = (int) $data['time'] - $old_record['time'];
                } else {
                    $model->create();
                }
                $model->save(array(
                    'phone' => $phone,
                    'day' => array(
                        'date' => new MongoDate(strtotime($date)),
                        'score' => $data['score'],
                        'time' => $data['time']
                    ),
                    'comment' => '',
                    'service_code' => 'S01',
                ));
            }
        }
        $this->out('hoan thanh cong du lieu vao bang ngay!');
    }
    
    protected function _pushWeekMonth($filter_data, $table_name) {
        $this->out('bat dau cong don du lieu vao bang tuan, thang: ' . $table_name);
        $model = new AppModel(FALSE, $table_name);
        
        $list_phone = array_keys($filter_data);
        $list_data = $model->find('all', array('conditions' => array(
            'phone' => array('$in' => $list_phone),
        )));
        foreach($list_data AS $data) {
            $phone = $data[$model->alias]['phone'];
            if(isset($filter_data[$phone['w'.$data[$model->alias]['week']['index']]], 
                    $filter_data[$phone['m'.$data[$model->alias]['month']['index']]])) {
                $data[$model->alias]['week']['score'] += $filter_data[$phone['w'.$data[$model->alias]['week']['index']]]['score'];
                $data[$model->alias]['week']['time'] += $filter_data[$phone['w'.$data[$model->alias]['week']['index']]]['time'];
                $data[$model->alias]['month']['score'] += $filter_data[$phone['m'.$data[$model->alias]['month']['index']]]['score'];
                $data[$model->alias]['month']['time'] += $filter_data[$phone['m'.$data[$model->alias]['month']['index']]]['time'];
                $model->save($data);
                unset($filter_data[$phone]['date']);
            }
        }
        
        $this->out('hoan thanh cong du lieu vao bang tuan, thang!');
    }
}