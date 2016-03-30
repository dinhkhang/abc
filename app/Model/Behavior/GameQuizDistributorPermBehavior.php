<?php

class GameQuizDistributorPermBehavior extends ModelBehavior {

    public function beforeFind(\Model $model, $query) {
        parent::beforeFind($model, $query);

        $user = CakeSession::read('Auth.User');
        if (!empty($user['type']) && $user['type'] == 'GAMEQUIZ_DISTRIBUTION_CHANNEL') {

            $distribution_channel_code = $user['distribution_channel_code'];
            $query['conditions']['distribution_channel_code'] = $distribution_channel_code;
        }

        return $query;
    }

}
