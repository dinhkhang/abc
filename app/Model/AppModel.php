<?php

/**
 * Application model for CakePHP.
 *
 * This file is application-wide model file. You can put all
 * application-wide model-related methods here.
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Model
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
App::uses('Model', 'Model');

/**
 * Application model for Cake.
 *
 * Add your application-wide methods in the class below, your models
 * will inherit them.
 *
 * @package       app.Model
 */
class AppModel extends Model {

    public $object_type_id = null;

    public function beforeValidate($options = array()) {
        parent::beforeValidate($options);

        // remove model_name
        if (isset($this->data[$this->alias]['model_name'])) {
            unset($this->data[$this->alias]['model_name']);
        }

        // thực hiện chuyển về đúng kiểu dữ liệu của trường field 
        if (isset($this->data[$this->alias]['status'])) {
            $this->data[$this->alias]['status'] = (int) $this->data[$this->alias]['status'];
        }

        // thực hiện chuyển về đúng kiểu dữ liệu của trường field 
        if (isset($this->data[$this->alias]['standard_rate'])) {
            $this->data[$this->alias]['standard_rate'] = (int) $this->data[$this->alias]['standard_rate'];
        }

        // thực hiện chuyển về đúng kiểu dữ liệu của trường field 
        if (
                isset($this->data[$this->alias]['capacity']) &&
                strlen($this->data[$this->alias]['capacity']) > 0
        ) {
            $this->data[$this->alias]['capacity'] = (int) $this->data[$this->alias]['capacity'];
        }
        // thực hiện chuyển về đúng kiểu dữ liệu của trường field 
        if (
                isset($this->data[$this->alias]['order']) &&
                strlen($this->data[$this->alias]['order']) > 0
        ) {
            $this->data[$this->alias]['order'] = (int) $this->data[$this->alias]['order'];
        }

        // thực hiện chuyển về đúng kiểu dữ liệu của trường parent id 
        if (isset($this->data[$this->alias]['parent_id']) &&
                strlen($this->data[$this->alias]['parent_id']) &&
                !($this->data[$this->alias]['parent_id'] instanceof MongoId)
        ) {
            $this->data[$this->alias]['parent_id'] = new MongoId($this->data[$this->alias]['parent_id']);
        }

        // thực hiện chuyển về đúng kiểu dữ liệu của trường category
        $categories = $this->useTable . '_categories';
        if (isset($this->data[$this->alias][$categories]) && is_array($this->data[$this->alias][$categories])) {
            foreach ($this->data[$this->alias][$categories] AS $key => $category) {
                $this->data[$this->alias][$categories][$key] = new MongoId($category);
            }
        }

        // thực hiện chuyển về đúng kiểu dữ liệu của trường collection
        $collections = $this->useTable . '_collections';
        if (isset($this->data[$this->alias][$collections]) && is_array($this->data[$this->alias][$collections])) {
            foreach ($this->data[$this->alias][$collections] AS $key => $collection) {
                $this->data[$this->alias][$collections][$key] = new MongoId($collection);
            }
        }

        if (
                isset($this->data[$this->alias]['categories']) &&
                is_array($this->data[$this->alias]['categories']) &&
                !empty($this->data[$this->alias]['categories'])
        ) {

            foreach ($this->data[$this->alias]['categories'] as $k => $v) {

                $this->data[$this->alias]['categories'][$k] = new MongoId($v);
            }
        }
        if (
                isset($this->data[$this->alias]['collections']) &&
                is_array($this->data[$this->alias]['collections']) &&
                !empty($this->data[$this->alias]['collections'])
        ) {

            foreach ($this->data[$this->alias]['collections'] as $k => $v) {

                $this->data[$this->alias]['collections'][$k] = new MongoId($v);
            }
        }
        if (
                isset($this->data[$this->alias]['visitor_notification_groups']) &&
                is_array($this->data[$this->alias]['visitor_notification_groups']) &&
                !empty($this->data[$this->alias]['visitor_notification_groups'])
        ) {

            foreach ($this->data[$this->alias]['visitor_notification_groups'] as $k => $v) {

                $this->data[$this->alias]['visitor_notification_groups'][$k] = new MongoId($v);
            }
        }

        if (
                isset($this->data[$this->alias]['loc']['coordinates']) &&
                is_array($this->data[$this->alias]['loc']['coordinates']) &&
                !empty($this->data[$this->alias]['loc']['coordinates'])
        ) {

            foreach ($this->data[$this->alias]['loc']['coordinates'] as $k => $v) {

                $this->data[$this->alias]['loc']['coordinates'][$k] = floatval($v);
            }
        }

        if (isset($this->data[$this->alias]['loc']) && empty($this->data[$this->alias]['loc']['type'])) {

            $this->data[$this->alias]['loc']['type'] = Configure::read('sysconfig.App.GeoJSON_type');
        }

        // thực hiện đồng bộ thông tin trong Location với model hiện tại khi thực hiện edit
        if (
                isset($this->customSchema['loc']) &&
                !empty($this->data[$this->alias]['id'])
        ) {

            $get_back = $this->find('first', array(
                'conditions' => array(
                    'id' => new MongoId($this->data[$this->alias]['id']),
                ),
            ));

            $location_update = array();
            if (isset($this->data[$this->alias]['status'])) {

                $location_update['status'] = $this->data[$this->alias]['status'];
            }
            if (isset($this->data[$this->alias]['name'])) {

                $location_update['name'] = $this->data[$this->alias]['name'];
            }
            if (isset($this->data[$this->alias]['address'])) {

                $location_update['address'] = $this->data[$this->alias]['address'];
            }
            if (isset($this->data[$this->alias]['loc'])) {

                $location_update['loc'] = $this->data[$this->alias]['loc'];
            }
            if (
                    !empty($location_update) &&
                    !empty($get_back[$this->alias]['location']) &&
                    is_array($get_back[$this->alias]['location']) &&
                    !empty($get_back[$this->alias]['location']['_id'])
            ) {

                App::uses('Location', 'Model');
                $Location = new Location();
                $location_update['id'] = (string) $get_back[$this->alias]['location']['_id'];
                $Location->save($location_update);
            }
        }

        $user = CakeSession::read('Auth.User');
        if (
                !empty($user) &&
                !isset($this->data[$this->alias]['user']) &&
                empty($this->data[$this->alias]['id'])
        ) {
            $this->data[$this->alias]['user'] = new MongoId($user['id']);
        }

        // cache lại file_uris vào bảng content
        $this->parseFileUrisAfterSave();

        // nếu có định nghĩa schema, bắt chặt dữ liệu theo cấu trúc của schema chi khi create
        // đồng thời tự tạo ra các fields nếu trong $this->data đầu vào không tồn tại
        if (!empty($this->customSchema) && empty($this->data[$this->alias]['id'])) {

            $schema_data = $this->mergeCustomSchema($this->customSchema, $this->data[$this->alias]);
            $this->data[$this->alias] = $schema_data;
        }

        // nếu định nghĩa $asciiFields, thực hiện convert string gốc sang dạng ascii
        if (!empty($this->asciiFields)) {

            $this->convertFieldsToAscii($this->asciiFields, $this->data[$this->alias]);
        }

        return true;
    }

    /**
     * convertFieldsToAscii
     * thực hiện convert dữ liệu string tương ứng với field sang dạng ascii
     * 
     * @param array $fields
     * @param reference array $data
     */
    protected function convertFieldsToAscii($fields, &$data, $suffix = '_ascii') {

        App::import('Lib', 'Html2TextUtility');
        foreach ($fields as $v) {

            // nếu là trường field không phân cấp
            if (strpos($v, '.') === false && isset($data[$v])) {

                // nếu giá trị value của trường không phải là multiple-select
                if (!is_array($data[$v])) {

                    $content = Html2TextUtility::getText($data[$v]);
                    $data[$v . $suffix] = $this->convert_vi_to_en($content);
                }
                // nếu giá trị value là multiple-select, tức là 1 mảng array
                else {

                    if (empty($data[$v])) {

                        continue;
                    }
                    foreach ($data[$v] as $kk => $vv) {

                        $content = Html2TextUtility::getText($vv);
                        $data[$v . $suffix][$kk] = $this->convert_vi_to_en($content);
                    }
                }
            }
            // nếu là field trường phân cấp
            elseif (strpos($v, '.') !== false) {

                $index = $this->makeIndexArray($v);
                $evaluate = eval('return isset($data' . $index . ');');
                if (!$evaluate) {

                    continue;
                }
                $index_ascii = $this->makeIndexArray($v, $suffix);
                eval('$evaluate_value = $data' . $index . ';');

                // nếu giá trị value của trường không phải là multiple-select
                if (!is_array($evaluate_value)) {

                    eval('$content = Html2TextUtility::getText($data' . $index . ');');
                    eval('$data' . $index_ascii . ' = $this->convert_vi_to_en($content);');
                }
                // nếu giá trị value là multiple-select, tức là 1 mảng array
                else {

                    if (empty($evaluate_value)) {

                        continue;
                    }
                    foreach ($evaluate_value as $kk => $vv) {

                        $kk = $this->makeIndexArray($kk);

                        eval('$content = Html2TextUtility::getText($vv);');
                        eval('$data' . $index_ascii . $kk . ' = $this->convert_vi_to_en($content);');
                    }
                }
            }
        }
    }

    protected function makeIndexArray($path, $suffix = null) {

        if (!strlen($path)) {

            return;
        }

        $extract = explode('.', $path);
        $index_path = '';
        $counter = 1;
        foreach ($extract as $v) {

            if ($counter == count($extract)) {

                $v = $v . $suffix;
            }
            if (is_numeric($v)) {

                $index_path .= '[' . $v . ']';
            } else {

                $index_path .= '["' . $v . '"]';
            }
            $counter++;
        }

        return $index_path;
    }

    /**
     * mergeCustomSchema
     * Thực hiện merge schema với dữ liệu data input đầu vào
     * Đảm bảo các fields trong schema, luôn được lưu vào trong database
     * Đảm bảo những fields thừa trong data input đầu vào, sẽ bị loại bỏ, không lưu vào trong database
     * 
     * @param array $customSchema
     * @param reference array $data
     */
    protected function mergeCustomSchema($customSchema, $data) {

        App::import('Lib', 'ExtendedUtility');
        $reduce = ExtendedUtility::array_intersect_key_recursive($data, $customSchema);
        $map = Hash::merge($customSchema, $reduce);
        $data = $map;

        return $data;
    }

    /**
     * recoverCustomSchema
     * Thực hiện reset lại schema đối với toàn bộ dữ liệu 
     * 
     * @param array $options
     * @return mixed
     */
    public function recoverCustomSchema($options = array()) {

        $items = $this->find('all', $options);
        if (empty($items)) {

            return;
        }
        if (empty($this->customSchema)) {

            throw new NotImplementedException(__('Can not recover schema, because %s model does not define customSchema property', $this->alias));
        }
        foreach ($items as $item) {

            $id = $item[$this->alias]['id'];
            $recover_data = $this->mergeCustomSchema($this->customSchema, $item[$this->alias]);
            $this->mongoNoSetOperator = true;
            $this->save($recover_data);
        }
    }

    public function notWhiteSpace($check) {

        $check = array_values($check);
        $value = trim($check[0]);

        if (strpos($value, ' ') !== false) {
            return false;
        }

        return true;
    }

    /**
     * Returns false if any fields passed match any (by default, all if $or = false) of their matching values.
     *
     * Can be used as a validation method. When used as a validation method, the `$or` parameter
     * contains an array of fields to be validated.
     *
     * @param array $fields Field/value pairs to search (if no values specified, they are pulled from $this->data)
     * @param bool|array $or If false, all fields specified must match in order for a false return value
     * @return bool False if any records matching any fields are found
     */
    public function isUnique($fields, $or = true) {
        if (is_array($or)) {
            $isRule = (
                    array_key_exists('rule', $or) &&
                    array_key_exists('required', $or) &&
                    array_key_exists('message', $or)
                    );
            if (!$isRule) {
                $args = func_get_args();
                $fields = $args[1];
                $or = isset($args[2]) ? $args[2] : true;
            }
        }
        if (!is_array($fields)) {
            $fields = func_get_args();
            $fieldCount = count($fields) - 1;
            if (is_bool($fields[$fieldCount])) {
                $or = $fields[$fieldCount];
                unset($fields[$fieldCount]);
            }
        }

        foreach ($fields as $field => $value) {
            if (is_numeric($field)) {
                unset($fields[$field]);

                $field = $value;
                $value = null;
                if (isset($this->data[$this->alias][$field])) {
                    $value = $this->data[$this->alias][$field];
                }
            }

            if (strpos($field, '.') === false) {
                unset($fields[$field]);
//				$fields[$this->alias . '.' . $field] = $value;
                $fields[$this->alias . '.' . $field]['$regex'] = new MongoRegex("/^" . $value . "$/i"); // sửa lại cho tương thích với Mongodb
            }
        }

        if ($or) {
//			$fields = array('or' => $fields);
            $fields = array('$or' => array($fields)); // sửa lại cho tương thích với Mongodb
        }

        if (!empty($this->id)) {
//			$fields[$this->alias . '.' . $this->primaryKey . ' !='] = $this->id;
            $fields[$this->alias . '.' . $this->primaryKey]['$ne'] = $this->id; // sửa lại cho tương thích với Mongodb
        }

        return !$this->find('count', array('conditions' => $fields, 'recursive' => -1));
    }

    /**
     * convert_vi_to_en method
     * hàm chuyền đổi tiếng việt có dấu sang tiếng việt không dấu
     * @param string $str
     * @return string
     */
    public function convert_vi_to_en($str) {

        $str = preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/", 'a', $str);
        $str = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/", 'e', $str);
        $str = preg_replace("/(ì|í|ị|ỉ|ĩ)/", 'i', $str);
        $str = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/", 'o', $str);
        $str = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/", 'u', $str);
        $str = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/", 'y', $str);
        $str = preg_replace("/(đ)/", 'd', $str);
        $str = preg_replace("/(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)/", 'A', $str);
        $str = preg_replace("/(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)/", 'E', $str);
        $str = preg_replace("/(Ì|Í|Ị|Ỉ|Ĩ)/", 'I', $str);
        $str = preg_replace("/(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)/", 'O', $str);
        $str = preg_replace("/(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)/", 'U', $str);
        $str = preg_replace("/(Ỳ|Ý|Ỵ|Ỷ|Ỹ)/", 'Y', $str);
        $str = preg_replace("/(Đ|Ð)/", 'D', $str);
        //$str = str_replace(" ", "-", str_replace("&*#39;","",$str));
        // thực hiện cưỡng ép chuyển sang ascii
        $str = $this->forceConvertASCII($str);

        return $str;
    }

    /**
     * forceConvertASCII
     * 
     * @param string $str
     * @return string
     */
    public function forceConvertASCII($str) {

        try {

            $ascii_str = @iconv("UTF-8", "us-ascii//TRANSLIT", $str);
        } catch (Exception $e) {

            $this->log($e, 'notice');
            $this->log($str, 'notice');
        }
        return $ascii_str;
    }

    /**
     * isASCII
     * Thực hiện kiểm tra chuỗi string có phải là ASCII k?
     * 
     * @param string $str
     * @return boolean
     */
    public function isASCII($str) {

        return mb_detect_encoding($str, 'ASCII', true);
    }

    /**
     * In the event of ambiguous results returned (multiple top level results, with different parent_ids)
     * top level results with different parent_ids to the first result will be dropped
     *
     * @param string $state Either "before" or "after".
     * @param array $query Query.
     * @param array $results Results.
     * @return array Threaded results
     */
    protected function _findThreaded($state, $query, $results = array()) {
        if ($state === 'before') {
            return $query;
        }

        $parent = 'parent_id';
        if (isset($query['parent'])) {
            $parent = $query['parent'];
        }

        if (!empty($results)) {

            foreach ($results as $k => $v) {

                if (!empty($v[$this->alias][$parent]) && $v[$this->alias][$parent] instanceof MongoId) {

                    $results[$k][$this->alias][$parent] = (string) $v[$this->alias][$parent];
                }
            }
        }

        return Hash::nest($results, array(
                    'idPath' => '{n}.' . $this->alias . '.' . $this->primaryKey,
                    'parentPath' => '{n}.' . $this->alias . '.' . $parent
        ));
    }

    public function afterFind($results, $primary = false) {
        parent::afterFind($results, $primary);

        if (!empty($results)) {

            foreach ($results as $k => $v) {

                $this->pasreCategoryAfterFind($results, $v, $k, $this->useTable . '_categories');
                $this->pasreCategoryAfterFind($results, $v, $k, $this->useTable . '_collections');
                $this->pasreCategoryAfterFind($results, $v, $k, 'topics');
                $this->pasreCategoryAfterFind($results, $v, $k, 'categories');
                $this->pasreCategoryAfterFind($results, $v, $k, 'collections');
            }
        }

        return $results;
    }

    protected function parseFileUrisAfterSave() {

        // cache lại file_uris vào bảng content
        if (
                isset($this->data[$this->alias]['files']) &&
                is_array($this->data[$this->alias]['files']) &&
                !empty($this->data[$this->alias]['files'])
        ) {
            $this->data[$this->alias]['file_uris'] = array();

            App::import('Model', 'FileManaged');
            foreach ($this->data[$this->alias]['files'] as $k => $v) {

                if (!is_array($v) || empty($v)) {

                    continue;
                }

                foreach ($v as $vv) {

                    $FileManaged = new FileManaged();
                    $file = $FileManaged->find('first', array(
                        'conditions' => array(
                            'id' => $vv,
                        ),
                    ));
                    $file_id = (string) $vv;
                    if (empty($file)) {

                        continue;
                    }

                    $this->data[$this->alias]['file_uris'][$k][$file_id] = $file['FileManaged']['uri'];
                }
            }
        }
    }

    protected function pasreCategoryAfterFind(&$results, $item, $k, $field) {

        if (
                isset($item[$this->alias][$field]) &&
                is_array($item[$this->alias][$field]) &&
                !empty($item[$this->alias][$field])
        ) {

            foreach ($item[$this->alias][$field] as $k1 => $v1) {

                if ($v1 instanceof MongoId) {

                    $results[$k][$this->alias][$field][$k1] = (string) $v1;
                }
            }
        }
    }

    protected function _findDailyCollection($state, $query, $results = array()) {


        return $results;
    }

    public function beforeSave($options = array()) {
        parent::beforeSave($options); // TODO: Change the autogenerated stub
        if (isset($this->data[$this->alias]['ref_id']) && $this->data[$this->alias]['ref_id']) {
            $this->data[$this->alias]['ref_id'] = new MongoId($this->data[$this->alias]['ref_id']);
        }
    }

    /**
     * logAnyFile
     * 
     * @param mixed $content
     * @param string $file_name
     */
    protected function logAnyFile($content, $file_name) {

        CakeLog::config($file_name, array(
            'engine' => 'File',
            'types' => array($file_name),
            'file' => $file_name,
        ));

        $this->log($content, $file_name);
    }

    /**
     * getLastQuery
     * 
     * return last query
     */
    public function getLastQuery()
    {
        $dbo = $this->getDatasource();
        $logs = $dbo->getLog();
        $lastLog = end($logs['log']);
        return $lastLog['query'];
    }

}
