<?php
/**
 * AppShell file
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @since         CakePHP(tm) v 2.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
App::uses('Shell', 'Console');

/**
 * Application Shell
 *
 * Add your application-wide methods in the class below, your shells
 * will inherit them.
 *
 * @package       app.Console.Command
 */
class AppShell extends Shell
{

        /**
         * logAnyFile
         * 
         * @param mixed $content
         * @param string $file_name
         */
        protected function logAnyFile($content, $file_name)
        {

                CakeLog::config($file_name, array(
                    'engine' => 'File',
                    'types' => array($file_name),
                    'file' => $file_name,
                ));

                $this->log($content, $file_name);
        }

        /**
         * Đăng nhập FTP
         * @return resource
         */
        public function ftpLogin($conn = null)
        {
                if ($conn) {
                        ftp_close($conn);
                }
                $ftp_info = Configure::read('sysconfig.Shell.CdrFileCreationShell.ftp_info');
                extract($ftp_info);
                $conn_id = ftp_connect($ftp_server, $ftp_port, $ftp_timeout);
                if (!$conn_id) {
                        $this->out("FTP connection has failed!");
                        exit;
                } else {
                        $this->out("Connected to $ftp_server, for user $ftp_user_name");
                }
                $login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);
                if (!$login_result) {
                        $this->out("FTP login has failed!");
                        exit;
                } else {
                        $this->out("Logged in to $ftp_server, for user $ftp_user_name");
                }
                return $conn_id;
        }
}
