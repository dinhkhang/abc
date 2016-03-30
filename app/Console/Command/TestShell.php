<?php

class TestShell extends AppShell {

        public $uses = array(
            'Place',
        );

        public function hello() {

                $this->out('Hello command');
        }

        public function place() {

                $first = $this->Place->find('first');
                debug($first);
        }

}
