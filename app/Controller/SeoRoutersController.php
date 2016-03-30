<?php

App::uses('AppController', 'Controller');

class SeoRoutersController extends AppController
{

    public $uses = array(
        'SeoRouter'
    );
    public $components = array(
        'FileCommon',
        'StreamingCommon',
    );

    public function beforeFilter()
    {
        parent::beforeFilter();
        // nếu không có quyền truy cập, thì buộc user phải đăng xuất
//        if (!$this->isAllow()) {
//            return $this->redirect($this->Auth->loginRedirect);
//        }
        $this->setInit();
    }
    public function index()
    {
            $options = [
                'order' => array('modified' => 'DESC'),
                'conditions' => array(
                ),
            ];
        $this->setSearchConds($options);
        $this->Paginator->settings = $options;
        $list_data = $this->Paginator->paginate($this->modelClass);
        $this->set([
            'breadcrumb' => [
                array(
                    'url' => Router::url(array('controller'=>'SeoRouters','action' => 'index')),
                    'label' => 'Danh sách Seo Router',
                ),
            ],
            'page_title' => 'Danh sách Seo Router',
            'list_data' => $list_data
        ]);
    }


    public function edit($id = null)
    {
        if (!$this->{$this->modelClass}->exists($id)) {
            throw new NotFoundException(__('invalid_data'));
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            $save_data = $this->request->data[$this->modelClass];
            unset($save_data['object_code']);
            unset($save_data['defaults']);
            $save_data['id'] = new MongoId($id);
            if ($this->{$this->modelClass}->save($save_data)) {
                $this->Session->setFlash(__('save_successful_message'), 'default', array(), 'good');
                $this->redirect(array('action' => 'index'));
            } else {

                $this->Session->setFlash(__('save_error_message'), 'default', array(), 'bad');
            }
        }
        $this->setRequestData($id);
        $this->set([
            'breadcrumb' => [
                array(
                    'url' => Router::url(array('action' => 'index')),
                    'label' => 'Danh sách Seo Router',
                ),
                array(
                    'url' => Router::url(array('action' => __FUNCTION__, $id)),
                    'label' => __('edit_action_title'),
                )
            ],
            'page_title' => 'Danh sách Seo Route'
        ]);

        $this->render('add');
    }
    protected function setSearchConds(&$options)
    {
        if (isset($this->request->query['name']) && strlen(trim($this->request->query['name'])) > 0) {
            $name = trim($this->request->query['name']);
            $this->request->query['name'] = $name;
            $options['conditions']['name']['$regex'] = new MongoRegex("/" . mb_strtolower($name) . "/i");
        }

        if (isset($this->request->query['menu_code']) && strlen(trim($this->request->query['menu_code'])) > 0) {
            $name = trim($this->request->query['menu_code']);
            $this->request->query['menu_code'] = $name;
            $options['conditions']['menu_code']['$regex'] = new MongoRegex("/" . mb_strtolower($name) . "/i");
        }
        if (isset($this->request->query['status']) && strlen($this->request->query['status']) > 0) {
            $status = (int)$this->request->query['status'];
            $options['conditions']['status']['$eq'] = $status;
        }
    }

    protected function setInit()
    {
        $this->set('model_name', $this->modelClass);
        $this->set('spins', 'region_weekly_spins');
        $this->set('status', Configure::read('sysconfig.Menus.status'));
        $this->set('objectTypeId', $this->object_type_id);
    }

    private function setRequestData($id, $clone = false)
    {
        $request_data = $this->{$this->modelClass}->find('first', array(
            'conditions' => array(
                'id' => new MongoId($id),
            ),
        ));
        $this->FileCommon->autoSetFiles($request_data[$this->modelClass]);

        // thực hiện đọc ra thông tin streaming
        $this->StreamingCommon->autoSet($request_data, $id);

        $this->request->data = $request_data;

        if ($clone && isset($this->request->data[$this->modelClass]['id'])) {
            $this->request->data[$this->modelClass]['ref_id'] = $this->request->data[$this->modelClass]['id'];
            unset($this->request->data[$this->modelClass]['id']);
        }
    }

}
