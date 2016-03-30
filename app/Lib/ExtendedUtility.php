<?php

class ExtendedUtility {

    static public function array_intersect_key_recursive(array $array1, array $array2) {

        $array1 = array_intersect_key($array1, $array2);

        foreach ($array1 as $key => &$value) {

            if (is_array($value) && is_array($array2[$key])) {

                $value = self::array_intersect_key_recursive($value, $array2[$key]);
            }
        }

        return $array1;
    }

    /**
     * getMimeType
     * nhận dạng mime type của file thông qua đuôi mở rộng
     * @param string $filename
     * @param string $mimePath
     * @return string
     */
    static public function getMimeType($filename) {
        $fileext = substr(strrchr($filename, '.'), 1);
        if (empty(
                        $fileext))
            return (false);
        $regex = "/^([\w\+\-\.\/]+)\s+(\w+\s)*($fileext\s)/i";
        $mimePath = APP . 'Config' . '/mime.types';
        $lines = file($mimePath);
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

    /**
     * generateFolderStructure
     * Thực hiện tạo ra cấu trúc thư mục lưu trữ [Tên module/Tên ext/nămtháng/ngày]
     * 
     * @param string $module_name
     * @param string $ext
     * 
     * @return string
     */
    static public function generateFolderStructure($module_name, $mime, $absolute = false) {

        App::uses('Folder', 'Utility');
        App::uses('File', 'Utility');

        $data_root_name = Configure::read('sysconfig.App.data_file_root');

        $pretty_mime = strtolower($mime);
        $extract_mime = explode('/', $pretty_mime);

        $year = date('Y');
        $month = date('m');
        $day = date('d');
//		$block = rand(1, 2000);
        $folder_structure = array(
            $data_root_name,
            $module_name,
            $extract_mime[0],
            $year . $month,
            $day,
//			$block,
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

    /**
     * generateRandomLetters
     * thực tạo ra các kí tự ngẫu nhiên
     * 
     * @param int $length
     * @return string
     */
    static public function generateRandomLetters($length) {

        $random = '';

        for ($i = 0; $i < $length; $i++) {

            $random .= chr(rand(ord('a'), ord('z')));
        }

        return $random;
    }

    /**
     * processFiles
     * Tự động xử lý liên quan tới files, gọi trước khi thực hiện save vào database
     * 
     * @param reference array $save_data
     * @param string $module_name - Là tên thư mục lưu trữ dành cho Module, mặc định đọc trong 'sysconfig.' . $this->controller->name . '.data_file_root'
     * 
     * @return boolean
     * @throws CakeException
     */
    static public function processFiles(&$save_data, $module_name, $options = array()) {

        if (empty($save_data)) {

            return false;
        }

        if (empty($save_data['files']) || !is_array($save_data['files'])) {

            $save_data['files'] = "";
            return;
        }

        foreach ($save_data['files'] as $type => $file) {

            $file_ids = self::moveFromTmp($file, $module_name);
            if ($file_ids === false) {

                unset($save_data['files'][$type]);
                continue;
            }

            $save_data['files'][$type] = $file_ids;
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
    static public function moveFromTmp($file, $module_name) {

        if (empty($file)) {

            return false;
        }

        $status_file_upload_completed = Configure::read('sysconfig.App.constants.STATUS_FILE_UPLOAD_COMPLETED');
        $file_ids = array();

        if (!is_array($file)) {

            $file = array($file);
        }

        foreach ($file as $v) {

            $item = json_decode($v, true);
            if (empty($item)) {

                throw new CakeException(__('The input file is invalid'));
            }

            // kiểm tra xem file đã được move hay chưa?
            if ($item['status'] == $status_file_upload_completed) {

                $file_ids[] = new MongoId($item['id']);
                continue;
            }

            $file_uri = APP . WEBROOT_DIR . DS . $item['uri'];

            // thực hiện support cho môi trường windows
            if (DIRECTORY_SEPARATOR == '\\') {

                $file_uri = str_replace('\\', '/', $file_uri);
            }

            $file_obj = new File($file_uri, false, 0755);
            if (!$file_obj->exists()) {

                throw new CakeException(__('The input file is not exist'));
            }

            $file_name = basename($item['uri']);

            if (!empty($item['mime'])) {

                $mime = $item['mime'];
            } else {

                $mime = ExtendedUtility::getMimeType($file_name);
            }

            $target = ExtendedUtility::generateFolderStructure($module_name, $mime);

            $copy = $file_obj->copy(APP . $target . $file_name);

            if (!$copy) {

                throw new CakeException(__('Can not copy file from %s to %s', $file_obj->path, APP . $target . $file_name));
            }
            $file_obj->delete();

            // cập nhật lại đường dẫn file và set status = 1
            $item['uri'] = $target . $file_name;
            $item['status'] = $status_file_upload_completed;

            App::uses('FileManaged', 'Model');
            $FileManaged = new FileManaged();
            if (!$FileManaged->save($item)) {

                throw new CakeException(__('Cant save file data into File Collection'));
            }

            $file_ids[] = new MongoId($item['id']);
        }

        return $file_ids;
    }

}
