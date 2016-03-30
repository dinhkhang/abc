<?php

App::uses('AppShell', 'Shell');

/**
 * Class CronGanShell
 */
class CronGanShell extends AppShell
{
    const MIN_SPAN = 3;
    const MAX_SPAN = 20;
    const TYPE = 'GAN';
    public $uses = array('Region', 'DateLuck', 'DateResult');

    /**
     * main function
     */
    public function main()
    {
        // get all child region
        $list_region = $this->Region->find('list', array(
            'fields' => array('id', 'code'),
            'conditions' => array('parent' => array('$ne' => null)),
        ));
        // loop region
        foreach($list_region AS $region) {
            // find 6 result lastest
            $datas = $this->DateResult->find('all', array(
                'fields' => array('lotos'),
                'conditions' => array(
                    'region_code' => $region
                ),
                'limit' => self::MAX_SPAN,
                'order' => array('modified' => 'DESC')
            ));
            $this->execute($datas, $region);
        }
        //
    }

    /**
     * @param $datas
     * @param $region
     */
    protected function execute($datas, $region) {
        $arr_number = array_fill(0, 100, 0);
        $arr_number_no_increase = array();
        foreach($datas AS $key => $data) {
            foreach($arr_number AS $k => $number) {
                if($key + 1 <= self::MIN_SPAN && in_array($number, $data['DateResult']['lotos'])) {
                    unset($arr_number[$k]);
                } elseif(in_array($number, $data['DateResult']['lotos'])) {
                    $arr_number_no_increase[] = $number;
                    if(count($arr_number_no_increase) == count($arr_number)) {
                        break;
                    }
                } else {
                    $arr_number[$k] += 1;
                }
            }
        }
        $this->DateLuck->create();
        $save = array(
            'date' => (int) date('Ymd'),
            'region_code' => $region,
            'type' => self::TYPE,
            'numbers' => array()
        );
        for($i = max($arr_number); $i <= self::MIN_SPAN; $i--) {
            $save[numbers][$i] = array_keys($arr_number, $i);
        }
        $this->DateLuck->save($save);
    }
}