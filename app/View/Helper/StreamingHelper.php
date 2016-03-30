<?php

/**
 * Application level View Helper
 *
 * This file is application-wide helper file. You can put all
 * application-wide helper-related methods here.
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
 * @package       app.View.Helper
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
//App::uses('AppHelper', 'View');

/**
 * Application helper
 *
 * Add your application-wide methods in the class below, your helpers
 * will inherit them.
 *
 * @package       app.View.Helper
 */
class StreamingHelper extends AppHelper {

    public function getUrls($file_path, $file_mime = null) {

        $streaming_servers = Configure::read('sysconfig.Streamings.servers');
        if (empty($file_mime)) {

            $file_mime = $this->getMimeType($file_path);
        }

        $urls = array();
        foreach ($streaming_servers as $protocol => $streaming_server) {

            if ($this->startsWith($file_mime, "audio")) {

                $url = $streaming_server . '/vod/' . $file_path;
                $file_type = 'audio';
            } else if ($this->startsWith($file_mime, "video")) {

                if ($protocol == 'HLS' || $protocol == 'RTSP') {

                    $url = $streaming_server . '/vod/_definst_/' . $file_path;
                    if ($protocol == 'HLS') {

                        $url .= '/playlist.m3u8';
                    }
                } else {

                    $url = $streaming_server . '/vod/' . $file_path;
                }
                $file_type = 'video';
            }

            $urls[] = array(
                'url' => $url,
                'type' => $file_type,
                'mime' => $file_mime,
            );
        }

        return $urls;
    }

    protected function startsWith($haystack, $needle) {

        // search backwards starting from haystack length characters from the end
        return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== FALSE;
    }

    /**
     * getMimeType
     * nhận dạng mime type của file thông qua đuôi mở rộng
     * @param string $filename
     * @param string $mimePath
     * @return string
     */
    protected function getMimeType($filename) {
        $fileext = substr(strrchr($filename, '.'), 1);
        if (empty(
                        $fileext))
            return (false);
        $regex = "/^([\w\+\-\.\/]+)\s+(\w+\s)*($fileext\s)/i";
        $mimePath = APP . 'Config' . '/mime.types';
        $lines = file($mimePath);
        foreach ($lines as $line) {
            if (substr($line, 0, 1) == '#')
                continue; // skip comments 
            $line = rtrim($line) . " ";
            if (!preg_match($regex, $line, $matches))
                continue; // no match to the extension 
            return ($matches[1]);
        }
        return (false); // no match at all 
    }

}
