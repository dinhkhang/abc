<?php

App::uses('AppModel', 'Model');
App::uses('SeoRouter', 'Model');
App::uses('Tag', 'Model');

class Blog extends AppModel
{

    public $useTable = 'blogs';
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
        'topic' => '',
        'name' => '',
        'seo_url' => '',
        'meta_tags' => '',
        'keyword' => '',
        'tags' => '',
        'short_description' => '',
        'description' => '',
        'weight' => '',
        'status' => '',
        'publish_time' => '',
        'files' => array(
            'logo' => '',
        ),
        'file_uris' => array(
            'logo' => '',
        ),
        'user' => '',
        'created' => '',
        'modified' => '',
    );

    public $asciiFields = array(
        'name',
        'tags',
        'short_description',
    );

    public function findListName($id = null)
    {
        $result = $this->find('list', ['fields' => ['id', 'name']]);
        if (isset($id, $result[$id])) {
            unset($result[$id]);
        }
        return $result;
    }

    public function beforeSave($options = array())
    {
        parent::beforeSave($options);

        if (isset($this->data[$this->alias]['topic'])) {
            $this->data[$this->alias]['topic'] = new MongoId($this->data[$this->alias]['topic']);
        }

        if (isset($this->data[$this->alias]['tags'])) {
            $tags = explode(',', $this->data[$this->alias]['tags']);
            $tags_ascii = explode(',', $this->data[$this->alias]['tags_ascii']);
            $this->data[$this->alias]['tags'] = array_map('trim', $tags);
            $this->data[$this->alias]['tags_ascii'] = array_map('trim', $tags_ascii);
        }

        if (isset($this->data[$this->alias]['weight']) && strlen($this->data[$this->alias]['weight'])) {
            $this->data[$this->alias]['weight'] = (int)$this->data[$this->alias]['weight'];
        }

        if (isset($this->data[$this->alias]['publish_time']) && strtotime($this->data[$this->alias]['publish_time'])) {
            $this->data[$this->alias]['publish_time'] = new MongoDate(strtotime($this->data[$this->alias]['publish_time']));
            if($this->data[$this->alias]['status'] == Configure::read('sysconfig.App.constants.STATUS_SCHEDULE')) {
                $this->data[$this->alias]['created'] = $this->data[$this->alias]['publish_time'];
            }
        }
    }

    public function afterFind($results, $primary = false)
    {
        foreach($results AS $k => $item) {
            if(isset($item[$this->alias]['tags']) && is_array($item[$this->alias]['tags'])) {
                $results[$k][$this->alias]['tags'] = implode(',', $item[$this->alias]['tags']);
            }
        }
        return parent::afterFind($results, $primary);
    }

    /**
     * check route
     * @return bool
     */
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

    /**
     * check status
     * @return bool
     */
    public function validateStatus()
    {
        $status = isset($this->data[$this->alias]['status']) ? $this->data[$this->alias]['status'] : '';
        $publish = isset($this->data[$this->alias]['publish_time']) ? $this->data[$this->alias]['publish_time'] : '';
        if($status == Configure::read('sysconfig.App.constants.STATUS_SCHEDULE')) {
            if(!strtotime($publish)) {
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

        // check case create or update
        $tag_model = new Tag();
        $id = $this->id;
        if(isset($id, $this->data[$this->alias]['tags']) && is_array($this->data[$this->alias]['tags'])) {
            // delete all old tag
            $tag_model->deleteTags($id);
            // insert all new tag
            $tag_model->createTags($this->data[$this->alias]['tags'], $this->useTable, $id);
        }
    }

    public function afterDelete()
    {
        parent::afterDelete();

        // DELETE from seo_routers
        $this->deleteSeoRouter();
        // DELETE from tags
        $this->deleteTag();
    }

    /**
     * update to seo_router when create or update a blog
     * @throws Exception
     */
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
        $save = array(
            'object_code' => $this->useTable,
            'object_id' => $id,
            'route' => $this->data[$this->alias]['seo_url'],
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

    /**
     * delete record from seo_routers table when blog was deleted
     */
    public function deleteSeoRouter()
    {
        $seo_router_model = new SeoRouter();
        $one = $seo_router_model->find('first', array('conditions' => array('object_id' => new MongoId($this->id))));
        if($one) {
            $seo_router_model->delete(new MongoId($one[$seo_router_model->alias]['id']));
        }
    }
    /**
     * delete record from tags table when blog was deleted
     */
    public function deleteTag()
    {
        $tag_model = new Tag();
        $tag_model->deleteTags($this->id);
    }

}
