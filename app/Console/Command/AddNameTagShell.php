<?php

class AddNameTagShell extends AppShell {

//    public $uses = array(
//        'Hotel', 'Place', 'Event', 'Tour', 'Restaurant', 'Tag'
//    );

    public $uses = array(
        'Place', 'Tag'
    );

    public function main() {

        $limit = $this->args[1];
        $offset = $this->args[0];
        if (empty($limit)) {
            $limit = 100;
        }
        if (empty($offset)) {
            $offset = 0;
        }
        $arr_model = $this->uses;
        foreach ($arr_model as $model_name) {
            if ($model_name == 'Tag') {
                continue;
            }
            $count = $this->editTags($model_name, $limit, $offset);
            $this->out("So ban ghi cua $model_name la: $count");
        }
    }

    public function editTags($model_name, $limit, $offset) {
        $this->logAnyFile("Start editTags. Model_name: $model_name", __CLASS__ .
                '_' . __FUNCTION__);
        $count = 0;
        $taglow = strtolower($model_name) . 's';
        $arr_tag = array(
            'lang_code' => 'vi',
            'status' => 2,
            'name' => null,
            'name_ascii' => null,
            'object_type_code' => $taglow,
        );
        $option['limit'] = $limit;
        $option['offset'] = $offset;
//        $option['order'] = array(
//            'id' => 'ASC',
//        );
        $option['fields'] = array('id', 'name', 'tags');
        $list_data = $this->$model_name->find('all', $option);
        $this->logAnyFile($list_data, __CLASS__ . '_' . __FUNCTION__);
        if (empty($list_data)) {
            $er = 'hong o day';
            return $er;
        }

        $checkExist = false;
        $this->logAnyFile("Foreach begin. checkExist: $checkExist", __CLASS__ . '_' . __FUNCTION__);
        foreach ($list_data as $new_data) {
            $new_data[$model_name]['name'] = trim($new_data[$model_name]['name']);
            $arr_update = array(
                'id' => null,
                'tags' => array(),);
            if (empty($new_data [$model_name]['name'])) {
                continue;
            }

            $newName = $this->convert_vi_to_en($new_data[$model_name]['name']);
            $newName = trim($newName);
            if (!empty($new_data[$model_name]['tags'])) {
                $checkExist = $this->checkExistTag($new_data[$model_name]['tags'], strtolower($newName));
            }
            if ($checkExist) {
                continue;
            }
            if (!empty($new_data[$model_name]['tags'])) {
                if (!is_array($new_data[$model_name]['tags'])) {
                    array_push($arr_update['tags'], $new_data[$model_name]['tags']);
                } else {
                    $arr_update['tags'] = $new_data[$model_name]['tags'];
                }
                array_push($arr_update['tags'], strtolower($newName));
            } else {
                $arr_update['tags'][0] = strtolower($newName);
            }
            $arr_update['id'] = new MongoId($new_data[$model_name]['id']);

            $arr_update['tags'] = $this->trimData($arr_update['tags']);
            $this->logAnyFile($arr_update, __CLASS__ . '_' . __FUNCTION__);
            $this->logAnyFile("name: " . strtolower($newName), __CLASS__ . '_' . __FUNCTION__);
//            debug($arr_update);
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
            $count++;
        }
        $this->logAnyFile("Foreach end. checkExist: $checkExist", __CLASS__ . '_' . __FUNCTION__);
        $this->logAnyFile("End editTags. Model_name: $model_name", __CLASS__ . '_' . __FUNCTION__);
        return $count;
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

    protected function existTag($name, $type) {

        $option = array('conditions' => array(
                'name' => $name,
                'object_type_code' => $type,
            ),
        );
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

    protected function convert_vi_to_en($str) {

        $str = preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/", 'a', $str);
        $str = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/", 'e', $str);
        $str = preg_replace("/(ì|í|ị|ỉ|ĩ)/", 'i', $str);
        $str = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/"
                , 'o', $str);
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
// thực hiện cưỡng ép chuyển  sang ascii
        $str = $this->forceConvertASCII($str);

        return $str;
    }

    protected function forceConvertASCII($str) {

        try {

            $ascii_str = @iconv("UTF-8", "us-ascii//TRANSLIT", $str);
        } catch (Exception $e) {

            $this->log($e, 'notice');
            $this->log($str, 'notice');
        }
        return $ascii_str;
    }

    protected function resetTags($model_name) {
        $option['fields'] = array('id', 'name', 'tags');
        $list_data = $this->$model_name->find('all', $option);
        debug($list_data);
        if (empty($list_data)) {
            return;
        }
        $count = 0;
        foreach ($list_data as $new_data) {
            $arr_update = array(
                'id' => null, 'tags' => array(),
            );

            $arr_update['id'] = new MongoId($new_data[$model_name]['id']);
            $this->$model_name->save($arr_update);
            $count++;
        }
        return $count;
    }

}
