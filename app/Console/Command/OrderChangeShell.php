<?php

class OrderChangeShell extends AppShell {

    const LIMIT = 500;

    public $uses = array('Setting');

    public function main() {

        $raw_object_type_codes = $this->args[0];
        $object_type_codes = explode(',', $raw_object_type_codes);

        $limit = !empty($this->args[1]) ? $this->args[1] : self::LIMIT;
        $options = array(
            'conditions' => array(
                'status' => Configure::read('sysconfig.App.constants.STATUS_APPROVED'),
                'modified' => array(
                    '$lte' => new MongoDate(),
                ),
            ),
            'limit' => $limit,
            'order' => array(
                'modified' => 'ASC',
                'order' => 'ASC',
            ),
        );
        foreach ($object_type_codes as $code) {

            $model_name = Inflector::classify($code);
            $this->loadModel($model_name);

            try {

                $get_records = $this->$model_name->find('all', $options);
            } catch (Exception $ex) {

                $this->logAnyFile($ex->getMessage(), __CLASS__ . '_' . __FUNCTION__);
                $this->logAnyFile($ex->getTraceAsString(), __CLASS__ . '_' . __FUNCTION__);
                $this->out(__('Some thing wrongs, error detail: %s', $ex->getMessage()));
                exit();
            }

            if (empty($get_records)) {

                $this->out(__('%s have no record to change order', $model_name));
                continue;
            }
            $max_order = count($get_records);
            foreach ($get_records as $item) {

                $order = rand(1, $max_order);
                $save_data = array(
                    'id' => $item[$model_name]['id'],
                    'order' => $order,
                );
                $this->$model_name->save($save_data);
                $this->out(__('%s with id=%s was changed order to %s', $model_name, $item[$model_name]['id'], $order));
            }
        }

        $settings = $this->Setting->find('all', array(
            'conditions' => array(
                'status' => Configure::read('sysconfig.App.constants.STATUS_APPROVED'),
            ),
        ));
        if (empty($settings)) {

            $this->out(__('Have no Setting'));
            exit();
        }
        foreach ($settings as $setting) {

            if (empty($setting['Setting']['configuration']['Home_index']['categories'])) {

                continue;
            }
            $home_cates = $setting['Setting']['configuration']['Home_index']['categories'];
            $max_home_cate_order = count($home_cates);
            $save_data = array(
                'id' => $setting['Setting']['id'],
            );
            foreach ($home_cates as $k => $v) {

                $save_data['configuration.Home_index.categories.' . $k . '.order'] = rand(1, $max_home_cate_order);
            }
            $this->Setting->save($save_data);
        }

        $this->out(__('Finished'));
    }

}
