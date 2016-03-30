<?php

/**
 * CakePHP Behavior
 * @author User
 */
class LocationCommonBehavior extends ModelBehavior {

//        public function afterSave(\Model $model, $created, $options = array()) {
//                parent::afterSave($model, $created, $options);
//
//                if (
//                        !$created &&
//                        isset($model->data[$model->alias]['status']) &&
//                        !empty($model->data[$model->alias]['location']['_id']) &&
//                        !empty($model->data[$model->alias]['location']['object_type']) &&
//                        !empty($model->object_type_id)
//                ) {
//
//                        $location_id = $model->data[$model->alias]['location']['_id'];
//                        $object_type_id = $model->data[$model->alias]['location']['object_type'];
//                        if ($model->data[$model->alias]['location']['_id'] instanceof MongoId) {
//
//                                $location_id = (string) $model->data[$model->alias]['location']['_id'];
//                        }
//
//                        if ($model->data[$model->alias]['location']['object_type'] instanceof MongoId) {
//
//                                $object_type_id = (string) $model->data[$model->alias]['location']['object_type'];
//                        }
//
//                        if ($object_type_id == $model->object_type_id) {
//
//                                App::import('model', 'Location');
//                                $Location = new Location();
//                                $Location->save(array(
//                                    'id' => $location_id,
//                                    'status' => $model->data[$model->alias]['status'],
//                                ));
//                        }
//                }
//
//                return true;
//        }

        public function beforeDelete(\Model $model, $cascade = true) {
                parent::beforeDelete($model, $cascade);

                // đọc lại thông tin 
                $get_back = $model->find('first', array(
                    'conditions' => array(
                        'id' => new MongoId($model->id),
                    ),
                ));

                if (
                        !empty($get_back[$model->alias]['location']['_id']) &&
                        !empty($get_back[$model->alias]['location']['object_type'])
                ) {

                        $location_id = $get_back[$model->alias]['location']['_id'];
                        $object_type_id = $get_back[$model->alias]['location']['object_type'];
                        if ($get_back[$model->alias]['location']['_id'] instanceof MongoId) {

                                $location_id = (string) $get_back[$model->alias]['location']['_id'];
                        }

                        if ($get_back[$model->alias]['location']['object_type'] instanceof MongoId) {

                                $object_type_id = (string) $get_back[$model->alias]['location']['object_type'];
                        }

                        if ($object_type_id == $model->object_type_id) {

                                App::import('model', 'Location');
                                $Location = new Location();
                                $Location->delete($location_id);
                        }
                }

                return true;
        }

        public function beforeValidate(\Model $model, $options = array()) {
                parent::beforeValidate($model, $options);

                if (
                        isset($model->data[$model->alias]['loc']['coordinates']) &&
                        isset($model->data[$model->alias]['loc']['coordinates'][0]) &&
                        isset($model->data[$model->alias]['loc']['coordinates'][1])
                ) {

                        $long = floatval(trim($model->data[$model->alias]['loc']['coordinates'][0]));
                        $lat = floatval(trim($model->data[$model->alias]['loc']['coordinates'][1]));
                        $is_valid = 1;

                        if ($long > 180 || $long < -180) {

                                $model->validationErrors['loc']['coordinates'][0] = __('location_common_invalid_long');
                                $is_valid = 0;
                        }

                        if ($lat > 90 || $lat < -90) {

                                $model->validationErrors['loc']['coordinates'][1] = __('location_common_invalid_lat');
                                $is_valid = 0;
                        }

                        if (!$is_valid) {

                                return false;
                        }
                }
        }

}
