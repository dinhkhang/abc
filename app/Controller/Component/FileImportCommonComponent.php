<?php

App::uses('Component', 'Controller');
App::uses('Folder', 'Utility');
App::uses('File', 'Utility');
App::uses('HttpSocket', 'Network/Http');

class FileImportCommonComponent extends Component {

    public $controller = '';

    const DIR_PATH_IN_EDITOR = 'files'; // thư mục 
    const MAX_DOWNLOAD_TIME_OUT = 600;

    public function initialize(\Controller $controller) {

        parent::initialize($controller);

        $this->controller = $controller;
    }

    public function getFiles($request_data_file) {

        if (empty($request_data_file)) {

            return false;
        }

        if (!isset($this->controller->FileManaged)) {

            $this->controller->loadModel('FileManaged');
        }

        $results = array();

        foreach ($request_data_file as $type => $file) {

            if (empty($file)) {

                continue;
            }

            foreach ($file as $v) {

                if ($v instanceof MongoId) {

                    $file_obj = $this->controller->FileManaged->find('first', array(
                        'conditions' => array(
                            'id' => $v,
                        ),
                        'fields' => array(
                            'name', 'size', 'mime', 'status', 'uri',
                        ),
                    ));

                    $results[$type][] = !empty($file_obj['FileManaged']) ?
                            json_encode($file_obj['FileManaged']) : json_encode(array());
                }
            }
        }

        return $results;
    }

    /**
     * autoSetFiles
     * Thực hiện lấy ra thông tin chi tiết của file từ FileManaged
     * 
     * @param reference array $request_data
     * 
     * @return boolean
     */
    public function autoSetFiles(&$request_data, $options = array()) {

        if (empty($request_data)) {

            return false;
        }

        if (empty($request_data['files']) || !is_array($request_data['files'])) {

            $request_data['files'] = array();
            return;
        }

        if (!isset($this->controller->FileManaged)) {

            $this->controller->loadModel('FileManaged');
        }

        foreach ($request_data['files'] as $type => $file) {

            if (empty($file)) {

                continue;
            }

            foreach ($file as $k => $v) {

                if ($v instanceof MongoId) {

                    $file_obj = $this->controller->FileManaged->find('first', array(
                        'conditions' => array(
                            'id' => $v,
                        ),
                        'fields' => array(
                            'name', 'size', 'mime', 'status', 'uri',
                        ),
                    ));

                    $request_data['files'][$type][$k] = !empty($file_obj['FileManaged']) ?
                            json_encode($file_obj['FileManaged']) : json_encode(array());
                } else {

                    $request_data['files'][$type][$k] = json_encode(array());
                }
            }
        }

        $this->autoSetFilesRecursive($request_data, $options);
    }

    protected function autoSetFilesRecursive(&$request_data, $options = array()) {

        if (empty($options['recursive']) || empty($options['recursive_path'])) {

            return;
        }

        if (!is_array($options['recursive_path'])) {

            $options['recursive_path'] = array($options['recursive_path']);
        }

        foreach ($options['recursive_path'] as $recursive_path) {

            $check = Hash::check($request_data, $recursive_path);
            if (!$check) {

                continue;
            }
            $extract_data = Hash::extract($request_data, $recursive_path);
            if (strpos($recursive_path, '{n}') !== false) {

                foreach ($extract_data as $k => $v) {

                    $fix_path = str_replace('{n}', $k, $recursive_path);
                    $index = explode('.', $fix_path);
                    foreach ($v as $k1 => $v1) {

                        $path_index = $this->joinRecursivePath($index);
                        $path_index = $path_index . '["' . $k1 . '"]';

                        foreach ($v1 as $k2 => $v2) {

                            $fix_path_index = $path_index . '[' . $k2 . ']';
                            if ($v2 instanceof MongoId) {

                                $file_obj = $this->controller->FileManaged->find('first', array(
                                    'conditions' => array(
                                        'id' => $v2,
                                    ),
                                ));

                                $file_json = !empty($file_obj['FileManaged']) ?
                                        json_encode($file_obj['FileManaged']) : json_encode(array());
                                $express = '$request_data' . $fix_path_index . '= $file_json;';
                                eval($express);
                            } else {

                                $express = '$request_data' . $fix_path_index . '= json_encode(array());';
                                eval($express);
                            }
                        }
                    }
                }
            } else {

                foreach ($extract as $k => $v) {

                    $index = explode('.', $recursive_path);
                    $path_index = $this->joinRecursivePath($index);
                    $path_index = $path_index . '["' . $k . '"]';

                    foreach ($v as $k1 => $v1) {

                        $fix_path_index = $path_index . '[' . $k1 . ']';
                        if ($v1 instanceof MongoId) {

                            $file_obj = $this->controller->FileManaged->find('first', array(
                                'conditions' => array(
                                    'id' => $v1,
                                ),
                            ));

                            $file_json = !empty($file_obj['FileManaged']) ?
                                    json_encode($file_obj['FileManaged']) : json_encode(array());
                            $express = '$request_data' . $fix_path_index . '= $file_json;';
                            eval($express);
                        } else {

                            $express = '$request_data' . $fix_path_index . '= json_encode(array());';
                            eval($express);
                        }
                    }
                }
            }
        }
    }

    protected function joinRecursivePath($path = array()) {

        if (empty($path)) {

            return;
        }

        $join_path = '';
        foreach ($path as $v) {

            if (is_numeric($v)) {

                $join_path .= '[' . $v . ']';
            } else {

                $join_path .= '["' . $v . '"]';
            }
        }

        return $join_path;
    }

    /**
     * generateFolderStructure
     * Thực hiện tạo ra cấu trúc thư mục lưu trữ [Tên module/Tên ext/nămtháng/ngày]
     * 
     * @param string $module_name
     * @param string $ext
     * 
     * @return string
     */
    public function generateFolderStructure($module_name, $mime, $absolute = false) {

        $data_root_name = Configure::read('sysconfig.App.data_file_root');

        $pretty_mime = strtolower($mime);
        $extract_mime = explode('/', $pretty_mime);

        $year = date('Y');
        $month = date('m');
        $day = date('d');
        $block = rand(1, 2000);
        $folder_structure = array(
            $data_root_name,
            $module_name,
            $extract_mime[0],
            $year . $month,
            $day,
            $block,
        );
        $folder_path = APP;

        foreach ($folder_structure as $item) {

            $folder_path .= DS . $item;
            $folder = new Folder($folder_path, false, 0777);
            if (!$folder->inPath($folder_path)) {

                $folder = new Folder($folder_path, true, 0777);
            }
        }

        if ($absolute) {

            return $folder_path . DS;
        }

        return str_replace(APP, '', $folder_path . DS);
    }

    public function process($request_data_file, $module_name) {

        if (empty($request_data_file) || !is_array($request_data_file)) {

            return false;
        }

        $results = array();
        foreach ($request_data_file as $k => $v) {

            if (empty($v)) {

                continue;
            }

            $file_ids = $this->moveFromTmp($v, $module_name);
            if ($file_ids !== false) {

                $results[$k] = $file_ids;
            }
        }

        return $results;
    }

    /**
     * autoProcess
     * Tự động xử lý liên quan tới files, gọi trước khi thực hiện save vào database
     * 
     * @param reference array $save_data
     * @param string $module_name - Là tên thư mục lưu trữ dành cho Module, mặc định đọc trong 'sysconfig.' . $this->controller->name . '.data_file_root'
     * 
     * @return boolean
     * @throws CakeException
     */
    public function autoProcess(&$save_data, $module_name = null, $options = array()) {

        if (empty($module_name)) {

            $module_name = Configure::read('sysconfig.' . $this->controller->name . '.data_file_root');
        }

        if (empty($module_name)) {

            throw new CakeException(__('Invalid sysconfig, make sure that %s was defined', 'sysconfig.' . $this->controller->name . '.data_file_root'));
        }

        if (empty($save_data)) {

            return false;
        }

        if (empty($save_data['files']) || !is_array($save_data['files'])) {

            $save_data['files'] = "";
            return;
        }

        foreach ($save_data['files'] as $type => $file) {

            $file_ids = $this->moveFromTmp($file, $module_name);
            if ($file_ids === false) {

                unset($save_data['files'][$type]);
                continue;
            }

            $save_data['files'][$type] = $file_ids;
        }

        $this->autoProcessRecursive($save_data, $module_name, $options);
    }

    protected function autoProcessRecursive(&$save_data, $module_name = null, $options = array()) {

        if (empty($options['recursive']) || empty($options['recursive_path'])) {

            return;
        }

        if (!is_array($options['recursive_path'])) {

            $options['recursive_path'] = array($options['recursive_path']);
        }

        foreach ($options['recursive_path'] as $recursive_path) {

            $check = Hash::check($save_data, $recursive_path);
            if (!$check) {

                continue;
            }

            $extract_save_data = Hash::extract($save_data, $recursive_path);
            if (strpos($recursive_path, '{n}') !== false) {

                foreach ($extract_save_data as $k => $v) {

                    $fix_path = str_replace('{n}', $k, $recursive_path);
                    $index = explode('.', $fix_path);

                    foreach ($v as $kk => $vv) {

                        $path_index = '["' . implode('"]["', $index) . '"]';
                        $path_index = $path_index . '["' . $kk . '"]';

                        $file_ids = $this->moveFromTmp($vv, $module_name);
                        if ($file_ids === false) {

                            $express = 'unset($save_data' . $path_index . ');';
                            eval($express);
                            continue;
                        }

                        $express = '$save_data' . $path_index . '= $file_ids;';
                        eval($express);
                    }
                }
            } else {

                foreach ($extract_save_data as $k => $v) {

                    $index = explode('.', $recursive_path);
                    $path_index = '["' . implode('"]["', $index) . '"]';
                    $path_index = $path_index . '["' . $k . '"]';

                    $file_ids = $this->moveFromTmp($v, $module_name);
                    if ($file_ids === false) {

                        $express = 'unset($save_data' . $path_index . ');';
                        eval($express);
                        continue;
                    }

                    $express = '$save_data' . $path_index . '= $file_ids;';
                    eval($express);
                }
            }
        }
    }

    /**
     * moveFromTmp
     * Thực hiện chuyển file từ thư mục tmp vào thư mục target
     * 
     * @param array $file
     * @param string $module_name - Tên thư mục Module cần chuyển file vào
     * 
     * @return boolean|\MongoId
     * @throws CakeException
     */
    public function moveFromTmp($file, $module_name) {

        if (empty($file)) {

            return false;
        }

        $status_file_upload_completed = Configure::read('sysconfig.App.constants.STATUS_FILE_UPLOAD_COMPLETED');
        $file_ids = array();

        if (!is_array($file)) {

            $file = array($file);
        }

        foreach ($file as $v) {

            $file_path = $v; // truyền vào đường dẫn tuyệt đối của file
            if (empty($file_path)) {

                $this->log(__('File path is empty'), 'file_import');
                continue;
            }

            // thực hiện support cho môi trường windows
            if (DIRECTORY_SEPARATOR == '\\') {

                $file_path = str_replace('\\', '/', $file_path);
            }

            // lấy ra thông tin của file
            $file_obj = new File($file_path, false, 0755);
            if (!$file_obj->exists()) {

                $this->log(__('File in %s is not exist', $file_obj->path), 'file_import');
                continue;
            }

            $file_name = $file_obj->name;
            $file_managed = array(
                'name' => $file_name,
                'size' => $file_obj->size(),
                'mime' => $file_obj->mime(),
                'status' => $status_file_upload_completed,
                'uri' => $file_path,
            );

            if (empty($file_managed['mime'])) {

                $file_managed['mime'] = $this->controller->getMimeType($file_name);
            }

            if (!isset($this->controller->FileManaged)) {

                $this->controller->loadModel('FileManaged');
            }

            // thực hiện insert vào FileManaged
            $this->controller->FileManaged->create();
            $this->controller->FileManaged->save($file_managed);
            $file_id = $this->controller->FileManaged->getLastInsertID();

            $target = $this->generateFolderStructure($module_name, $file_managed['mime']);

            // thực hiện làm sạch file name
            // ghi đè vào phương thức đã có, làm đẹp file_name và tạo unique
            $unique = $this->generateRandomLetters(5);
            $ext = pathinfo($file_name, PATHINFO_EXTENSION);
            $origin_name = basename($file_name, "." . $ext);
            $unique_name = $this->normalizeUrl($origin_name) . '_' . $unique . '.' . $ext;

            $copy = $file_obj->copy(APP . $target . $unique_name);

            if (!$copy) {

                $this->log(__('Can not copy file from %s to %s', $file_obj->path, APP . $target . $unique_name), 'file_import');
                throw new CakeException(__('Can not copy file from %s to %s', $file_obj->path, APP . $target . $unique_name));
            }

            // cập nhật lại đường dẫn file và set status = 1
            $file_managed['uri'] = $target . $unique_name;
            $file_managed['status'] = $status_file_upload_completed;
            $file_managed['name'] = $unique_name;
            $file_managed['id'] = $file_id;

            if (!$this->controller->FileManaged->save($file_managed)) {

                $this->log(__('Cant save file data into File Collection'), 'file_import');
                $this->log($file_managed, 'file_import');
                throw new CakeException(__('Cant save file data into File Collection'));
            }

            $file_ids[] = new MongoId($file_managed['id']);
        }

        return $file_ids;
    }

    public function downloadFileInContent(&$save_data, $fields, $module_name = null, $options = array()) {

        App::import('Vendor', 'simple_html_dom', array('file' => 'simple_html_dom' . DS . 'simple_html_dom.php'));
        $folder_structure = array(
            WEBROOT_DIR,
            self::DIR_PATH_IN_EDITOR,
            $module_name,
            date('Ym'),
            date('d'),
        );

        $folder_path = APP;

        foreach ($folder_structure as $item) {

            $folder_path .= DS . $item;
            $folder = new Folder($folder_path, false, 0777);
            if (!$folder->inPath($folder_path)) {

                $folder = new Folder($folder_path, true, 0777);
            }
        }

        if (!is_array($fields)) {

            $fields = array($fields);
        }
        foreach ($fields as $v) {

            if (!isset($save_data[$v])) {

                continue;
            }

            // thực hiện phân tích dom
            $dom = str_get_html($save_data[$v]);
            $img = $dom->find('img');
            if (empty($img)) {

                continue;
            }

            foreach ($img as $item) {

                $src = $item->src;
                $download = $this->downloadFile($src, $folder_path . '/');
                if ($download === false) {

                    $this->log('$save_data:', 'file_import');
                    $this->log($save_data, 'file_import');
                }
                $item->src = Router::url('/') . $download;
            }
            $save_data[$v] = $dom->outertext;
        }
    }

    protected function downloadFile($link, $module_path) {

        if ($this->isHttpLink($link)) {

            $random_char = $this->generateRandomLetters(5);
            $file_name = basename($link) . $random_char;
            $file_name = $this->normalizeUrl($file_name);

            $file_download = new File(APP . 'tmp/' . $file_name, true, 0755);
//            $http = new HttpSocket(array(
//                'timeout' => self::MAX_DOWNLOAD_TIME_OUT,
//                'ssl_verify_peer' => false,
//                'ssl_verify_host' => false,
//                'ssl_allow_self_signed' => false,
//                'ssl_cafile' => false,
//            ));
//            $f = fopen($file_download->path, 'w');
//            $http->setContentResource($f);
            $file_url = $link;

            // endcode lại từ đường dẫn file path -> tạo ra đường dẫn chuẩn, hợp lệ dùng để download file về
            $file_link = $this->encodeUrl($file_url);

//            $response = $http->get($file_link, array(), array('redirect' => true));
//            fclose($f);
//
//
//            // thực hiện tải file về
//            // xác định đường dẫn file_path có thật sự tồn tại file hay không?
//            if (!isset($response) || !$response->isOk()) {
//
//                $file_download->delete();
//                $this->log(__('Can not download file from %s into %s', $link, $file_download->path), 'file_import');
//                $this->log(__('$response'), 'file_import');
//                $this->log($response, 'file_import');
//                return false;
//            }

            file_put_contents($file_download->path, fopen($file_link, 'r'));

            $info = getimagesize($file_download->path);
            if (!empty($info[2])) {

                $file_ext = image_type_to_extension($info[2], false);
            } else {

                $file_ext = $file_download->ext();
            }

            if (!empty($file_ext)) {

                rename($file_download->path, $file_download->path . '.' . $file_ext);
                $file_name = $file_download->name . '.' . $file_ext;
                $file_download->path = $file_download->path . '.' . $file_ext;
            } else {

                $this->log(__('Can not download file from %s into %s', $link, $file_download->path), 'file_import');
                $file_download->delete();
                return false;
            }

            if ($file_download->copy($module_path . $file_name)) {

                $file_download_path = str_replace(APP, '', $module_path . $file_name);
                $file_download->delete();

                return $file_download_path;
            }

            $file_download->delete();
            $this->log(__('Can not copy a downloaded file from %s to %s', $file_download->path, $module_path . $file_name), 'file_import');
            return false;
        } else {

            return $link;
        }
    }

    /**
     * isHttpLink
     * xác định xem link có phải dạng http:// hoặc https://
     * 
     * @param string $link
     * @return boolean
     */
    protected function isHttpLink($link) {

        if (strpos($link, 'http://') !== false || strpos($link, 'https://') !== false) {

            return true;
        }

        return false;
    }

    /**
     * encodeUrl
     * Thực hiện endcode url, dùng để download file
     * 
     * @param string $url
     * @return string
     */
    public function encodeUrl($url) {

        $path = parse_url($url, PHP_URL_PATH);

        if (strpos($path, '%') !== false) {

            return $url; //avoid double encoding
        } else {

            $encoded_path = array_map('rawurlencode', explode('/', $path));
            return str_replace($path, implode('/', $encoded_path), $url);
        }
    }

    /**
     * convert_vi_to_en method
     * hàm chuyền đổi tiếng việt có dấu sang tiếng việt không dấu
     * 
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
        $str = preg_replace("/(Đ)/", 'D', $str);
//$str = str_replace(" ", "-", str_replace("&*#39;","",$str));
        return $str;
    }

    /**
     * normalizeUrl
     * hàm chuyển đổi các kí tự đặc biệt, dấu cách thành dạng có dấu gạch ngang và viết thường
     * trong việc tạo ra folder trong dựa vào file name
     * 
     * @param string $str
     * @return string
     */
    protected function normalizeUrl($str) {

        $str = $this->convert_vi_to_en($str);
        $str = preg_replace("![^a-z0-9]+!i", "-", mb_strtolower($str, "UTF-8"));

        return $str;
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

}
