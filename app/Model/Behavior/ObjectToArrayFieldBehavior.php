<?php

/**
 * Thực hiện convert trường fields dạng object: key => value, sang dạng array
 * mục đích dành cho việc dot notation find
 */
class ObjectToArrayFieldBehavior extends ModelBehavior {

    const SUFFIX = '_arr';

    public function beforeSave(\Model $model, $options = array()) {
        parent::beforeSave($model, $options);

        if (!empty($model->object_fields)) {

            foreach ($model->object_fields as $field) {

                if (isset($model->data[$model->alias][$field]) && is_array($model->data[$model->alias][$field])) {

                    $field_arr = $field . self::SUFFIX;
                    $model->data[$model->alias][$field_arr] = array_values($model->data[$model->alias][$field]);
                }
            }
        }

        if (isset($model->data[$model->alias]['packages'])) {

            $packages = $model->data[$model->alias]['packages'];
            if (empty($packages) || !is_array($packages)) {

                $model->data[$model->alias]['register_packages'] = array();
            } else {

                $model->data[$model->alias]['register_packages'] = $this->convertPackageArr($packages);
            }
        }

        return true;
    }

    protected function convertPackageArr($packages = array()) {

        $register_packages = array();
        foreach ($packages as $pkg) {

            if (in_array($pkg['status'], array(1, 4))) {

                $register_packages[] = $pkg['package'];
            }
        }

        return $register_packages;
    }

}
