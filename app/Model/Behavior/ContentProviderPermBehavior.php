<?php

/**
 * CakePHP Behavior
 * @author User
 */
class ContentProviderPermBehavior extends ModelBehavior {

    public function beforeFind(\Model $model, $query) {
        parent::beforeFind($model, $query);

        $user = CakeSession::read('Auth.User');

        // đối với user nhập content, thì chỉ nhìn thấy content của mình
        if (
                $user['type'] == 'CONTENT_EDITOR' &&
                !empty($user['content_provider_code'])
        ) {

            $query['conditions']['user'] = new MongoId($user['id']);
        }
        // đối với content admin, thì nhìn thấy được hết content thuộc vào content provider
        // mà user đó thuộc vào
        elseif (
                $user['type'] == 'CONTENT_ADMIN' &&
                !empty($user['content_provider_code'])
        ) {

            App::import('Model', 'User');
            $User = new User();
            $user_ids = $User->getUserIdsByCPcode($user['content_provider_code']);
            $query['conditions']['$and'][]['user']['$in'] = $user_ids;
        }
        return $query;
    }

    public function beforeSave(\Model $model, $options = array()) {
        parent::beforeSave($model, $options);

        $user = CakeSession::read('Auth.User');
        if (
                !isset($model->data[$model->alias]['status']) &&
                empty($model->data[$model->alias]['id']) &&
                $user['type'] == 'CONTENT_EDITOR' &&
                !empty($user['content_provider_code'])
        ) {

            $model->data[$model->alias]['status'] = Configure::read('sysconfig.App.constants.STATUS_WAIT_REVIEW');
        }

        return true;
    }

    public function beforeDelete(\Model $model, $cascade = true) {
        parent::beforeDelete($model, $cascade);

        $user = CakeSession::read('Auth.User');
        $status_public = Configure::read('sysconfig.App.constants.STATUS_APPROVED');

        // đối với user nhập content, thì những content nào ở trạng thái PUBLIC thì
        // không được phép xóa
        if (
                isset($model->data[$model->alias]['status']) &&
                $model->data[$model->alias]['status'] == $status_public &&
                $user['type'] == 'CONTENT_EDITOR'
        ) {

            throw new Exception(__('The content editor can not delete any public content'));
        }

        return true;
    }

}
