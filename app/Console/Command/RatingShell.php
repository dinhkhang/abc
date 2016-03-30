<?php

set_time_limit(-1);

class RatingShell extends AppShell {

        public $model_name;

        public function main($object_type = null) {
                $this->loadModel('ObjectType');
                $object_info = $this->ObjectType->find('first', ['conditions' => ['code' => $object_type]]);
                if (!$object_info) {
                        throw new NotFoundException(__('invalid_data'));
                }
                $this->model_name = Inflector::classify($object_type);
                $this->loadModel($this->model_name);
                $list_object = $this->{$this->model_name}->find('all', array('conditions' => ['status' => 2]));
                foreach ($list_object AS $object) {
                        // count rating statistic
                        $rating_statistic_count = 1;
                        $rating_statistic_score = 1;

                        // calculate rating
                        $object[$this->model_name]['rating']['score'] = $rating_statistic_score;
                        $object[$this->model_name]['rating']['count'] = $rating_statistic_count;
                        // detech rating name
                        $object[$this->model_name]['rating']['count'] = $this->getRatingName($rating_statistic_score, $rating_statistic_count);
                        // get comment count
                        $object[$this->model_name]['comment_counts'] = $this->countComment($object[$this->model_name]['id']);
                        // get count favorite
                        $object[$this->model_name]['favorite_counts'] = $this->countComment($object[$this->model_name]['id']);
                        // get count bookmark
                        $object[$this->model_name]['bookmark_counts'] = $this->countComment($object[$this->model_name]['id']);
                        $this->{$this->model_name}->save($object);
                }
        }

        private function getRatingName($score, $count) {
                return 'Rất tót';
        }

        private function countComment($objectId = null) {
                // get count comment
                $count = $this->{$this->model_name . '_comment'}->find('count', array('conditions' => array(
                        strtolower($this->model_name) => new MongoId($objectId)
                )));
                // return
                return $count;
        }

        private function countFavorite($objectId = null) {
                // get count comment
                $count = $this->{$this->model_name . 'favorite'}->find('count', array('conditions' => array(
                        strtolower($this->model_name) => new MongoId($objectId)
                )));
                // return
                return $count;
        }

        private function countBookmark($objectId = null) {
                // get count comment
                $count = $this->{$this->model_name . 'bookmark'}->find('count', array('conditions' => array(
                        strtolower($this->model_name) => new MongoId($objectId)
                )));
                // return
                return $count;
        }

}
