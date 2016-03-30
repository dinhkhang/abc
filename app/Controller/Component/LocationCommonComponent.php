<?php

App::uses('Component', 'Controller');

class LocationCommonComponent extends Component
{

    public $controller = '';
    public $models = array('Country', 'Region', 'Location');
    public $default_settings = array(
        // các trường field khi giá trị thay đổi cần update vào location collection
        'listener_fields'  => array(
            'name',
            'address',
            'location.country_code',
            'location.region',
            'loc.coordinates.0',
            'loc.coordinates.1',
        ),
        // đồng nhất hóa kiểu dữ liệu
        'transform_fields' => array(
            'location._id',
            'location.region',
            'location.object_type',
        ),
    );

    public function initialize(\Controller $controller)
    {

        parent::initialize($controller);

        $this->controller = $controller;

        // Nạp vào các Model liên quan
        foreach ($this->models as $model) {

            if (!isset($this->controller->$model)) {

                $this->controller->loadModel($model);
            }
        }

        $setting = array();
        if (!empty($this->default_settings)) {

            foreach ($this->default_settings as $k => $v) {

                if (isset($this->settings[$k])) {

                    $setting[$k] = $this->settings[$k];
                } else {

                    $setting[$k] = $v;
                }
            }
        }
        $this->settings = $setting;


        $model_name = $this->controller->modelClass;
        if (!empty($this->settings['model_name'])) {

            $model_name = $this->settings['model_name'];
        }
        $this->controller->$model_name->Behaviors->load('LocationCommon');
    }

    public function autoInit($model_name = null)
    {

        if (empty($model_name)) {

            $model_name = $this->controller->modelClass;
        }

        $country_codes = $this->controller->getList('Country', array(
            'fields' => array(
                'code', 'name',
            ),
        ));
        $this->controller->request->data[$model_name]['location']['country_codes'] = $country_codes;

        if (!empty($this->controller->request->data[$model_name]['location']['country_code'])) {

            $opt_regions = array();
            $opt_regions['conditions']['country_code'] = $this->controller->request->data[$model_name]['location']['country_code'];
            $regions = $this->controller->getList('Region', $opt_regions);

            $this->controller->request->data[$model_name]['location']['regions'] = $regions;
        } else {

            $this->controller->request->data[$model_name]['location']['regions'] = array();
        }
    }

    public function reqRegionByCountry()
    {

        $this->controller->layout = 'ajax';

        $country_code = $this->controller->request->data('country_code');

        $options = array();
        $options['conditions']['status']['$eq'] = Configure::read('sysconfig.App.constants.STATUS_APPROVED');
        $options['conditions']['country_code']['$eq'] = $country_code;

        if (isset($this->controller->request->data['lang_code'])) {
            $options['conditions']['lang_code'] = $this->controller->request->data['lang_code'];
        }
        $options['fields'] = array(
            'id', 'name',
        );

        $data = $this->controller->Region->find('list', $options);
        $this->controller->set('data', $data);
        $this->controller->render('/Elements/Req/option_select');
    }

    public function autoProcess(&$save_data, $options = array())
    {

        // xóa đi dữ liệu thừa
        unset($save_data['location']['country_codes']);
        unset($save_data['location']['regions']);

        // xác định object_type
        if (isset($options['object_type_id'])) {

            $object_type_id = $options['object_type_id'];
        } else {

            $object_type_id = $this->controller->object_type_id;
        }

        // kiểm tra tính hợp lệ của dữ liệu
        if (empty($save_data['location']['country_code']) || empty($save_data['location']['region'])) {

            return false;
        }

        $country_code = $save_data['location']['country_code'];
        $region = $save_data['location']['region'];
        $address = !empty($save_data['address']) ? trim($save_data['address']) : '';
        $location_field = array(
            'country_code' => $country_code,
            'region'       => new MongoId($region),
            'object_type'  => new MongoId($object_type_id),
        );

        $save_data['loc']['coordinates'][0] = trim($save_data['loc']['coordinates'][0]);
        $save_data['loc']['coordinates'][1] = trim($save_data['loc']['coordinates'][1]);

        // khi chưa tồn tại location thì thực hiện insert
        if (
            empty($save_data['location']['_id']) &&
            isset($save_data['loc']['coordinates'][0]) &&
            isset($save_data['loc']['coordinates'][1])
        ) {

            $save_data['loc']['type'] = Configure::read('sysconfig.App.GeoJSON_type');
            $save_data['loc']['coordinates'][0] = floatval($save_data['loc']['coordinates'][0]);
            $save_data['loc']['coordinates'][1] = floatval($save_data['loc']['coordinates'][1]);

            $location_data = array(
                'country_code' => $country_code,
                'region'       => $region,
                'loc'          => array(
                    'type'        => Configure::read('sysconfig.App.GeoJSON_type'),
                    'coordinates' => $save_data['loc']['coordinates'],
                ),
                'name'         => $save_data['name'],
                'status'       => isset($save_data['status']) ?
                    $save_data['status'] : Configure::read('sysconfig.App.constants.STATUS_WAIT_REVIEW'),
                'address'      => $address,
                'object_type'  => new MongoId($object_type_id),
            );
            $this->controller->Location->create();
            $this->controller->Location->save($location_data);
            $location_id = $this->controller->Location->getLastInsertId();

            $location_field['_id'] = new MongoId($location_id);

            $save_data['location'] = $location_field;
        } // chú ý không thể thay đổi object_type sau khi đã insert
        elseif (
            !empty($save_data['location']['_id'])
        ) {

            // thực hiện transform kiểu dữ liệu
            foreach ($this->settings['transform_fields'] as $path) {

                $index = $this->makeIndexArray($path);
                $evaluate = eval('return (isset($save_data' . $index . ') && !($save_data' . $index . ' instanceof MongoId));');
                if ($evaluate) {

                    eval('$save_data' . $index . '= new MongoId($save_data' . $index . ');');
                }
            }
        }
    }

    public function autoSet(&$request_data)
    {

        // thực hiện lấy ra options
        if (isset($request_data['location']['country_code'])) {

            $country_codes = $this->controller->getList('Country', array(
                'fields' => array(
                    'code', 'name',
                ),
            ));
            $request_data['location']['country_codes'] = $country_codes;
        }

        if (isset($request_data['location']['region'])) {

            $opt_regions = array();
            $opt_regions['conditions']['country_code'] = $request_data['location']['country_code'];
            $regions = $this->controller->getList('Region', $opt_regions);

            $request_data['location']['regions'] = $regions;
        }

        // thực hiện biến kiểu dữ liệu
        foreach ($this->settings['transform_fields'] as $path) {

            $index = $this->makeIndexArray($path);
            $evaluate = eval('return (isset($request_data' . $index . ') && $request_data' . $index . ' instanceof MongoId);');
            if ($evaluate) {

                eval('$request_data' . $index . '= (string) $request_data' . $index . ';');
            }
        }
    }

    public function setInfo(&$list_data, $model_name = null)
    {

        if (empty($list_data)) {

            return;
        }

        if (empty($model_name)) {

            $model_name = $this->controller->modelClass;
        }

        $country_infos = array();
        $region_infos = array();
        foreach ($list_data as $k => $v) {

            $country_code = $v[$model_name]['location']['country_code'];
            $country_lang = $v[$model_name]['lang_code'];
            $key = $country_code . '_' . $country_lang;
            if (!isset($country_infos[$key])) {

                $country_info = $this->controller->Country->find('first', array(
                    'conditions' => array(
                        'code' => $country_code,
                        'lang_code' => $country_lang
                    ),
                ));

                $country_infos[$key] = !empty($country_info['Country']) ? $country_info['Country'] : array();
            }

            $region = $v[$model_name]['location']['region'];
            $region_id = (string)$region;
            if (!isset($region_infos[$region_id])) {

                $region_info = $this->controller->Region->find('first', array(
                    'conditions' => array(
                        'id' => $region,
                        'lang_code' => $country_lang
                    ),
                ));

                $region_infos[$region_id] = !empty($region_info['Region']) ? $region_info['Region'] : array();
            }

            $list_data[$k]['Country'] = $country_infos[$key];
            $list_data[$k]['Region'] = $region_infos[$region_id];
        }
    }

    protected function makeIndexArray($path)
    {

        if (empty($path)) {

            return;
        }

        $extract = explode('.', $path);
        $index_path = '';
        foreach ($extract as $v) {

            if (is_numeric($v)) {

                $index_path .= '[' . $v . ']';
            } else {

                $index_path .= '["' . $v . '"]';
            }
        }

        return $index_path;
    }

}
