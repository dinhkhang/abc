<?php

App::uses('Component', 'Controller');

class PaginationComponent extends Component {

	public $controller = null;
	public $datetime_fields = array(
		'modified',
		'created',
	);
	public $components = array(
        'Session'
    );

	public function initialize(\Controller $controller) {

		parent::initialize($controller);

		$this->controller = $controller;
	}

	//$add_mt = true: trường hợp lấy mo+mt data
	public function paginate($model_pattern, $from_date, $to_date, $options = array(),$add_mt=false) {

		$this->controller->set('model_name', 'AppModel');

		$date_range = $this->extractDateRange($from_date, $to_date);
		if ($date_range === false) {

			return array();
		}

		if (empty($options['not_search_date'])) {

			$this->setSearchConds($options, $from_date, $to_date);
		}

		// với trường hợp lý tưởng tìm kiếm trong phạm vi của 1 ngày
		if (count($date_range) == 1) {

			$year = $date_range[0]['year'];
			$month = $date_range[0]['month'];
			$day = $date_range[0]['day'];

			$this->controller->Paginator->settings = $options;
			// tạo Model object động theo $year, $month và $day
			App::uses('AppModel', 'Model');
			$His = new AppModel(array(
				'table' => sprintf($model_pattern, $year, $month, $day),
			));

			$list_data = $this->controller->Paginator->paginate($His);
			$this->setConvertDate($list_data);
			
			if($add_mt) $this->add_mt_data($list_data,sprintf('mt_%s_%s_%s', $year, $month, $day));
			return $list_data;
		}
		// với trường hợp tìm kiếm qua nhiều ngay khác nhau
		else {

			$options['date_range'] = $date_range;
			$options['model_pattern'] = $model_pattern;
			$options['add_mt'] = $add_mt;
			App::uses('Paginator', 'Model');
			$His = new Paginator();

			$this->controller->Paginator->settings = $options;
			
			$list_data = $this->controller->Paginator->paginate($His);
			$this->setConvertDate($list_data);
			return $list_data;
		}
	}

	protected function setConvertDate(&$list_data) {

		if (empty($list_data)) {

			return;
		}

		foreach ($list_data as $k => $v) {

			foreach ($this->datetime_fields as $field) {

				if (isset($v['AppModel'][$field]) && $v['AppModel'][$field] instanceof MongoDate) {

					$list_data[$k]['AppModel'][$field] = date('d-m-Y H:i:s', $v['AppModel'][$field]->sec);
				}
			}
		}
	}

	protected function extractDateRange($from_date, $to_date) {

		if (strtotime($from_date) > strtotime($to_date)) {

			return false;
		}
		$date_range = array();

		// lấy ra danh sách cặp (year, month, day) trong khoảng from_date và to_date
		$start = strtotime($from_date);
		$end = strtotime($to_date);

		$start_date = date('Ymd', $start);
		$end_date = date('Ymd', $end);
		while ($start_date <= $end_date) {

			$date_range[] = array(
				'year' => date('Y', $start),
				'month' => date('m', $start),
				'day' => date('d', $start),
			);
			$start = strtotime("+1 day", $start);
			$start_date = date('Ymd', $start);
		}

		return array_reverse($date_range); // đảo ngược thứ tự, cho năm tháng gần nhất lên đầu
	}

	protected function setSearchConds(&$options, $from_date, $to_date) {
		$username = $this->controller->request->query('username');
		if(empty($this->controller->request->query('username'))){
            $session_user = $this->Session->read('History.Username');
            if(isset($session_user)){
                $username = $session_user;
            }else{
            	$username = '';
            }
        }

		//$options['conditions']['mobile'] = $username;
		if( !empty( $this->controller->request->query('package') ) ) {
			$options['conditions']['package'] = $this->controller->request->query('package');
		}
		if (!empty($from_date)) {

			$options['conditions']['created']['$gte'] = new MongoDate(strtotime($from_date));
		}

		if (!empty($to_date)) {

			$options['conditions']['created']['$lte'] = new MongoDate(strtotime($to_date));
		}
	}

	/**
	 * add_mt_data
	 * Thêm dữ liệu MT vào MO tương ứng
	 * 
	 * @param reference array &$result
	 * @return mixed
	 */
	protected function add_mt_data(&$result,$mt_table) {

		if (empty($result)) {
			return;
		}

		$Mt = new AppModel(array(
				'table' => $mt_table,
			));

		foreach ($result as $k => $v) {
			$moid = $v['AppModel']['id'];
			$options = array(
				'conditions'	=>	array(
					'mo_id'	=>	$moid
				)
			);
			$mt_data = $Mt->find('first',$options);
			if(!empty($mt_data)){
				$mt_data['AppModel']['created'] = date('d-m-Y H:i:s', $mt_data['AppModel']['created']->sec);
				$mt_data['AppModel']['modified'] = date('d-m-Y H:i:s', $mt_data['AppModel']['modified']->sec);
				$result[$k]['AppModel']['mt_data'] = $mt_data['AppModel'];
			}
			
		}
	}

}
