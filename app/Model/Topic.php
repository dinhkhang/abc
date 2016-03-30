<?php

App::uses('AppModel', 'Model');
App::uses('SeoRouter', 'Model');

class Topic extends AppModel
{

    public $useTable = 'topics';
    public $actsAs = array('ContentProviderPerm');

    public $validate = array(
        'name' => array(
            'rule' => 'isUnique',
            'message' => 'This name has already been taken.'
        ),
        'seo_url' => array(
            array(
                'rule' => 'isUnique',
                'message' => 'This seo_url has already been taken.',
            ),
            array(
                "rule" => array("validateRoute"),
                "message" => "This router already exists.",
            )
        ),
        'publish_time' => array(
            'rule' => array("validateStatus"),
            'message' => 'This publish time is empty.'
        ),
    );

    public $customSchema = array(
        'id' => '',
        'parent' => '',
        'name' => '',
        'seo_url' => '',
        'meta_tags' => '',
        'short_description' => '',
        'weight' => '',
        'status' => '',
        'publish_time' => '',
        'user' => '',
        'created' => '',
        'modified' => '',
    );

    public function findListName($id = null)
    {
        $result = $this->find('list', array(
            'fields' => array('id', 'name'),
            'conditions' => array('status' => Configure::read('sysconfig.App.constants.STATUS_APPROVED'))
        ));
        if (isset($id, $result[$id])) {
            unset($result[$id]);
        }
        return $result;
    }

    public function beforeSave($options = array())
    {
        parent::beforeSave($options);

        if (isset($this->data[$this->alias]['status'])) {
            if ($this->data[$this->alias]['status'] != Configure::read('sysconfig.App.constants.STATUS_SCHEDULE')) {
                $this->data[$this->alias]['publish_time'] = '';
            }
        }

        if (isset($this->data[$this->alias]['weight']) && strlen($this->data[$this->alias]['weight'])) {
            $this->data[$this->alias]['weight'] = (int)$this->data[$this->alias]['weight'];
        }
    }

    public function validateRoute()
    {
        $seoRouterModel = new SeoRouter();
        $seo_url = isset($this->data[$this->alias]['seo_url']) ? $this->data[$this->alias]['seo_url'] : '';
        if($seo_url) {
            // check old seo_url == new seo_url
            if($this->id && $this->field('seo_url') == $seo_url) {
                return true;
            }
            // if not exists, return ok
            return !$seoRouterModel->checkExistsRoute($seo_url);
        }
        return true;
    }

    public function validateStatus()
    {
        if(isset($this->data[$this->alias]['status']) && $this->data[$this->alias]['status'] == Configure::read('sysconfig.App.constants.STATUS_SCHEDULE')) {
            if(isset($this->data[$this->alias]['publish_time']) && !strtotime($this->data[$this->alias]['publish_time'])) {
                return false;
            }
        }
        return true;
    }

    public function afterSave($created, $options = array())
    {
        parent::afterSave($created, $options);

        // save to seo_routers
        $this->updateSeoRouter();
    }

    public function afterDelete()
    {
        parent::afterDelete();

        // save to seo_routers
        $this->deleteSeoRouter();
    }

    public function updateSeoRouter()
    {
        $seoRouterModel = new SeoRouter();
        $id = isset($this->id) ? $this->id : $this->getLastInsertID();
        if (!is_object($id)) {
            $id = new MongoId($id);
        }
        // get old data
        $status = isset($this->data[$this->alias]['status']) ? $this->data[$this->alias]['status'] : '';
        $status_new = 0;
        if ($status == Configure::read('sysconfig.App.constants.STATUS_APPROVED')) {
            $status_new = 1;
        }

        // check exists
        if(!(isset($this->data[$this->alias]['seo_url']) && strlen($this->data[$this->alias]['seo_url']))) {
            return;
        }
        $data = $seoRouterModel->find('first', array('conditions' => array('object_id' => $id)));
        $route = isset($this->data[$this->alias]['seo_url']) ? $this->data[$this->alias]['seo_url'] : $this->field('seo_url');
        $save = array(
            'object_code' => $this->useTable,
            'object_id' => $id,
            'route' => $route,
            'defaults' => json_encode(array('controller' => $this->useTable, 'action' => 'view', $id->{'$id'})),
            'options' => json_encode(array()),
            'status' => $status_new,
        );
        if ($data) {
            // update
            $seoRouterModel->id = new MongoId($data[$seoRouterModel->alias]['id']);
        } else {
            // insert new record
            $seoRouterModel->create();
        }
        // save vào bảng seo_routers
        $seoRouterModel->save($save);
    }

    public function deleteSeoRouter()
    {
        $seoRouterModel = new SeoRouter();
        if($this->id) {
            $seoRouterModel->delete($this->id);
        }
    }

}
