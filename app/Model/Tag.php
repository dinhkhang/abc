<?php

App::uses('AppModel', 'Model');

/**
 * Class Tag
 * @author PhuTX
 */
class Tag extends AppModel
{

    public $useTable = 'tags';

    public $customSchema = array(
        'id' => '',
        'object_id' => '',
        'object_code' => '',
        'name' => '',
        'user' => '',
        'created' => '',
        'modified' => '',
    );

    public $asciiFields = array(
        'name',
    );

    /**
     * @param $object_id
     */
    public function deleteTags($object_id) {
        $all = $this->find('all', array('conditions' => array('object_id' => new MongoId($object_id))));
        foreach($all AS $one) {
            $this->delete(new MongoId($one[$this->alias]['id']));
        }
    }

    /**
     * @param $tags
     * @param $table_name
     * @param $object_id
     * @throws Exception
     */
    public function createTags($tags, $table_name, $object_id) {
        foreach($tags AS $tag) {
            $this->create();
            $this->save(array(
                'object_code' => $table_name,
                'name' => $tag,
                'name_ascii' => $this->convert_vi_to_en($tag),
                'object_id' => new MongoId($object_id)
            ));
        }
    }
}
