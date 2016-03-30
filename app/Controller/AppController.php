<?php

/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
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
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
App::uses('Controller', 'Controller');

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package		app.Controller
 * @link		http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller {

    public $components = array(
        'Session',
        'Paginator',
        'Auth' => array(
            'loginAction' => array(
                'controller' => 'Users',
                'action' => 'login',
            ),
            'loginRedirect' => array('controller' => 'Regions', 'action' => 'index'),
            'logoutRedirect' => array('controller' => 'Users', 'action' => 'login'),
            'authorize' => array('Controller'),
            'authenticate' => array(
                'Form' => array(
                    'userModel' => 'User',
                    'fields' => array('username' => 'username', 'password' => 'password')
                )
            )
        ),
        'DebugKit.Toolbar',
    );
    public $uses = array(
        'Region'
    );
    public $helpers = array('Common');
    public $paginate = array(
        'limit' => 20,
        'order' => 'modified'
    );
    public $optionSortByOrder = [
        'fields' => ['name'],
        'order' => 'order',
        'conditions' => ['status' => 2]
    ];
    public $specialModel = ['Atm'];
    public $object_type_id = null;
    public $object_type_code = null;
    public $objectId = null;
    public $objectTypeId = null;
    public $objectInfo = null;
    public $parentObject = null;
    public $dynamicModel = null;
    public $dynamicObject = null;
    public $modelParentCode = null;
    public $modelParentModel = null;

    public function beforeFilter() {
        parent::beforeFilter();
        $this->{$this->modelClass}->request = $this->request;
        if (in_array('Location', $this->uses)) {
            $this->Location->request = $this->request;
        }

        $this->setObjectTypeId();
        $this->set('object_type_id', $this->object_type_id);
        $this->set('object_type_code', $this->object_type_code);

        $user = $this->Auth->user();
        if(!empty($user['permissions'])){
            $this->set('_permissions', $user['permissions']);
        }
        $this->mAllPackages = $this->getAllPackage();
        
        $this->set('allPackage', $this->mAllPackages);
        //debug($user['permissions']);
//        $this->getMenus();
    }

    /**
     * convertPhoneNumber
     * thực hiện chuyển đổi số điện thoại có số 0 ở đầu ví dụ 098 thành 8498
     * 
     * @param string $phone
     * @return string
     */
    protected function convertPhoneNumber($phone) {

        $target = $phone;
        $first = substr($phone, 0, 1);
        $last = substr($phone, 1);
        $country_code = '84';
        if ($first == 0) {

            $target = $country_code . $last;
        }

        return $target;
    }

    protected function remove_plus_character($phone) {

        $target = $phone;
        $first = substr($phone, 0, 1);
        $last = substr($phone, 1);
        if ($first == '+') {

            $target = $last;
        }

        return $target;
    }

    /**
     * generateRandomLetters
     * thực tạo ra các kí tự ngẫu nhiên
     * 
     * @param int $length
     * @return string
     */
    public function generateRandomLetters($length) {

        $random = '';

        for ($i = 0; $i < $length; $i++) {

            $random .= chr(rand(ord('a'), ord('z')));
        }

        return $random;
    }

    public function setObjectTypeId($code = null) {

        if (empty($code)) {

            $code = $this->{$this->modelClass}->useTable;
        }

        if (!isset($this->ObjectType)) {

            $this->loadModel('ObjectType');
        }

        $object_type = $this->ObjectType->find('first', array(
            'conditions' => array(
                'code' => $code,
            ),
        ));

        if (!empty($object_type)) {

            $this->object_type_id = $object_type['ObjectType']['id'];
            $this->object_type_code = $object_type['ObjectType']['code'];
        }

        if (isset($this->{$this->modelClass})) {

            $this->{$this->modelClass}->object_type_id = $this->object_type_id;
        }
    }

    public function getList($model_name = null, $options = array()) {

        $status_approved = Configure::read('sysconfig.App.constants.STATUS_APPROVED');
        if (!empty($options['fields'])) {

            $fields = $options['fields'];
        }
        $conditions = [
            'status' => $status_approved
        ];
        if (isset($_GET['lang_code']) && $_GET['lang_code']) {
            $conditions['lang_code'] = $_GET['lang_code'];
        }
        $default_options = array(
            'conditions' => $conditions,
            'fields' => array(
                'id', 'name',
            ),
        );
        $options = Hash::merge($default_options, $options);
        if (!empty($fields)) {

            $options['fields'] = $fields;
        }
        if (empty($model_name)) {

            $model_name = $this->modelClass;
        }

        $list_data = $this->$model_name->find('list', $options);
        return $list_data;
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
//		$str = $this->forceConvertASCII($str);

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

    public function reqUpload() {

        $this->autoRender = false;
        App::import('Vendor', 'CustomUploadHandler', array('file' => 'jQueryFileUpload/server/php' . DS . 'CustomUploadHandler.php'));

        $upload_handler = new CustomUploadHandler();
        $result = $upload_handler->post(false);

        if (empty($result['files'][0])) {

            $this->log(__('Quá trình upload file gặp lỗi'));
            $this->log($result);

            echo json_encode($result);
            return;
        }

        if (!isset($this->FileManaged)) {

            $this->loadModel('FileManaged');
        }

        $status_file_upload_to_tmp = Configure::read('sysconfig.App.constants.STATUS_FILE_UPLOAD_TO_TMP');

        $file = &$result['files'][0];
        if (empty($file->type)) {

            $file->type = $this->getMimeType($file->name);
        }
        $save_data = array(
            'name' => $file->name,
            'size' => $file->size,
            'mime' => $file->type,
            'status' => $status_file_upload_to_tmp,
            'uri' => WEBROOT_DIR . DS . 'tmp' . DS . $file->name,
        );

        $this->FileManaged->create();
        $this->FileManaged->save($save_data);
        $file_id = $this->FileManaged->getLastInsertID();

        // đọc lại thông tin file
        $file_obj = $this->FileManaged->find('first', array(
            'conditions' => array(
                'id' => new MongoId($file_id),
            ),
            'fields' => array(
                'name', 'size', 'mime', 'status', 'uri',
            ),
        ));

        // chuỗi hóa thông tin về file
        $file->fileSerialize = json_encode($file_obj['FileManaged']);

        // thực hiện ghi đè lại deleteUrl
        $deleteUrl = Router::url(array(
                    'action' => 'reqDeleteFile',
                    '?' => array(
                        'id' => $file_id,
        )));
        $file->deleteUrl = $deleteUrl;

        echo json_encode($result);
        return;
    }

    public function reqDeleteFile() {

        $this->autoRender = false;
        if ($this->request->is('delete') || $this->request->is('post')) {

            $file_id = $this->request->query('id');
            $result = array(
                'success' => true
            );

            if (!isset($this->FileManaged)) {

                $this->loadModel('FileManaged');
            }

            $status_file_upload_to_tmp = Configure::read('sysconfig.App.constants.STATUS_FILE_UPLOAD_TO_TMP');

            $get_file = $this->FileManaged->find('first', array(
                'conditions' => array(
                    'id' => new MongoId($file_id),
//					'status' => $status_file_upload_to_tmp, // chỉ được phép xóa file tạm
                )
            ));

            // nếu file không tồn tại
            if (empty($get_file)) {

                echo json_encode($result);
                return;
            }

            // nếu file là file nằm trong thự mục tmp (file tạm) - thực hiện xóa vật lý
            if ($get_file['FileManaged']['status'] == $status_file_upload_to_tmp) {

                $uri = $get_file['FileManaged']['uri'];

                if ($this->FileManaged->delete($file_id, false)) {

                    $file = new File(APP . $uri, false);
                    $file->delete();

                    echo json_encode($result);
                    return;
                } else {

                    $location = __CLASS__ . ':' . __FUNCTION__ . ':' . __LINE__;
                    $this->log(__($location . ': ' . __('Can not delete a file, the file as below')));
                    $this->log($get_file);

                    echo json_encode(array(
                        'success' => false,
                        'message' => __('delete_file_error_message'),
                    ));
                }
            }
            // nếu file đã được sử dụng - chỉ thực hiện set cờ
            else {

//                                $update_data = array(
//                                    'id' => $file_id,
//                                    'status' => $status_file_upload_to_tmp,
//                                    'uri' => $get_file['FileManaged']['uri'],
//                                );
//                                if ($this->FileManaged->save($update_data)) {
//
                echo json_encode($result);
                return;
//                                } else {
//
//                                        $location = __CLASS__ . ':' . __FUNCTION__ . ':' . __LINE__;
//                                        $this->log(__($location . ': ' . __('Can not set a file as deleted, the file as below')));
//                                        $this->log($get_file);
//
//                                        echo json_encode(array(
//                                            'success' => false,
//                                            'message' => __('delete_file_error_message'),
//                                        ));
//                                }
            }
        }
    }

    /**
     * Delete a record
     * 
     * @author trungnq
     * @param type $id
     */
    public function reqDelete($id = null) {

        $this->autoRender = false;
        $res = array(
            'error_code' => 0,
            'message' => __('delete_successful_message'),
        );
        if (!$this->request->is('post')) {

            $res = array(
                'error_code' => 1,
                'message' => __('invalid_data'),
            );
            echo json_encode($res);
            return;
        }
        $model_name = $this->request->data('model_name');
        if (empty($model_name) && in_array($model_name, $this->specialModel)) {
            if (isset($this->request->query['objectTypeId'], $this->request->query['objectId'])) {
                $this->loadModel('ObjectType');
                $object = $this->ObjectType->find('first', ['conditions' => [
                        'id' => new MongoId($this->request->query['objectTypeId']),
                        'status' => 2,
                ]]);
                if (!$object) {
                    throw new NotFoundException(__('invalid_data'));
                }
                $model_name = $object['ObjectType']['name'] . $this->modelClass;
            } else {
                $model_name = $this->modelClass;
            }
        } else {
            $model_name = isset($this->request->query['model_name']) ? $this->request->query['model_name'] : $this->modelClass;
        }
        if (!$this->$model_name) {
            $this->loadModel($model_name);
        }

        $check_exist = $this->$model_name->find('first', array(
            'conditions' => array(
                'id' => array(
                    '$eq' => $id,
                ),
            ),
        ));
        if (empty($check_exist)) {

            $res = array(
                'error_code' => 2,
                'message' => __('invalid_data'),
            );
            echo json_encode($res);
            return;
        }

        if ($this->$model_name->delete($id)) {

//                        $this->Session->setFlash(__('delete_successful_message'), 'default', array(), 'good');
            echo json_encode($res);
        } else {

            $res = array(
                'error_code' => 3,
                'message' => __('delete_error_message'),
            );
            echo json_encode($res);
            return;
        }
    }

    /**
     * Delete a record
     * 
     * @author trungnq
     * @param type $id
     */
    public function reqEdit($id = null) {

        $this->autoRender = false;
        $res = array(
            'error_code' => 0,
            'message' => __('save_successful_message'),
        );
        if (!$this->request->is('post')) {

            $res = array(
                'error_code' => 1,
                'message' => __('invalid_data'),
            );
            echo json_encode($res);
            return;
        }
        $model_name = $this->request->data('model_name');
        if (empty($model_name) && in_array($model_name, $this->specialModel)) {
            if (isset($this->request->query['objectTypeId'], $this->request->query['objectId'])) {
                $this->loadModel('ObjectType');
                $object = $this->ObjectType->find('first', ['conditions' => [
                        'id' => new MongoId($this->request->query['objectTypeId']),
                        'status' => 2,
                ]]);
                if (!$object) {
                    throw new NotFoundException(__('invalid_data'));
                }
                $model_name = Inflector::classify($object['ObjectType']['code']) . $this->modelClass;
            } else {
                $model_name = $this->modelClass;
            }
        } else {
            $model_name = $this->request->query('model_name') ? $this->request->query('model_name') : $this->modelClass;
        }

        if (!$this->$model_name) {
            $this->loadModel($model_name);
        }
        $check_exist = $this->$model_name->find('first', array(
            'conditions' => array(
                'id' => array(
                    '$eq' => $id,
                ),
            ),
        ));
        if (empty($check_exist)) {

            $res = array(
                'error_code' => 2,
                'message' => __('invalid_data'),
            );
            echo json_encode($res);
            return;
        }

        $this->request->data['id'] = $id;
        $save_data = $this->request->data;
        if ($this->$model_name->save($save_data)) {

            $this->Session->setFlash(__('save_successful_message'), 'default', array(), 'good');
            echo json_encode($res);
        } else {

            $res = array(
                'error_code' => 3,
                'message' => __('save_error_message'),
            );
            echo json_encode($res);
            return;
        }
    }

    public function reqRegionByCountry() {

        if (!isset($this->LocationCommon)) {

            throw new CakeException(__('LocationCommon component is not defined in %s controller', $this->name));
        }

        $this->LocationCommon->reqRegionByCountry();
    }

    public function reqObjectByObjectType()
    {
        $this->layout = 'ajax';
        if (!isset($this->ObjectType) || !$this->request->data) {

            throw new CakeException(__('ObjectType is not defined in %s model', $this->name));
        }

        $data = $this->ObjectType->reqObjectByObjectType($this->request->data);
        $this->set('data', $data);
        $this->render('/Elements/Req/option_select');
    }

    /**
     * getMimeType
     * nhận dạng mime type của file thông qua đuôi mở rộng
     * @param string $filename
     * @param string $mimePath
     * @return string
     */
    public function getMimeType($filename, $mimePath = '../Config') {
        $fileext = substr(strrchr($filename, '.'), 1);
        if (empty(
                        $fileext))
            return (false);
        $regex = "/^([\w\+\-\.\/]+)\s+(\w+\s)*($fileext\s)/i";
        $lines = file("$mimePath/mime.types");
        foreach ($lines as $line) {
            if (substr($line, 0, 1) == '#')
                continue; // skip comments 
            $line = rtrim($line) . " ";
            if (!preg_match($regex, $line, $matches))
                continue; // no match to the extension 
            return ($matches[1]);
        }
        return (false); // no match at all 
    }

    public function beforeSearch(&$customSearchField) {
        
    }

    public function afterSearch(&$options) {
        
    }

    /**
     * Add common search condition about collection, category, date modified
     * @param array $option
     */
    public function commonSearchCondition(&$options, $customSearchField = []) {
        if (!(isset($this->request->query) && is_array($this->request->query) && count($this->request->query))) {
            return;
        }
        $defaultSearchField = [
            'order' => 'int',
            'status' => 'int',
            'capacity' => 'int',
            'modified' => 'date_range',
            'capacity' => 'int_range',
            'lang_code' => 'string',
            'name' => 'string',
            'tel' => 'string',
            'email' => 'string',
            'source' => 'string',
            'country' => 'location',
            'topics' => 'mongoid',
            'categories' => 'mongoid',
            'collections' => 'mongoid',
            'user' => 'mongoid',
            'categories' => 'mongoid',
            'collections' => 'mongoid',
        ];

        $searchFields = hash::merge($defaultSearchField, $customSearchField);
        // call function beforeSearch
        if ($this->checClassHasMethod($this->name . 'Controller', 'beforeSearch')) {
            // check beforeSearch + call it
            $this->beforeSearch($customSearchField);
        }

        App::uses('DependencyQuery', 'Lib');
        $dependency = new DependencyQuery($this->request->query);

        foreach ($searchFields AS $nameForm => $type) {
            if ($dependency->isArray($nameForm)) {
                $$nameForm = $this->request->query[$nameForm];
                $this->request->query[$nameForm] = $$nameForm;
                $options['conditions'][$nameForm]['$in'] = $$nameForm;
            } elseif ($dependency->isString($nameForm)) {
                switch ($type) {
                    case 'location':
                        if (isset($this->request->query['region']) && strlen($this->request->query['region']) > 0) {
                            $this->request->query['region'] = $this->request->query['region'];
                            $this->request->query['country'] = $this->request->query['country'];
                            $options['conditions']['location.region']['$eq'] = new MongoId($this->request->query['region']);
                        } elseif (isset($this->request->query['country']) && strlen($this->request->query['country']) > 0) {
                            $this->request->query['country'] = $this->request->query['country'];
                            $options['conditions']['location.country_code']['$eq'] = $this->request->query['country'];
                        }
                        $this->set('listLocation', $this->Location->getListLocationId());
                        $this->set('locationInfo', $this->Location->getCountryRegion($this->Country, $this->Region));
                        break;
                    case 'object' :
                        $$nameForm = new MongoId(trim($this->request->query[$nameForm]));
                        $this->request->query[$nameForm] = $$nameForm;
                        $options['conditions'][$nameForm] = $$nameForm;
                        break;
                    case 'string' :
                        $$nameForm = trim($this->request->query[$nameForm]);
                        $this->request->query[$nameForm] = $$nameForm;
                        $options['conditions'][$nameForm]['$regex'] = new MongoRegex("/" . mb_strtolower($$nameForm) . "/i");
                        break;
                    case 'int' :
                        $$nameForm = (int) $this->request->query[$nameForm];
                        $this->request->query[$nameForm] = $$nameForm;
                        $options['conditions'][$nameForm]['$eq'] = $$nameForm;
                        break;
                    /*
                     * code adapt for search date modified with modified_start, modified_end
                     * if search event_start, event_end, then in $customSearchField only need pass 'event' => 'date_range'
                     */
                    case 'date_range' :
                        $date_start = $nameForm . "_start";
                        $date_end = $nameForm . "_end";
                        $$date_start = $this->request->query[$date_start];
                        $$date_end = $this->request->query[$date_end];
                        $this->request->query[$date_start] = $$date_start;
                        $this->request->query[$date_end] = $$date_end;
                        if ($dependency->isDateTime($date_start) || $dependency->isDateTime($date_end)) {
                            //datetime
                            if ($$date_start) {
                                $options['conditions'][$nameForm]['$gte'] = new MongoDate(strtotime($$date_start));
                            }
                            if ($$date_end) {
                                $options['conditions'][$nameForm]['$lte'] = new MongoDate(strtotime($$date_end));
                            }
                        } else {
                            // date
                            if ($$date_start) {
                                $options['conditions'][$nameForm]['$gte'] = new MongoDate(strtotime($$date_start . ' 00:00:00'));
                            }
                            if ($$date_end) {
                                $options['conditions'][$nameForm]['$lte'] = new MongoDate(strtotime($$date_end . ' 23:59:59'));
                            }
                        }
                        break;
                    case 'int_range' :
                        $date_start = $nameForm . "_start";
                        $date_end = $nameForm . "_end";
                        $$date_start = $this->request->query[$date_start];
                        $$date_end = $this->request->query[$date_end];
                        $this->request->query[$date_start] = $$date_start;
                        $this->request->query[$date_end] = $$date_end;
                        if ($$date_start) {
                            $options['conditions'][$nameForm]['$gte'] = (int) $$date_start;
                        }
                        if ($$date_end) {
                            $options['conditions'][$nameForm]['$lte'] = (int) $$date_end;
                        }
                        break;
                    case 'date_time' :
                        $$nameForm = $this->request->query[$nameForm];
                        $this->request->query[$nameForm] = $$nameForm;
                        $options['conditions'][$nameForm]['$eq'] = new MongoDate(strtotime($$nameForm));
                        break;
                    case 'mongoid' :
                        $$nameForm = $this->request->query[$nameForm];
                        $this->request->query[$nameForm] = $$nameForm;
                        $options['conditions'][$nameForm]['$eq'] = new MongoId($$nameForm);
                        break;
                }
            }
        }

        // call function afterSearch
        if ($this->checClassHasMethod($this->name . 'Controller', 'afterSearch')) {
            // check beforeSearch + call it
            $this->afterSearch($options);
        }
    }

    /**
     * isAllow
     * Kiểm tra quyền hạn của user đăng nhập, xem có được phép truy cập vào action hiện tại k?
     * 
     * @return boolean
     */
    protected function isAllow() {

        // lấy ra danh sách permissions của user đang đăng nhập
        $user = $this->Auth->user();
        $permissions = $user['UserGroup']['permissions'];

        $current_controller = $this->name;
        $current_action = $this->action;

        // kiểm tra xem action hiện tại có nằm trong danh sách permissions của user k?
        $perm_path = $current_controller . '/' . $current_action;
        if (in_array($perm_path, $permissions)) {

            return true;
        }

        return false;
    }

    /**
     * isJson
     * kiểm tra xem chuỗi string có phải là json k?
     * 
     * @param string $string
     * @return bool
     */
    protected function isJson($string) {

        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }

    public function isAuthorized($user) {

        // xác định trạng thái của user, nếu không phải là kích hoạt thì logout
        $user_status = $user['status'];
        if ($user_status != Configure::read('sysconfig.App.constants.STATUS_ACTIVE')) {

            return $this->redirect($this->Auth->logout());
        }

        // xác định group của user
        $group = $user['user_group'];
        if (!isset($this->UserGroup)) {

            $this->loadModel('UserGroup');
        }
        // lấy ra quyền permissions tương ứng với UserGroup
//                $user_group = $this->UserGroup->read(null, $group);
        $user_group = $this->UserGroup->find('first', array(
            'conditions' => array(
                'id' => new MongoId($group),
            ),
        ));
        if (empty($user_group)) {

            return $this->redirect($this->Auth->logout());
        }
        // gán quyền permissions vào trong $user
        $permissions = $user_group['UserGroup']['permissions'];
        $user['permissions'] = $permissions;
        $user['UserGroup'] = $user_group['UserGroup'];

        $allow_controllers = array();
        // lấy ra các controller mà user được phép truy cập dựa vào $permissions
        if (!empty($permissions)) {

            foreach ($permissions as $perm) {

                $extract = explode('/', $perm);
                $allow_controllers[$extract[0]] = $extract[0];
            }
        }
        $user['allow_controllers'] = array_values($allow_controllers);

        // xác định type của user để điều hướng cho chính xác
        $home_url = Router::url('/', true) . $user['UserGroup']['home_url'];
        $this->Auth->loginRedirect = $home_url;
        $user['home_url'] = $home_url;
        $this->set('home_url', $home_url);

        // lấy về thông tin content provider nếu user là user của cp
        if (!empty($user['content_provider_code'])) {

            if (!isset($this->ContentProvider)) {

                $this->loadModel('ContentProvider');
            }

            $content_provider = $this->ContentProvider->find('first', array(
                'conditions' => array(
                    'code' => array(
                        '$eq' => $user['content_provider_code'],
                    ),
                    'status' => array(
                        '$eq' => Configure::read('sysconfig.App.constants.STATUS_APPROVED'),
                    ),
                ),
            ));

            // nếu nhà cung cấp content provider bị xóa, thì không cho phép user của cp đó đăng nhập
            if (empty($content_provider)) {

                return $this->redirect($this->Auth->logout());
            }

            $user['ContentProvider'] = $content_provider['ContentProvider'];
        }

        $this->Session->write('Auth.User', $user);

        $this->getMenus();

        return true;
    }

    protected function setObjectInfoInList(&$list_data, $model_name = null) {

        if (empty($list_data) || empty($this->dynamicObject)) {

            return;
        }

        $user_infos = array();
        foreach ($list_data as $k => $v) {
            $user_id = (string) $v[$this->dynamicModel][$this->dynamicObject];
            if (!isset($user_infos[$user_id])) {
                $user_info = $this->{ucfirst($this->dynamicObject)}->findById(new MongoId($user_id));
                $user_infos[$user_id] = !empty($user_info[ucfirst($this->dynamicObject)]) ? $user_info[ucfirst($this->dynamicObject)] : array();
            }
            $list_data[$k][ucfirst($this->dynamicObject)] = $user_infos[$user_id];
        }
    }

    protected function setUserInfoInList(&$list_data, $model_name = null) {

        if (empty($list_data)) {

            return;
        }

        if (!isset($this->User)) {

            $this->loadModel('User');
        }

        $model_name = !empty($model_name) ? $model_name : $this->modelClass;
        $user_infos = array();
        foreach ($list_data as $k => $v) {

            if (empty($v[$model_name]['user'])) {

                $list_data[$k]['User'] = array();
                continue;
            }

            $user_id = (string) $v[$model_name]['user'];
            if (!isset($user_infos[$user_id])) {

                $user_info = $this->User->find('first', array(
                    'conditions' => array(
                        'id' => new MongoId($user_id),
                    ),
                ));
                $user_infos[$user_id] = !empty($user_info['User']) ? $user_info['User'] : array();
            }
            $list_data[$k]['User'] = $user_infos[$user_id];
        }
    }

    protected function setCommentObjectName(&$list_data, $model_name = null) {

        if (empty($list_data) || empty($model_name)) {

            return;
        }
        $parentModel = str_replace('Comment', '', $model_name);
        if (!$this->{$parentModel}) {

            $this->loadModel($parentModel);
        }

        foreach ($list_data as $k => $v) {

            $object_info = $this->{$parentModel}->find('first', array(
                'conditions' => array(
                    'id' => $v[$model_name][strtolower($parentModel)],
                ),
            ));
            $list_data[$k][$model_name]['Object'] = isset($object_info[$parentModel]) ? $object_info[$parentModel] : array();
        }
    }

    public function checkRequestObject() {
        $this->loadModel('ObjectType');
        if (isset($this->request->query['objectTypeId'], $this->request->query['objectId'])) {
            $object = $this->ObjectType->find('first', ['conditions' => [
                    'id' => new MongoId($this->request->query['objectTypeId']),
                    'status' => 2,
            ]]);
            if (!$object) {
                throw new NotFoundException(__('invalid_data'));
            }
            // get object type id (eg id of bank, place from table object_type)
            $this->objectTypeId = $this->request->query('objectTypeId');

            // get object id of object (eg id of bank, place)
            $this->objectId = $this->request->query['objectId'];

            $object['ObjectType']['name'] = Inflector::classify($object['ObjectType']['code']);
            $this->modelParentCode = Inflector::singularize($object['ObjectType']['code']);
            $this->modelParentModel = Inflector::classify($object['ObjectType']['code']);

            // load model of parent object
            if (!$this->$object['ObjectType']['name']) {
                $this->loadModel($object['ObjectType']['name']);
            }

            $this->objectInfo = $this->{$object['ObjectType']['name']}->findById(new MongoId($this->objectId))[$object['ObjectType']['name']];
            $info = $this->{$object['ObjectType']['name']}->find('first', ['conditions' => ['id' => new MongoId($this->objectId)]]);

            // set to view
            $this->set('activity_involve', $object['ObjectType']['name']);
            if ($this->Location) {
                $this->set('listCountryRegion', $this->Location->getListCountryRegion());
            }
            $this->set('objectInfo', $this->objectInfo);
            $this->set('activity_info', $info);

            $this->parentObject = $object['ObjectType']['name'];
            $this->dynamicModel = $object['ObjectType']['name'] . $this->modelClass;
            $this->dynamicObject = strtolower($object['ObjectType']['name']);
            $this->modelClass = $this->dynamicModel;
        } else {
            $object = $this->ObjectType->find('first', ['conditions' => ['name' => $this->modelClass]]);
            $this->objectTypeId = $object['ObjectType']['id'];
            $this->set('objectInfo', null);
        }
        $this->set('objectId', $this->objectId);
        $this->set('objectTypeId', $this->objectTypeId);
    }

    public function checkRequestObjectCoupon() {
        $this->loadModel('ObjectType');
        if (isset($this->request->query['objectTypeId'], $this->request->query['objectId'])) {
            $object = $this->ObjectType->find('first', ['conditions' => [
                'id' => new MongoId($this->request->query['objectTypeId']),
                'status' => 2,
            ]]);
            if (!$object) {
                throw new NotFoundException(__('invalid_data'));
            }
            // get object type id (eg id of bank, place from table object_type)
            $this->objectTypeId = $this->request->query('objectTypeId');

            // get object id of object (eg id of bank, place)
            $this->objectId = $this->request->query['objectId'];

            $object['ObjectType']['name'] = Inflector::classify($object['ObjectType']['code']);
            $this->modelParentCode = Inflector::singularize($object['ObjectType']['code']);
            $this->modelParentModel = Inflector::classify($object['ObjectType']['code']);

            // load model of parent object
            if (!$this->$object['ObjectType']['name']) {
                $this->loadModel($object['ObjectType']['name']);
            }

            $this->objectInfo = $this->{$object['ObjectType']['name']}->findById(new MongoId($this->objectId))[$object['ObjectType']['name']];
            $info = $this->{$object['ObjectType']['name']}->find('first', ['conditions' => ['id' => new MongoId($this->objectId)]]);

            // set to view
            $this->set('objectInfo', $this->objectInfo);
            $this->set('activity_info', $info);
        } else {
            $object = $this->ObjectType->find('first', ['conditions' => ['name' => $this->modelClass]]);
            $this->objectTypeId = $object['ObjectType']['id'];
            $this->set('objectInfo', null);
        }
        $this->set('objectId', $this->objectId);
        $this->set('objectTypeId', $this->objectTypeId);
    }

    public function checkRequestObjectComment() {
        $this->loadModel('ObjectType');
        if (isset($this->request->query['objectTypeId'])) {
            $object = $this->ObjectType->find('first', ['conditions' => [
                    'id' => new MongoId($this->request->query['objectTypeId']),
                    'status' => 2,
            ]]);
            if (!$object) {
                throw new NotFoundException(__('invalid_data'));
            }
            // get object type id (eg id of bank, place from table object_type)
            $this->objectTypeId = $this->request->query('objectTypeId');

            // get object id of object (eg id of bank, place)
            $this->objectId = $this->request->query('objectId');

            $object['ObjectType']['name'] = Inflector::classify($object['ObjectType']['code']) . $this->modelClass;
            $this->modelParentCode = Inflector::singularize($object['ObjectType']['code']);
            $this->modelParentModel = Inflector::classify($object['ObjectType']['code']);

            // load model of parent object
            if (!$this->$object['ObjectType']['name']) {
                $this->loadModel($object['ObjectType']['name']);
            }

            if ($this->objectId) {
                $this->objectInfo = $this->{$object['ObjectType']['name']}->findById(new MongoId($this->objectId))[$object['ObjectType']['name']];
                $info = $this->{$object['ObjectType']['name']}->find('first', ['conditions' => ['id' => new MongoId($this->objectId)]]);
                $this->set('activity_info', $info);
                $this->set('objectInfo', $this->objectInfo);
                $this->set('objectId', $this->objectId);
            }

            // set to view
            $this->set('activity_involve', $object['ObjectType']['name']);
            $this->set('objectTypeCode', Inflector::singularize($object['ObjectType']['code']));

            $this->parentObject = $object['ObjectType']['name'];
            $this->dynamicModel = $object['ObjectType']['name'];
            $this->dynamicObject = strtolower($object['ObjectType']['name']);
            $this->modelClass = $this->dynamicModel;
        } else {
            throw new NotFoundException(__('invalid_data'));
        }
        $this->set('objectTypeId', $this->objectTypeId);
    }

    public function checClassHasMethod($className, $methodName) {
        $class = new \ReflectionClass($className);
        return $class->getMethod($methodName);
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

    public function beforeRender() {
        parent::beforeRender();

        $this->set('active_controller', $this->name);
        $this->set('active_action', $this->action);
        $this->set('object_type_id', $this->object_type_id);

        $user = CakeSession::read('Auth.User');
        $this->set('user', $user);

        $allow_controllers = $user['allow_controllers'];
        $this->set('allow_controllers', $allow_controllers);

        $permissions = $user['permissions'];
        $this->set('permissions', $permissions);

        $active_permission_code = $this->getActivePermissionCode($this->name, $this->action, $this->request->query);
        $this->set('active_permission_code', $active_permission_code);
    }

    protected function getActivePermissionCode($controller, $action, $query_str) {

        if ($controller == 'Categories' || $controller == 'Comments') {

            $object_type_code = !empty($query_str['object_type_code']) ?
                    $query_str['object_type_code'] : 'unknown';
            $permission_code = $controller . '_' . $object_type_code . '_' . $action;
            return $permission_code;
        }

        return $controller . '/' . $action;
    }

    public function makeListMongoObjectId(&$list) {
        if (isset($list) && is_array($list) && count($list)) {
            foreach ($list AS $k => $v) {
                $list[$k] = new MongoId($v);
            }
        } else {
            return array();
        }
    }

    public function addTagByFullName($data, $model_name) {
        $this->logAnyFile("Start addTagByFullName, model_name: $model_name, id:" . $data['id'], __CLASS__ . '_' . __FUNCTION__);
        if (empty($data)) {
            return;
        }

        $taglow = strtolower($model_name) . 's';
        $arr_tag = array(
            'lang_code' => 'vi',
            'status' => 2,
            'name' => null,
            'name_ascii' => null,
            'object_type_code' => $taglow,
        );

        $data['name'] = trim($data['name']);
        $arr_update = array(
            'id' => null,
            'tags' => array(),);
        if (empty($data ['name'])) {
            $this->logAnyFile("Fail addTagByFullName, name: " . $data ['name'], __CLASS__ . '_' . __FUNCTION__);
            return;
        }

        $newName = trim($this->convert_vi_to_en($data['name']));
        $checkExist = false;
        if (!empty($data['tags'])) {
            $checkExist = $this->checkExistTag($data['tags'], strtolower($newName));
        }
        if ($checkExist) {
            $this->logAnyFile("Fail addTagByFullName, tag is exist", __CLASS__ . '_' . __FUNCTION__);
            return;
        }
        if (!empty($data['tags'])) {
            if (!is_array($data['tags'])) {
                array_push($arr_update['tags'], $data['tags']);
            } else {
                $arr_update['tags'] = $data['tags'];
            }
            array_push($arr_update['tags'], strtolower($newName));
        } else {
            $arr_update['tags'][0] = strtolower($newName);
        }
        $arr_update['id'] = new MongoId($data['id']);

        $this->logAnyFile($arr_update, __CLASS__ . '_' . __FUNCTION__);
        $this->logAnyFile("name: " . strtolower($newName), __CLASS__ . '_' . __FUNCTION__);

        $arr_update['tags'] = $this->trimData($arr_update['tags']);
        $arr_tag['name'] = $newName;
        $arr_tag['name_ascii'] = $newName;
        try {
            if (!$this->existTag($arr_tag['name'], $arr_tag['object_type_code'])) {
                $this->Tag->save($arr_tag);
            }
            $this->$model_name->save($arr_update);
        } catch (Exception $excep) {
            $this->logAnyFile("Error. Id:" . $arr_update['id'] . "error: $excep", __CLASS__ . '_' . __FUNCTION__);
        }
        $this->logAnyFile("End addTagByFullName, model_name: $model_name, id:" . $arr_update['id'], __CLASS__ . '_' . __FUNCTION__);
    }

    protected function existTag($name, $type) {

        $option = array('conditions' => array(
                'name' => $name,
                'object_type_code' => $type,
            ),
        );
        
        if (!isset($this->Tag)) {
            
            $this->loadModel('Tag');
        }
        $list_data = $this->Tag->find('first', $option);
        if (empty($list_data)) {

            return false;
        }

        return true;
    }

    protected function checkExistTag($tag, $name) {
        $check = false;
        if (is_array($tag)) {
            foreach ($tag as $newTag) {
                if ($name == $newTag) {
                    $check = true;
                }
            }
        } else {
            if ($name == $tag) {
                $check = true;
            }
        }
        return $check;
    }

    protected function trimData($data) {
        if (is_array($data)) {
            foreach ($data as $num => $ndata) {
                $data[$num] = trim($ndata);
            }
        } else {
            $data = trim($data);
        }
        return $data;
    }

    public function getMenus() {
        $user = $this->Auth->user();
        if (!$user)
            return;
        $pers = $user['permissions'];

        //get all menus from configure file
        $allMenus = Configure::read('S.Menus');
        $menuToView = [];

        foreach ($allMenus as $menu) {
            if (!isset($menu['child'])) { // is single
                if (!in_array($menu['controller'] . '/' . $menu['action'], $pers)) {
                    $menuToView[$menu['name']] = [
                        'icon' => $menu['icon'],
                        'url' => [
                            'controller' => $menu['controller'],
                            'action' => $menu['action'],
                            '?' => isset($menu['?']) ? $menu['?'] : null
                        ]
                    ];
                }
            } else { // if menu has child
                $menuChild = [];
                foreach ($menu['child'] as $child) {
                    if (!in_array($child['controller'] . '/' . $child['action'], $pers))
                        continue;
                    $menuChild = array_merge($menuChild, [
                        $child['name'] => [
                            'url' => [
                                'controller' => $child['controller'],
                                'action' => $child['action'],
                                '?' => isset($child['?']) ? $child['?'] : null
                            ]
                        ]
                    ]);
                }
                if ($menuChild) {
                    $menuToView[$menu['name']] = [
                        'icon' => $menu['icon'],
                        'url' => '#',
                        'child' => $menuChild
                    ];
                }
            }
        }
        $this->set('menus', $menuToView);
    }

    protected function format_report_date($date){
        if(!empty($date)){
            return substr($date, 6, 2).'-'.substr($date, 4, 2).'-'.substr($date, 0, 4);
        }
        return $date;
    }

    protected function format_number($number)
    {
        return number_format($number,0,',','.');
    }

    protected function get_current_quarter($month='',$nice=false)
    {
        if($month == '') {
            $curMonth = date("m", time());
        }else{
            $curMonth = $month;
        }
        $curQuarter = ceil($curMonth/3);
        if($nice){
            return $this->nice_date($curQuarter);
        }
        return $curQuarter;
    }

    protected function get_quarter_start_month($quarter)
    {
        switch ($quarter) {
            case '01':
                return $this->nice_date(1);
                break;
            
            case '02':
                return $this->nice_date(4);
                break;
            
            case '03':
                return $this->nice_date(7);
                break;
            
            case '04':
                return $this->nice_date(10);
                break;
            
            default:
                # code...
                break;
        }
    }

    protected function get_quarter_end_month($quarter)
    {
        switch ($quarter) {
            case '01':
                return $this->nice_date(3);
                break;
            
            case '02':
                return $this->nice_date(6);
                break;
            
            case '03':
                return $this->nice_date(9);
                break;
            
            case '04':
                return $this->nice_date(12);
                break;
            
            default:
                # code...
                break;
        }
    }

    protected function get_quarter_end_date($quarter)
    {
        switch ($quarter) {
            case '01':
                return $this->nice_date(31); //31-03
                break;
            
            case '02':
                return $this->nice_date(30); //30-06
                break;
            
            case '03':
                return $this->nice_date(30); //30-09
                break;
            
            case '04':
                return $this->nice_date(31); //31-12
                break;
            
            default:
                # code...
                break;
        }
    }

    protected function nice_date($str)
    {
        return str_pad($str, 2, 0, STR_PAD_LEFT);
    }

    protected function get_last_query($model)
    {
        $dbo = $model->getDatasource();
        $logs = $dbo->getLog();
        $lastLog = end($logs['log']);
        return $lastLog['query'];
    }

    protected function get_week_number($unix_time='')
    {
        if(empty($unix_time)){
            return $this->nice_date(date('W'));
        }else{
            return $this->nice_date(date('W',$unix_time));
        }
    }

    /**
    * @author Ungnv
    */
    protected function getYearReport()
    {
        if( isset($this->request->query['year']) ){
            return $this->request->query['year'];
        }else{
            return date('Y');
        }
    }

    /**
    * @author Ungnv
    */
    protected function getMonthReport()
    {
        if( isset($this->request->query['month']) ){
            return $this->request->query['month'];
        }else{
            return date('m');
        }
    }

    /**
    * @author Ungnv
    */
    protected function getDayReport()
    {
        if( isset($this->request->query['day']) ){
            return $this->request->query['day'];
        }else{
            return date('d');
        }
    }

    /**
    * Chuyển mảng nhiều chiều thành 1 chiều
    * @author Ungnv
    * @param $array
    */
    protected function arrayFlatten($array) { 
        if (!is_array($array)) { 
            return FALSE; 
        } 
        $result = array(); 
        foreach ($array as $key => $value) { 
            if (is_array($value)) { 
                $result = array_merge($result, $this->arrayFlatten($value)); 
            } 
            else { 
                $result[] = $value; 
            } 
        } 
        return $result; 
    }

    /**
    * @author: Ungnv
    * @param: mảng 27 số của bộ kết quả 1 ngày
    * @return: phân tách ra từng chữ số, trả về mảng gồm 107 phần tử
    */
    protected function parseResult($result)
    {
        $returnArray = array();
        if(!empty($result)){
            foreach ($result as $item) {
                $itemToArray = array_map('intval', str_split($item));
                $returnArray = array_merge($returnArray, $itemToArray);
            }
        }
        return $returnArray;
    }

    function getAllLoto($result){
        $lotoArray = array();
        if(!empty($result)){
            foreach ($result as $item) {
                $itemToArray = substr($item,-2);
                $lotoArray[] = $itemToArray;
            }
        }
        //$lotoArray = array_values(array_unique($lotoArray));
        return array_values($lotoArray);
    }

    protected function setDateRange(&$startdate, &$enddate)
    {
        if( !empty($startdate) && !empty($enddate) ){
            if($startdate <= $enddate){
                $this->request->query['from_date'] = $startdate = $startdate;
                $this->request->query['to_date'] = $enddate = $enddate;
            }
        }elseif( !empty($startdate) ){
            if( strtotime($startdate) <= mktime( 23,59,59,date('m'),date('j'),date('Y') ) ){
                //$startdate .= ' 00:00:00';
                $this->request->query['to_date'] = $enddate = date('d-m-Y 23:59:59');
            }
        }elseif( !empty($enddate) ){
            if( strtotime($enddate) <= mktime( 23,59,59,date('m'),date('j'),date('Y') ) ){
                $this->request->query['from_date'] = $startdate = date('d-m-Y 00:00:00');
                //$enddate .= ' 23:59:59';
            }
        }else{
            $this->request->query['from_date'] = $startdate = date('d-m-Y 00:00:00',strtotime('7 days ago'));
            $this->request->query['to_date'] = $enddate = date('d-m-Y 23:59:59');
        }
    }

    protected function setConditions(&$options, $from_date, $to_date, $mobile) {

        $options['order']['modified'] = 'DESC';
        $options['conditions']['mobile'] = $mobile;
        if( !empty( $this->request->query('package') ) ) {
            $options['conditions']['package'] = $this->request->query('package');
        }
        if( !empty( $this->request->query('action') ) ) {
            $options['conditions']['action'] = $this->request->query('action');
        }
        if( isset( $this->request->query['status'] ) && strlen($this->request->query['status']) > 0 ) {
            $options['conditions']['status'] = (int)$this->request->query('status');
        }
        if (!empty($from_date)) {
            $options['conditions']['created']['$gte'] = new MongoDate(strtotime($from_date));
        }

        if (!empty($to_date)) {
            $options['conditions']['created']['$lte'] = new MongoDate(strtotime($to_date));
        }
    }

    protected function setConvertDate(&$list_data,$model_name='AppModel') {

        if (empty($list_data)) {

            return;
        }

        $datetime_fields = array(
            'modified',
            'created',
        );

        foreach ($list_data as $k => $v) {

            foreach ($datetime_fields as $field) {

                if (isset($v[$model_name][$field]) && $v[$model_name][$field] instanceof MongoDate) {

                    $list_data[$k][$model_name][$field] = date('d-m-Y H:i:s', $v[$model_name][$field]->sec);
                }
            }
        }
    }


    protected function checkDate($date) {
        $d = DateTime::createFromFormat('d-m-Y', $date);
        return $d && $d->format('d-m-Y') == $date;
    }

    protected function checkYear($year) {
        $year = (int)$year;
        if ($year > 1000 && $year < 2100)
        {
            return true;
        }else{
            return false;
        }
    }

    protected function isMobifoneNumber($number)
    {
        if(!empty($number)){
            if (preg_match('/^(8412[01268]|849[03])[0-9]{7}$/', $number)) {
                return true;
            }
        }
        return false;
    }

    
    protected function isVinaPhoneNumber($number)
    {
        if(!empty($number)){
            if (preg_match('/^(8412[34579]|849[14])[0-9]{7}$/', $number)) {
                return true;
            }
        }
        return false;
    }

    
    protected function isViettelNumber($number)
    {
        if(!empty($number)){
            if (preg_match('/^(8416[23456789]|849[678])[0-9]{7}$/', $number)) {
                return true;
            }
        }
        return false;
    }

    
    public function exportCsv($module_name, $folder_name, $arr_data, $file_name)
    {
        $this->autoRender = false;
        $year = date('Y');
        $month = date('m');
        $day = date('d');
        $data_root_name = Configure::read("sysconfig.$module_name.data_file_root");

        $folder_structure = array(
            $data_root_name,
            $folder_name,
            $year . $month,
            $day,
        );
        $folder_path = APP.'data_files';
        $absolute = 'data_files';

        foreach ($folder_structure as $item) {

            $folder_path .= DS . $item;

            $absolute .= DS . $item;

            $folder = new Folder($folder_path, false, 0777);
            if (!$folder->inPath($folder_path)) {

                $folder = new Folder($folder_path, true, 0777);
            }
        }

        $csv_filename = $folder_path.DS.$file_name;
        $absolute_filename = $absolute.DS.$file_name;

        $file = fopen ($csv_filename, "w");

        foreach ($arr_data as $line){
            debug($line);
            fputcsv($file,$line);
        }

        fclose($file);

        

        if (DIRECTORY_SEPARATOR == '\\') {
            $absolute_filename = str_replace('\\', '/', $absolute_filename);
            //$absolute_filename = str_replace('\\\\', '/', $absolute_filename);
        }

        return $absolute_filename;

    }

    public function getAllPackage()
    {
        
        $returnArray = Configure::read('sysconfig.App.base_package');
        $allRegions = $this->Region->find('all');
        if (!empty($allRegions)) {
            foreach ($allRegions as $region) {
                $region = $region['Region'];
                $returnArray[$region['code']] = 'XS '.$region['name'];
            }
        }
        return $returnArray;
    }

}
