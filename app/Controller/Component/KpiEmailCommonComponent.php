<?php

class KpiEmailCommonComponent extends Component {

    public $controller = null;

    public function initialize(\Controller $controller) {
        parent::initialize($controller);

        $this->controller = $controller;
    }

    public function logTracking($data, $options = array()) {

        if (!isset($this->controller->KpiEmail)) {

            $this->controller->loadModel('KpiEmail');
        }
        if (!empty($options['async']) && $options['async'] === false) {

            $this->controller->KpiEmail->create();
            return $this->controller->KpiEmail->save($data);
        } else {

            if (empty($data['created'])) {

                $data['created'] = new MongoDate();
            }
            if (empty($data['modified'])) {

                $data['modified'] = new MongoDate();
            }
            $mongo = $this->controller->KpiEmail->getDataSource();
            $mongoCollectionObject = $mongo->getMongoCollection($this->controller->KpiEmail);
            return $mongoCollectionObject->insert($data, array('w' => 0));
        }
    }

}
