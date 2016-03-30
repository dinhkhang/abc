<?php

class TagCommonBehavior extends ModelBehavior {

    public function afterSave(\Model $model, $created, $options = array()) {
        parent::afterSave($model, $created, $options);

        if (
                isset($model->data[$model->alias]['status']) &&
                isset($model->data[$model->alias]['tags']) &&
                is_array($model->data[$model->alias]['tags']) &&
                !empty($model->data[$model->alias]['tags'])
        ) {

            App::import('Lib', 'Html2TextUtility');
            App::uses('Tag', 'Model');
            $Tag = new Tag();
            foreach ($model->data[$model->alias]['tags'] as $v) {

                $v = trim($v);
                if (!strlen($v)) {

                    continue;
                }

                // thực hiện lưu trữ Tag
                $Tag->store($model->alias, $v, $model->data[$model->alias]['status']);
            }
        }

        return true;
    }

    public function beforeSave(\Model $model, $options = array()) {
        parent::beforeSave($model, $options);

        if (
                isset($model->data[$model->alias]['tags']) &&
                is_array($model->data[$model->alias]['tags']) &&
                !empty($model->data[$model->alias]['tags'])
        ) {

            App::import('Lib', 'Html2TextUtility');
            $tags = $model->data[$model->alias]['tags'];
            $model->data[$model->alias]['tags_ascii'] = array();
            $model->data[$model->alias]['tags'] = array();
            $tag_index = 0;
            foreach ($tags as $v) {

                $v = trim($v);
                if (!strlen($v)) {

                    continue;
                }
                $model->data[$model->alias]['tags'][$tag_index] = $v;

                // thực hiện tạo ra tags_ascii
                $content = Html2TextUtility::getText($v);
                $tags_ascii = $model->convert_vi_to_en($content);
                $model->data[$model->alias]['tags_ascii'][$tag_index] = trim($tags_ascii);
                $tag_index++;
            }
        }

        // khi user thực hiện edit, bỏ tag đã gán ra khỏi content
        if (!empty($model->data[$model->alias]['id']) && isset($model->data[$model->alias]['tags'])) {

            // đọc lại thông tin
            $get_back = $model->find('first', array(
                'conditions' => array(
                    'id' => new MongoId($model->data[$model->alias]['id']),
                ),
            ));

            // nếu content trước đó chưa được gán tag
            if (empty($get_back[$model->alias]['tags']) || !is_array($get_back[$model->alias]['tags'])) {

                return true;
            }

            $tags = $get_back[$model->alias]['tags'];
            $current_tags = $model->data[$model->alias]['tags'];

            // thực hiện so sánh các tags đã gán với tags đang được gán hiện tại
            // để tìm ra các tags đã bị bỏ gán khỏi content
            $this->minusTags($tags, $current_tags, $model);
        }

        return true;
    }

    protected function minusTags($tags, $current_tags, $model) {

        App::uses('Tag', 'Model');
        $Tag = new Tag();

        // nếu tags hiện tại đã bị bỏ gán hoàn toàn
        if (empty($current_tags)) {

            foreach ($tags as $v) {

                $this->minusTagCount($Tag, $v, $model);
            }
        } else {

            foreach ($tags as $k => $v) {

                $tags[$k] = mb_strtolower($v);
            }

            foreach ($current_tags as $k => $v) {

                $current_tags[$k] = mb_strtolower($v);
            }

            // tìm ra các tag đã bị bỏ gán khỏi content
            $minus_tags = array_diff($tags, $current_tags);
            if (empty($minus_tags)) {

                return;
            }

            foreach ($minus_tags as $v) {

                $this->minusTagCount($Tag, $v, $model);
            }
        }
    }

    /**
     * minusTagCount
     * giảm số lượt Tag count
     * 
     * @param Model $Tag
     * @param string $name
     * @param Model $model
     * 
     * @return mixed
     */
    protected function minusTagCount($Tag, $name, $model) {

        $tag_exist = $Tag->checkExist($name, $model->useTable);
        if (empty($tag_exist)) {

            return;
        }
//        $tag_count = (int) $tag_exist['Tag']['count'];
        $tag_count = $Tag->countExist($model->alias, $name);
        if ($tag_count - 1 <= 0) {

            $Tag->save(array(
                'id' => $tag_exist['Tag']['id'],
                'count' => 0,
                'status' => Configure::read('sysconfig.App.constants.STATUS_DELETE'),
            ));
        } else {

            // đếm tổng số content đang public được gán vào tag
            $tag_public_count = $Tag->countExist($model->alias, $name, array(
                'conditions' => array(
                    'status' => Configure::read('sysconfig.App.constants.STATUS_APPROVED'),
                    'id' => array(
                        '$ne' => new MongoId($model->data[$model->alias]['id']),
                    ),
                ),
            ));

            if ($tag_public_count > 0) {

                $Tag->save(array(
                    'id' => $tag_exist['Tag']['id'],
                    'count' => $tag_count - 1,
                    'status' => Configure::read('sysconfig.App.constants.STATUS_APPROVED'),
                ));
            } else {

                $Tag->save(array(
                    'id' => $tag_exist['Tag']['id'],
                    'count' => $tag_count - 1,
                    'status' => Configure::read('sysconfig.App.constants.STATUS_HIDDEN'),
                ));
            }
        }
    }

    public function beforeDelete(\Model $model, $cascade = true) {
        parent::beforeDelete($model, $cascade);

        $get_back = $model->find('first', array(
            'conditions' => array(
                'id' => new MongoId($model->id),
            ),
        ));
        if (empty($get_back[$model->alias]['tags']) || !is_array($get_back[$model->alias]['tags'])) {

            return true;
        }

        $tags = $get_back[$model->alias]['tags'];

        App::uses('Tag', 'Model');
        $Tag = new Tag();

        foreach ($tags as $v) {

            $check_exist = $Tag->checkExist($v, $model->useTable);
            if (empty($check_exist)) {

                continue;
            }

//            $count = $check_exist['Tag']['count'];
            $count = $Tag->countExist($model->alias, $v);

            // nếu tag được gán vào nhiều hơn 1 content, thì giảm số lượng gán đi -1
            if ($count > 1) {

                $count_public = $Tag->countExist($model->alias, $v, array(
                    'conditions' => array(
                        'status' => Configure::read('sysconfig.App.constants.STATUS_APPROVED'),
                        'id' => array(
                            '$ne' => new MongoId($model->id),
                        ),
                    ),
                ));

                if ($count_public > 0) {

                    $Tag->save(array(
                        'id' => $check_exist['Tag']['id'],
                        'count' => $count - 1,
                        'status' => Configure::read('sysconfig.App.constants.STATUS_APPROVED'),
                    ));
                } else {

                    $Tag->save(array(
                        'id' => $check_exist['Tag']['id'],
                        'count' => $count - 1,
                        'status' => Configure::read('sysconfig.App.constants.STATUS_HIDDEN'),
                    ));
                }
            }
            // nếu tag được gán vào nhỏ hơn 1 content, thì thực hiện set cờ xóa
            else {

                $Tag->save(array(
                    'id' => $check_exist['Tag']['id'],
                    'count' => 0,
                    'status' => Configure::read('sysconfig.App.constants.STATUS_DELETE'),
                ));
            }
        }

        return true;
    }

}
