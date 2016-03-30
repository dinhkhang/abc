<?php

App::uses('Folder', 'Utility');
App::uses('File', 'Utility');
set_time_limit(-1);

class TagShell extends AppShell {

    public $uses = array('TagLite', 'Tag', 'TagClientVersion');

    const TAGS_CLIENT_DB = 'tags_client.db';
    const TAGS_CLIENT_ZIP = 'tags_client.zip';
    const TAGS_CLIENT_DIR = 'data_files/tags_client_files/';

    public function __construct($stdout = null, $stderr = null, $stdin = null) {

        $stdout = new ConsoleOutput('file://' . TMP . DS . 'logs' . DS . __CLASS__ . '.out.log');
        $stderr = new ConsoleOutput('file://' . TMP . DS . 'logs' . DS . __CLASS__ . '.err.log');
        $stdin = new ConsoleOutput('file://' . TMP . DS . 'logs' . DS . __CLASS__ . '.in.log');
        parent::__construct($stdout, $stderr, $stdin);
    }

    public function main() {

        // lấy ra modified cuối cùng trong Tag
        $get_last_modified = $this->Tag->find('first', array(
            'order' => array(
                'modified' => 'DESC',
            ),
        ));
        if (empty($get_last_modified)) {

            $this->out(__('Have no any tag in CMS'));
            $this->logAnyFile(__('Have no any tag in CMS'), __CLASS__);
            exit();
        }

        $last_modified = $get_last_modified['Tag']['modified']->sec;
        $is_generated = $this->fetchMaxModifiedFromTag();

        $tag_client_version = $this->getTagClientVersion($last_modified);
        if ($tag_client_version === false) {

            $this->out(__('Tag has no new update'));
            $this->logAnyFile(__('Tag has no new update'), __CLASS__);
            exit();
        }

        // thực hiện đếm tổng số tag trong cms
        $cms_count = $this->Tag->find('count');
        if (empty($cms_count)) {

            $this->out(__('Have no any tag in CMS'));
            $this->logAnyFile(__('Have no any tag in CMS'), __CLASS__);
            exit();
        }

        // thực hiện đếm số Tag đang public group theo name_ascii và object_type_code
        $cms_public_count = $this->Tag->find('count', array(
            'conditions' => array(
                'status' => Configure::read('sysconfig.App.constants.STATUS_APPROVED'),
            ),
        ));

        if (
                Configure::read('sysconfig.Console.FORCE_READ_ALL_TAG') ||
                empty($is_generated)
        ) {

            $this->forceGenerate();
        } else {

            $this->generate();
        }

        $this->TagClientVersion->create();
        $this->TagClientVersion->save(array(
            'version' => $tag_client_version,
            'cms_count' => $cms_count,
            'cms_public_count' => $cms_public_count,
            'lang_code' => 'vi',
        ));

        // copy tags.db to tags_client.db
        $file = new File(APP . 'tags.db', false, 0777);
        if (!$file->exists()) {

            $this->out(__('File in %s does not exist', $file->path));
            exit();
        }

        $tags_client_dir = new Folder(APP . self::TAGS_CLIENT_DIR, true, 0777);

        $fileClient = new File(APP . self::TAGS_CLIENT_DIR . self::TAGS_CLIENT_DB, false, 0777);
        $fileClient->create();

        if (!$file->copy(APP . self::TAGS_CLIENT_DIR . self::TAGS_CLIENT_DB)) {

            $this->out(__('Can not copy to %s', self::TAGS_CLIENT_DB));
            $this->logAnyFile(__('Can not copy to %s', self::TAGS_CLIENT_DB), __CLASS__);
            exit();
        }

        $output = $return_var = null;
        // thực hiện zip file lại
        // thực hiện support cho môi trường windows
        if (DIRECTORY_SEPARATOR == '\\') {

            $cmd = sprintf('cd %s && Rar a %s %s 2>&1', APP . self::TAGS_CLIENT_DIR, self::TAGS_CLIENT_ZIP, self::TAGS_CLIENT_DB);
        } else {

            $cmd = sprintf('cd %s && zip %s %s 2>&1', APP . self::TAGS_CLIENT_DIR, self::TAGS_CLIENT_ZIP, self::TAGS_CLIENT_DB);
        }
        exec($cmd, $output, $return_var);
        if (!empty($return_var)) {

            $this->out(__('Can not zip %s', self::TAGS_CLIENT_DB));
            $this->logAnyFile(__('Can not zip %s', self::TAGS_CLIENT_DB), __CLASS__);
            $this->logAnyFile($output, __CLASS__);
        }

        $this->out('Finish');
        $this->logAnyFile('Finish', __CLASS__);
    }

    protected function getTagClientVersion($last_modified) {

        $get_last_version = $this->TagClientVersion->find('first', array(
            'order' => array(
                'version' => 'DESC',
            ),
        ));

        if (empty($get_last_version)) {

            return $last_modified;
        }

        $last_version = $get_last_version['TagClientVersion']['version'];
        if ($last_version >= $last_modified) {

            return false;
        }

        return $last_modified;
    }

    protected function forceGenerate() {

        $options = array(
            'conditions' => array(
                'status' => Configure::read('sysconfig.App.constants.STATUS_APPROVED'),
        ));

        $tags = $this->Tag->find('all', $options);
        if (empty($tags)) {

            $this->out('Have no any public tag in CMS');
            $this->logAnyFile(__('Have no any public tag in CMS'), __CLASS__);
            exit();
        }

        // thực hiện xóa toàn bộ dữ liệu 
        $this->TagLite->query('DELETE FROM ' . $this->TagLite->useTable);
        foreach ($tags AS $tag) {

            if (empty($tag['Tag']['name_ascii'])) {

                continue;
            }

            $id = $tag['Tag']['id'];
            $name = $tag['Tag']['name_ascii'];
            $type = $tag['Tag']['object_type_code'];
            $modified = $tag['Tag']['modified']->sec;

            $this->TagLite->create();
            $this->TagLite->save(array(
                'id' => $id,
                'name' => $name,
                'type' => $type,
                'lang_code' => 'vi',
                'modified' => $modified,
            ));
        }
    }

    protected function generate() {

        $maxModifiedFromTagDbLite = $this->fetchMaxModifiedFromTag();
        $options = array(
            'conditions' => array(
                'modified' => array(
                    '$gt' => $maxModifiedFromTagDbLite,
                ),
        ));

        $tags = $this->Tag->find('all', $options);
        if (empty($tags)) {

            $this->out(__('Have no any tag in CMS which was changed from %s', date('d-m-Y H:i:s', $maxModifiedFromTagDbLite->sec)));
            $this->logAnyFile(__('Have no any tag in CMS which was changed from %s', date('d-m-Y H:i:s', $maxModifiedFromTagDbLite->sec)), __CLASS__);
            exit();
        }

        foreach ($tags AS $tag) {

            if (empty($tag['Tag']['name_ascii'])) {

                continue;
            }

            $id = $tag['Tag']['id'];
            $status = $tag['Tag']['status'];
            $name = $tag['Tag']['name_ascii'];
            $type = $tag['Tag']['object_type_code'];
            $modified = $tag['Tag']['modified']->sec;

            $status_approved = Configure::read('sysconfig.App.constants.STATUS_APPROVED');

            $check_exist = $this->TagLite->exists($id);
            if (!empty($check_exist)) {

                if ($status != $status_approved) {

                    $this->TagLite->delete($id);
                } else {

                    $this->TagLite->save(array(
                        'id' => $id,
                        'modified' => $modified,
                    ));
                }
            } else {

                if ($status != $status_approved) {

                    continue;
                }

                $this->TagLite->create();
                $this->TagLite->save(array(
                    'id' => $id,
                    'name' => $name,
                    'type' => $type,
                    'lang_code' => 'vi',
                    'modified' => $modified,
                ));
            }
        }
    }

    protected function fetchMaxModifiedFromTag() {

        $tagOld = $this->TagLite->find('first', array(
            'order' => array(
                'modified' => "DESC",
        )));
        return isset($tagOld['TagLite']['modified']) ? new MongoDate($tagOld['TagLite']['modified']) : '';
    }

}
