<?php

App::uses('Component', 'Controller');
App::uses('Folder', 'Utility');
App::uses('File', 'Utility');
App::import('Lib', 'ExtendedUtility');

class StreamingCommonComponent extends Component {

    public $controller = '';
    public $object_type_id = null;
    public $object_id = null;
    public $components = array('FileCommon');

    public function initialize(\Controller $controller) {

        parent::initialize($controller);

        $this->controller = $controller;

        if (!isset($this->controller->Streaming)) {

            $this->controller->loadModel('Streaming');
        }
    }

    public function autoSet(&$request_data, $object_id, $object_type_id = null) {

        if (empty($object_type_id)) {

            $this->object_type_id = $this->controller->object_type_id;
        } else {

            $this->object_type_id = $object_type_id;
        }

        $options = array(
            'conditions' => array(
                'object_id' => new MongoId($object_id),
                'object_type' => new MongoId($this->object_type_id),
                'status' => Configure::read('sysconfig.App.constants.STATUS_APPROVED'),
            ),
        );

        $streamings = $this->controller->Streaming->find('all', $options);
        if (empty($streamings)) {

            return;
        }
        $expect_results = array();
        foreach ($streamings as $k => $v) {

            // thực hiện đọc ra file
            $this->FileCommon->autoSetFiles($v['Streaming']);
            $expect_results[$k] = $v['Streaming'];
        }

        $request_data['Streaming'] = $expect_results;
    }

    public function autoProcess(&$request_data, $object_id, $model_name = null) {

        if (empty($model_name)) {

            $model_name = $this->controller->modelClass;
        }

        if (empty($request_data['Streaming']) && empty($request_data[$model_name]['id'])) {

            return;
        }

        if (!isset($this->controller->Streaming)) {

            $this->controller->loadModel('Streaming');
        }

        // với trường hợp edit
        if (!empty($request_data[$model_name]['id'])) {

            $options = array(
                'conditions' => array(
                    'object_id' => new MongoId($request_data[$model_name]['id']),
                    'object_type' => new MongoId($this->controller->object_type_id),
                    'status' => Configure::read('sysconfig.App.constants.STATUS_APPROVED'),
                ),
                'fields' => array(
                    'id', 'id'
                ),
            );

            // lấy ra toàn bộ danh sách streaming id công khai của object
            $streaming_ids = $this->controller->Streaming->find('list', $options);
        }

        // đối với trường hợp edit mà không thấy có Streaming, 
        // tức chuyển toàn bộ Streaming từ công khai sang ẩn đi
        if (empty($request_data['Streaming'])) {

            if (!empty($streaming_ids)) {

                foreach ($streaming_ids as $k => $v) {

                    $streaming_ids[$k] = new MongoId($v);
                }

                $this->controller->Streaming->updateAll(array(
                    'status' => Configure::read('sysconfig.App.constants.STATUS_HIDDEN'),
                        ), array(
                    '_id' => array(
                        '$in' => array_values($streaming_ids),
                    ),
                ));
            }

            return;
        }

        foreach ($request_data['Streaming'] as $v) {

            if (empty($v['file_path'])) {

                continue;
            }

            if (!empty($v['id']) && !empty($streaming_ids) && in_array($v['id'], $streaming_ids)) {

                unset($streaming_ids[$v['id']]);
            }

            $v['object_type'] = new MongoId($this->controller->object_type_id);
            $v['object_id'] = new MongoId($object_id);
            $v['file_mime'] = ExtendedUtility::getMimeType($v['file_path']);
            $v['status'] = Configure::read('sysconfig.App.constants.STATUS_APPROVED');

            // xử lý file
            $this->FileCommon->autoProcess($v, Configure::read('sysconfig.Streamings.data_file_root'));

            if (empty($v['id'])) {

                $this->controller->Streaming->create();
            }

            $this->controller->Streaming->save($v);
        }

        if (empty($streaming_ids)) {

            return;
        }

        // set status thành hidden đối với những streaming
        foreach ($streaming_ids as $v) {

            $hidden_data = array(
                'id' => $v,
                'status' => Configure::read('sysconfig.App.constants.STATUS_HIDDEN'),
            );
            $this->controller->Streaming->save($hidden_data);
        }
    }

}
