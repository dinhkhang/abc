<?php

App::import('Vendor', 'Html2Text', array('file' => 'Html2Text' . DS . 'Html2Text.php'));

class Html2TextUtility {

        static public function getText($raw_content) {

                if (empty($raw_content)) {

                        return $raw_content;
                }

                // nếu chuỗi là HTML
                if (self::isHTML($raw_content)) {

                        // bước xử lý tạo tránh lỗi HTML chứa UTF-8 bị lỗi
//                        $content = self::decodeHtmlEnt($raw_content);

                        $plain_text = new \Html2Text\Html2Text($raw_content);
                        return $plain_text->getText();
                } else {

                        return $raw_content;
                }
        }

        /**
         * isHTML
         * xác định xem chuỗi string có phải chuối HTML không?
         * 
         * @param string $string
         * @return boolean
         */
        static public function isHTML($string) {

                if ($string != strip_tags($string)) {

                        return true;
                }

                return false;
        }

        /**
         * Hàm chuyển đổi mã kí tự html có chứa kí tự UTF-8 không bị lỗi trên linux
         * 
         * @param string $str
         * @return string
         */
        static public function decodeHtmlEnt($str) {

                $ret = html_entity_decode($str, ENT_COMPAT, 'UTF-8');
                $p2 = -1;

                for (;;) {

                        $p = strpos($ret, '&#', $p2 + 1);

                        if ($p === FALSE) {

                                break;
                        }

                        $p2 = strpos($ret, ';', $p);

                        if ($p2 === FALSE) {

                                break;
                        }

                        if (substr($ret, $p + 2, 1) == 'x') {

                                $char = hexdec(substr($ret, $p + 3, $p2 - $p - 3));
                        } else {

                                $char = intval(substr($ret, $p + 2, $p2 - $p - 2));
                        }

                        //echo "$char\n";
                        $newchar = iconv(
                                'UCS-4', 'UTF-8', chr(($char >> 24) & 0xFF) . chr(($char >> 16) & 0xFF) . chr(($char >> 8) & 0xFF) . chr($char & 0xFF)
                        );

                        //echo "$newchar<$p<$p2<<\n";
                        $ret = substr_replace($ret, $newchar, $p, 1 + $p2 - $p);

                        $p2 = $p + strlen($newchar);
                }

                return $ret;
        }

}
