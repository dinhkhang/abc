<?php

App::uses('Component', 'Controller');
App::uses('Folder', 'Utility');
App::uses('File', 'Utility');

class ImportContentComponent extends Component {

        public $controller = '';
        public $location_models = array('Country', 'Region', 'Location');
        public $location_required = array(
            'region',
            'country_code',
            'name',
            'address',
            'latitude',
            'longitude',
        );
        public $file_models = array('FileManaged');
        public $file_required = array('logo', 'thumbnails');
        public $file_module_name = '';
        public $components = array('FileCommon');
        public $default_settings = array(
            'file_module_name' => '',
            'location_models' => array('Country', 'Region', 'Location'),
            'location_required' => array('region', 'country_code', 'name', 'address', 'latitude', 'longitude'),
            'file_models' => array('FileManaged'),
            'file_required' => array('logo', 'thumbnails'),
            'remove_fields' => array(), // những fields sẽ không xử lý khi import - những trường nhập sai
            'additional_schemas' => array(), // mảng chứa cấu trúc thêm vào để fill đầy như schema đã thiết kế
            'wrong_fields' => array(
                'location.addrress' => 'location.address',
                'addrress' => 'address',
            ),
            'clean_region_field' => array(
                'City',
                'city',
                'Province',
                'province',
                'Provice',
                'provice',
                'Capital',
                'capital',
                'tp.',
                'TP.',
                '.',
            ),
        );

        const GEO_SERVICE = 'http://ws.geonames.org/searchJSON';

        public $geo_params = array(
            'country' => 'VN',
            'maxRows' => 1,
            'username' => 'trungpolit',
            'name' => '',
        );
//        const GEO_SERVICE = 'https://maps.googleapis.com/maps/api/geocode/json';
//
//        public $geo_params = array(
//            'key' => 'AIzaSyDlItEAJMZ4WR7IL2V-FTg4KS4-9kg_Kaw',
//            'country' => 'VN',
//            'address' => '',
//        );
        public $object_type_id = null;

        public function initialize(\Controller $controller) {

                parent::initialize($controller);

                $this->controller = $controller;
                if (!isset($this->controller->import_error)) {

                        $this->controller->import_error = array();
                }

                if (empty($this->object_type_id)) {

                        if (!isset($this->controller->ObjectType)) {

                                $this->controller->loadModel('ObjectType');
                        }

                        $object_type = $this->controller->ObjectType->find('first', array(
                            'conditions' => array(
                                'code' => $this->controller->{$this->controller->modelClass}->useTable,
                            ),
                        ));

                        $this->object_type_id = $object_type['ObjectType']['id'];
                }

                $this->settings = Hash::merge($this->default_settings, $this->settings);
        }

        public function init(&$save_data) {

                if (!empty($this->settings['remove_fields'])) {

                        foreach ($this->settings['remove_fields'] as $field) {

                                unset($save_data[$field]);
                        }
                }

                if (!empty($this->settings['wrong_fields'])) {

                        foreach ($this->settings['wrong_fields'] as $wrong_path => $correct_path) {

                                $wrong = $this->makeIndexArray($wrong_path);
                                $correct = $this->makeIndexArray($correct_path);

                                $evaluate = eval('return isset($save_data' . $wrong . ');');
                                if ($evaluate) {

                                        eval('return $save_data' . $correct . ' = $save_data' . $wrong . ';');
                                        eval('unset($save_data' . $wrong . ');');
                                }
                        }
                }

                $this->fixLocationsField($save_data);

                if (!empty($this->settings['additional_schemas'])) {

                        $save_data = Hash::merge($save_data, $this->settings['additional_schemas']);
                }

                $save_data['status'] = 1;
                $save_data['lang_code'] = 'en';
        }

        public function saveLocation(&$save_data, $data, $required = true) {

                // Nạp vào các Model liên quan
                $this->loadModel($this->location_models);

                $is_valid = 1;
                if ($required && !$this->checkRequired($data, 'location', $this->location_required)) {

                        $is_valid = 0;
                }

                if (!$is_valid) {

                        return false;
                }

                // thực hiện insert country
                $check_country = $this->controller->Country->find('first', array(
                    'conditions' => array(
                        'code' => $data['location']['country_code'],
                    ),
                ));

                // nếu không tồn tại thực hiện insert
                if (empty($check_country)) {

                        $country_data = array(
                            'name' => $data['location']['country_code'],
                            'code' => $data['location']['country_code'],
                        );

                        $this->controller->Country->create();
                        if (!$this->controller->Country->save($country_data)) {

                                $this->controller->import_error = array_merge($this->controller->import_error, $this->controller->Country->validationErrors);
                                return false;
                        }
                }

                // thực hiện insert region
                $check_region = $this->controller->Region->find('first', array(
                    'conditions' => array(
                        'name' => array(
                            '$regex' => new MongoRegex("/^" . mb_strtolower($data['location']['region']) . "$/i"),
                        ),
                        'country_code' => $data['location']['country_code'],
                    ),
                ));

                if (empty($check_region)) {

                        $this->controller->import_error[] = 'Không tồn tại trong hệ thống tỉnh/thành phố là "' . $data['location']['region'] . '", sửa lại file nội dung file js cho đúng với tỉnh/thành phố trong hệ thống';
                        return false;
                }

                // nếu không tồn tại thực hiện insert
//                if (empty($check_region)) {
//
//                        // call service để lấy lng và lat dựa vào tên name của region
//                        $regionLngLat = $this->getRegionLngLat($data['location']['region']);
//
//                        $region_data = array(
//                            'name' => $data['location']['region'],
//                            'code_name' => $this->getCodeName($data['location']['region']),
//                            'country_code' => $data['location']['country_code'],
//                            'status' => 2,
//                            'loc' => array(
//                                'type' => Configure::read('sysconfig.App.GeoJSON_type'),
//                                'coordinates' => array(
//                                    $regionLngLat['lng'], $regionLngLat['lat'],
//                                ),
//                            ),
//                            'location' => '',
//                        );
//
//                        $this->controller->Region->create();
//                        if (!$this->controller->Region->save($region_data)) {
//
//                                $this->controller->import_error = array_merge($this->controller->import_error, $this->controller->Region->validationErrors);
//                                return false;
//                        }
//
//                        $region_id = $this->controller->Region->getLastInsertID();
//                } else {
//
//                        $region_id = $check_region['Region']['id'];
//                }
//                
                $region_id = $check_region['Region']['id'];
                // thực hiện insert location
                $location_data = array(
                    'country_code' => $data['location']['country_code'],
                    'region' => new MongoId($region_id),
                    'name' => $data['location']['name'],
                    'address' => $data['location']['address'],
                    'object_type' => new MongoId($this->object_type_id),
//                    'latitude' => $data['location']['latitude'],
//                    'longitude' => $data['location']['longitude'],
                    'loc' => array(
                        'type' => Configure::read('sysconfig.App.GeoJSON_type'),
                        'coordinates' => array(
                            $data['location']['longitude'], $data['location']['latitude']
                        ),
                    ),
                    'status' => 2,
                );

                $this->controller->Location->create();
                if (!$this->controller->Location->save($location_data)) {

                        $this->controller->import_error = array_merge($this->controller->import_error, $this->controller->Location->validationErrors);
                        return false;
                }

                $location_id = $this->controller->Location->getLastInsertID();

                unset($save_data['location']);

                $save_data['location'] = array(
                    '_id' => new MongoId($location_id),
                    'country_code' => $data['location']['country_code'],
                    'region' => new MongoId($region_id),
                    'object_type' => new MongoId($this->object_type_id),
                );
                $save_data['loc'] = $location_data['loc'];

                return true;
        }

        public function saveFiles(&$save_data, $dir_path, $data, $required = true) {

                // Nạp vào các Model liên quan
                $this->loadModel($this->file_models);

                $is_valid = 1;
                if ($required && !$this->checkRequired($data, 'files', $this->settings['file_required'])) {

                        $is_valid = 0;
                        return $is_valid;
                }

                // validate sự tồn tại của files
                if (empty($data['files']) || !is_array($data['files'])) {

                        return $is_valid;
                }

                foreach ($data['files'] as $k => $v) {

                        // nếu bắt buộc thì mới trả về false khi thực hiện validate file
                        // không thì bỏ qua, mục tiêu ưu tiên nội dung text
                        if ($required && !$this->validateFiles($dir_path, $k, $v)) {

                                $is_valid = 0;
                        }
                }

                if (!$is_valid) {

                        return $is_valid;
                }

                $save_data['files'] = array();
                foreach ($data['files'] as $k => $v) {

                        $this->proccessFiles($save_data, $dir_path, $k, $v);
                }

                return true;
        }

        public function getRegionLngLat($region) {

                $geo_service = self::GEO_SERVICE;
                App::uses('HttpSocket', 'Network/Http');

                $HttpSocket = new HttpSocket();

                // chuẩn hóa region
                $region = $this->cleanRegion($region);

                $this->geo_params['name'] = trim($region);
                $results = $HttpSocket->get($geo_service, $this->geo_params);
                if (!$results->isOk()) {

                        $this->log('Call service lấy lng và lat bị lỗi đối với region = ' . $region, 'import');
                        $this->log($results, 'import');
                }

//                $this->geo_params['address'] = $region . ',' . $this->geo_params['country'];
//                unset($this->geo_params['country']);
//                $results = $HttpSocket->get($geo_service, $this->geo_params);

                if (!$results->isOk()) {

                        $this->log('Call service lấy lng và lat bị lỗi đối với region = ' . $region, 'import');
                        $this->log($results, 'import');
                }

                $json = json_decode($results->body, true);

                if (empty($json['geonames'][0]['lng']) || empty($json['geonames'][0]['lat'])) {

                        $this->log('Thiếu lng hoặc lat đối với region = ' . $region, 'import');
                        $this->log($results, 'import');

                        return array(
                            'lng' => '',
                            'lat' => '',
                        );
                }

                $result = array(
                    'lng' => $json['geonames'][0]['lng'],
                    'lat' => $json['geonames'][0]['lat'],
                );

                return $result;
        }

        protected function cleanRegion($region) {

                if (!empty($this->settings['clean_region_field'])) {

                        foreach ($this->settings['clean_region_field'] as $clean) {

                                $region = str_replace($clean, '', $region);
                        }
                }

                return trim($region);
        }

        protected function validateFiles($dir_path, $type, $paths) {

                $is_valid = 1;
                // kiểm tra sự tồn tại của file
                if (in_array($type, $this->settings['file_required'])) {

                        foreach ($paths as $path) {

                                $file_path = $dir_path . DS . trim($path);
                                // hot fix
//                                if (strpos($file_path, '.jpg') !== false) {
//
//                                        $file_path = str_replace('.jpg', '.jpeg', $file_path);
//                                }
                                // thực hiện support cho môi trường windows
                                if (DIRECTORY_SEPARATOR == '\\') {

//                                        $file_path = str_replace('\\', '/', $file_path);
                                        $file_path = str_replace('/', '\\', $file_path);
                                }

                                if (!$this->checkFileExist($type, $file_path)) {

                                        $is_valid = 0;
                                }
                        }
                } else {

                        foreach ($paths as $path) {

                                $path = trim($path);
                                if (!strlen($path)) {

                                        continue;
                                }

                                $file_path = $dir_path . DS . trim($path);

                                // hot fix
//                                if (strpos($file_path, '.jpg') !== false) {
//
//                                        $file_path = str_replace('.jpg', '.jpeg', $file_path);
//                                }
                                // thực hiện support cho môi trường windows
                                if (DIRECTORY_SEPARATOR == '\\') {

//                                        $file_path = str_replace('\\', '/', $file_path);
                                        $file_path = str_replace('/', '\\', $file_path);
                                }

                                if (!$this->checkFileExist($type, $file_path)) {

                                        $is_valid = 0;
                                }
                        }
                }

                return $is_valid;
        }

        protected function proccessFiles(&$save_data, $dir_path, $type, $paths) {

                // thực hiện move và lưu file vào database
                foreach ($paths as $path) {

                        $path = trim($path);
                        if (!in_array($type, $this->settings['file_required']) && strlen($path) <= 0) {

                                continue;
                        }

                        $file_path = $dir_path . DS . $path;
                        // hot fix
//                        if (strpos($file_path, '.jpg') !== false) {
//
//                                $file_path = str_replace('.jpg', '.jpeg', $file_path);
//                        }
                        // thực hiện support cho môi trường windows
                        if (DIRECTORY_SEPARATOR == '\\') {

//                                $file_path = str_replace('/', '\\', $file_path);
                                $file_path = str_replace('/', '\\', $file_path);
                        }

                        $file = new File($file_path);
                        if (!$file->exists()) {

                                continue;
                        }

                        $file_ext = $file->ext();
                        $file_name = $file->name() . $this->FileCommon->generateRandomLetters(5) . '.' . $file_ext;

                        $mime = $file->mime();
                        if (empty($mime)) {

                                $mime = $this->controller->getMimeType($file_name);
                        }

                        $target_file_path = $this->FileCommon->generateFolderStructure($this->file_module_name, $mime) . $file_name;
                        $file->copy(APP . $target_file_path);

                        $file_data = array(
                            'name' => $file_name,
                            'uri' => $target_file_path,
                            'mime' => $file->mime(),
                            'size' => $file->size(),
                            'status' => 1,
                        );
                        $this->controller->FileManaged->create();
                        $this->controller->FileManaged->save($file_data);
                        $save_data['files'][$type][] = new MongoId($this->controller->FileManaged->getLastInsertID());
                }
        }

        protected function checkFileExist($type, $file_path) {

                $file = new File($file_path, false, 0755);
                if (!$file->exists()) {

                        $this->controller->import_error[] = 'Không tồn tại file theo đường dẫn ' . $file_path . ' trong ' . 'files.' . $type;
                        return false;
                }

                return true;
        }

        protected function loadModel($models) {

                foreach ($models as $model) {

                        if (!isset($this->controller->$model)) {

                                $this->controller->loadModel($model);
                        }
                }
        }

        protected function checkRequired($data, $field, $children_fields = array()) {

                $is_valid = 1;
                if (empty($data[$field])) {

                        $this->controller->import_error[] = 'Không tồn tại thông tin trong ' . $field;
                        $is_valid = 0;
                }

                if (!empty($data[$field]) && !is_array($data[$field])) {

                        $this->controller->import_error[] = 'Thông tin trong ' . $field . ' không đúng định dạng';
                        $is_valid = 0;
                }

                if (empty($children_fields)) {

                        return $is_valid;
                }

                foreach ($children_fields as $v) {

                        if (empty($data[$field][$v])) {

                                $this->controller->import_error[] = 'Không tồn tại thông tin trong ' . $field . '.' . $v;
                                $is_valid = 0;
                        } elseif (is_string($data[$field][$v]) && strlen(trim($data[$field][$v])) <= 0) {

                                $this->controller->import_error[] = 'Chứa chuỗi rỗng trong ' . $field . '.' . $v;
                                $is_valid = 0;
                        }

// đối với trường hợp đặc biệt $field = "files", thì các trường con của "files" bắt buộc là array
//                        if ($field == 'files' && !$this->checkRequiredForFile($field, $v, $data[$field][$v])) {
//                                
//                        }
                }

                return $is_valid;
        }

        protected function checkRequiredForFile($field, $child_field, $value) {

                $is_valid = 1;
                if (!is_array($value)) {

                        $this->controller->import_error[] = 'Thông tin không đúng định dạng dữ liệu trong ' . $field . '.' . $child_field;
                        $is_valid = 0;

                        return $is_valid;
                }

                foreach ($value as $k => $v) {

                        $v = trim($v);
                        if (!strlen($v)) {

                                $this->controller->import_error[] = 'Thông tin không đúng định dạng dữ liệu trong ' . $field . '.' . $child_field . '.' . $k;
                                $is_valid = 0;
                        }
                }

                return $is_valid;
        }

        /* convert_vi_to_en method
         * hàm chuyền đổi tiếng việt có dấu sang tiếng việt không dấu
         * @param string $str
         * @return string
         */

        protected function convert_vi_to_en($str) {

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

                return $str;
        }

        protected function getCodeName($str) {

                $str = $this->convert_vi_to_en($str);
                $code_name = strtolower(str_replace(' ', '', $str));

                return $code_name;
        }

        public function checkExist($item) {

                $name = $item['name'];
                $check_exist = $this->controller->{$this->controller->modelClass}->find('first', array(
                    'conditions' => array(
                        'name' => array(
                            '$regex' => new MongoRegex("/" . mb_strtolower($name) . "/i"),
                        ),
                    ),
                ));

                if (!empty($check_exist)) {

                        return true;
                }

                return false;
        }

        public function fixLocationsField(&$item) {

                if (empty($item['location'])) {

                        return;
                }

                if (empty($item['location']['address'])) {

                        return;
                }

                if (empty($item['location']['country_code'])) {

                        $item['location']['country_code'] = 'VN';
                }

                if (empty($item['location']['region'])) {

                        $region = $this->getRegionFromAddrress($item['location']['address']);
                        $region = $this->cleanRegion($region);
                        $item['location']['region'] = $region;
                }
        }

        protected function getRegionFromAddrress($address) {

                $extract = explode(',', $address);
                $count = count($extract);

                if ($count > 1) {

                        return trim($extract[$count - 1]);
                } else {

                        return trim($extract[0]);
                }
        }

        protected function makeIndexArray($path) {

                if (empty($path)) {

                        return;
                }

                $extract = explode('.', $path);
                $index_path = '';
                foreach ($extract as $v) {

                        if (is_numeric($v)) {

                                $index_path .= '[' . $v . ']';
                        } else {

                                $index_path .= '["' . $v . '"]';
                        }
                }

                return $index_path;
        }

}
