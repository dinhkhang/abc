<?php

App::uses('Revision', 'Model');

class RevisionCommonBehavior extends ModelBehavior {

    public $old_state = array();
    public $new_state = array();

    public function beforeSave(\Model $model, $options = array()) {
        parent::beforeSave($model, $options);

        if (!empty($model->data[$model->alias]['id'])) {

            $old_state = $model->find('first', array(
                'conditions' => array(
                    'id' => $model->data[$model->alias]['id'],
                ),
            ));

            $this->old_state = $old_state[$model->alias];
        }
    }

    public function afterSave(\Model $model, $created, $options = array()) {
        parent::afterSave($model, $created, $options);

        if (!$created) {

            $user = CakeSession::read('Auth.User');
            $Revision = new Revision();
            $Revision->useTable = $model->useTable . '_revisions';
            $Revision->create();
            $save_data = $this->old_state;
            $save_data['revision_created'] = $save_data['revision_modified'] = new MongoDate();
            $save_data['revision_user'] = new MongoId($user['id']);
            $save_data['revision_id'] = new MongoId();
             $save_data['revision_log'] = '';
            $Revision->save($save_data);
        }
    }

}
