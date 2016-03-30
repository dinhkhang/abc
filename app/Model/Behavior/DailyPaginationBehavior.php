<?php

class DailyPaginationBehavior extends ModelBehavior {

    public $total = 0;
    public $total_virtual = 0; //tổng số bản ghi được hiệu chỉnh lại cho việc đơn giản hóa phân trang

    public function paginate(Model $model, $conditions, $fields, $order, $limit, $page = 1, $recursive = null, $extra = array()) {

        $from_date = $extra['from_date'];
        $to_date = $extra['to_date'];

        $date_ranges = $this->extractDateRange($from_date, $to_date);
        if (empty($date_ranges)) {

            return array();
        }

        // thực hiện đếm tổng số bản ghi qua tất cả các collection theo date
        // đồng thời xác định xem collection date nào sẽ được truy vấn lấy ra dữ liệu
        $fragments = array();
        $model_name = $model->alias;
        $seq_begin = 1;
        foreach ($date_ranges as $k => $date) {

            $DailyModel = new $model_name();
            $DailyModel->init($date);
            $total_by_date = $DailyModel->find('count', array(
                'conditions' => $conditions,
            ));

            if (empty($total_by_date)) {

                unset($date_ranges[$k]);
                continue;
            }

            $page_by_date = ceil($total_by_date / $limit);
            $page_begin = ceil($seq_begin / $limit);

            $this->total += $total_by_date;
            $seq_begin = $this->total + 1;
            $this->total_virtual += $page_by_date * $limit;

            $fragments[$date]['page_begin'] = $page_begin;
            $fragments[$date]['page_end'] = ceil($this->total / $limit);
        }

        // thực hiện set tổng số bản ghi thật sự
        $model->total = $this->total;

        $result = array();
        foreach ($date_ranges as $date) {

            $page_begin = $fragments[$date]['page_begin'];
            $page_end = $fragments[$date]['page_end'];

            // nếu $page hiện tại không thuộc vào khoảng [$page_begin,$page_end] của date collection hiện tại
            // thì chuyển về date collection trước đó
            if ($page > $page_end || $page < $page_begin) {

                continue;
            }

            $page_in_date = ($page - $page_begin) + 1;

            $DailyModel = new $model_name();
            $DailyModel->init($date);
            $result_by_date = $DailyModel->find('all', array(
                'conditions' => $conditions,
                'fields' => $fields,
                'order' => $order,
                'page' => $page_in_date,
                'limit' => $limit,
            ));
            $result = array_merge($result, $result_by_date);
        }

        return $result;
    }

    public function paginateCount(Model $model, $conditions = null, $recursive = 0, $extra = array()) {

        return $this->total_virtual;
    }

    protected function extractDateRange($from_date, $to_date) {

        if (strtotime($from_date) > strtotime($to_date)) {

            return false;
        }

        $date_ranges = array();
        while (date('Ymd', strtotime($from_date)) <= date('Ymd', strtotime($to_date))) {

            $date_ranges[] = date('Y-m-d', strtotime($from_date));
            $from_date = date('Y-m-d', strtotime('+1 day', strtotime($from_date)));
        }

        return array_reverse($date_ranges); // đảo ngược thứ tự
    }

}
