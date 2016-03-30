<?php

set_time_limit(-1);

class BackUpShell extends AppShell {

        public function main() {
                $folder = date("Ymd-His");
                $password = 'adAxx12kf55';
                $folderLocal = '/video/www/halovietnam/database-dump/';
//                $folderLocal = __FILE__;
                $command = 'mongodump --host "localhost" --username uhallovn -password ' 
					. $password . ' --db hallovn --out ' . $folderLocal . $folder;
                $this->out(date("d-m-Y H:i:s") . ': ' . $command);
                exec($command);
                $this->out('Done');
        }

}
