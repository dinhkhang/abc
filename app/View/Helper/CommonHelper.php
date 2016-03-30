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
class CommonHelper extends AppHelper {

        /**
         * Format date from MongoDate
         * @param MonggoDate $mongoDatetime
         * @return string
         */
        public function parseDate($mongoDatetime) {

                if ($mongoDatetime instanceof MongoDate) {

                        return date("d-m-Y", $mongoDatetime->sec);
                }

                return $mongoDatetime ? date("d-m-Y", strtotime($mongoDatetime)) : '';
        }

        /**
         * Format time from MongoDate
         * @param MongoDate $mongoDatetime
         * @return string
         */
        public function parseTime($mongoDatetime) {

                if ($mongoDatetime instanceof MongoDate) {

                        return date("H:i:s", $mongoDatetime->sec);
                }

                return $mongoDatetime ? date("H:i:s", strtotime($mongoDatetime)) : '';
        }

        /**
         * Format date from MongoDate
         * @param MonggoDate $mongoDatetime
         * @return string
         */
        public function parseDateTime($mongoDatetime) {

                if ($mongoDatetime instanceof MongoDate) {

                        return date("d-m-Y H:i:s", $mongoDatetime->sec);
                }

                return $mongoDatetime ? date("d-m-Y H:i:s", strtotime($mongoDatetime)) : '';
        }

        /**
         * Format time from MongoId
         * @param MongoId $mongoId
         * @return string
         */
        public function parseId($mongoId) {

                if ($mongoId instanceof MongoId) {

                        return (string) $mongoId;
                }

                return $mongoId;
        }

        /**
         * Get string location_id
         * @param array $data
         */
        public function getLocationId($data) {
                if (isset($data['location']['_id'])) {
                        return $data['location']['_id']->{'$id'};
                }
        }

        function formatSizeUnits($bytes) {

                if ($bytes >= 1073741824) {

                        $bytes = number_format($bytes / 1073741824, 2) . ' GB';
                } elseif ($bytes >= 1048576) {

                        $bytes = number_format($bytes / 1048576, 2) . ' MB';
                } elseif ($bytes >= 1024) {

                        $bytes = number_format($bytes / 1024, 2) . ' KB';
                } elseif ($bytes > 1) {

                        $bytes = $bytes . ' bytes';
                } elseif ($bytes == 1) {

                        $bytes = $bytes . ' byte';
                } else {

                        $bytes = '0 bytes';
                }

                return $bytes;
        }

        public function format_report_date($date,$separator = '-'){
            if( !empty($date) && is_numeric($date) && $date > 9999999 && $date < 100000000 ){
                return substr($date, 6, 2).$separator.substr($date, 4, 2).$separator.substr($date, 0, 4);
            }
            return $date;
        }

        public function format_report_week($week){
            if(!empty($week) && is_numeric($week) && $week > 99999 && $week < 1000000  ){
                return "Tuần ".substr($week, 4, 2).' - '.substr($week, 0, 4);
            }
            return $week;
        }

        public function format_report_month($month){
            if(!empty($month) && is_numeric($month) && $month > 99999 && $month < 1000000  ){
                return "Tháng ".substr($month, 4, 2).' - '.substr($month, 0, 4);
            }
            return $month;
        }

        public function format_report_quarter($quarter){
            if( !empty($quarter) && is_numeric($quarter) && $quarter > 99999 && $quarter < 1000000 ){
                return "Quý ".substr($quarter, 4, 2).' - '.substr($quarter, 0, 4);
            }
            return $quarter;
        }

        public function format_report_year($year){
            if( !empty($quarter) && is_numeric($year)){
                return "Năm ".$year;
            }
            return $year;
        }

        public function format_number($number)
        {
            return number_format($number,0,',','.');
        }

        public function add_plus_character($phone) {

            $target = $phone;
            $first = substr($phone, 0, 2);
            if ($first == '84') {

                $target = '+'.$phone;
            }

            return $target;
        }

        public function nice_money($money)
        {
            return number_format($money,0,",",".");
        }

        /**
        * Chuyển đổi giải sang từng chữ số, mỗi chữ số đặt trong 1 thẻ span, kèm theo thông
        * tin về tọa độ của chữ số đó (nằm trong mảng 106 chữ số phân tách từ 1 bộ kết quả)
        * @author Ungnv
        * @param $prize: 1 giải
        * @param $type: loại giải (Đặc biệt, nhất, nhì,...)
        * @param $prefixId: tiền tố để phân biệt các bảng với nhau, là ngày với định dạng YYYYMMDD
        */
        public function showPrize($prize, $type, $prefixId)
        {
            $returnString = "";
            switch ($type) {
                case 0:
                    $index = 0;
                    $className = 'prize-db';
                    break;
                
                case 1:
                    $index = 5;
                    $className = 'prize-1';
                    break;
                
                case 2:
                    $index = 10;
                    $className = 'prize-2';
                    break;

                case 3:
                    $index = 20;
                    $className = 'prize-3';
                    break;
                
                case 4:
                    $index = 50;
                    $className = 'prize-4';
                    break;
                
                case 5:
                    $index = 66;
                    $className = 'prize-5';
                    break;
                
                case 6:
                    $index = 90;
                    $className = 'prize-6';
                    break;
                
                case 7:
                    $index = 99;
                    $className = 'prize-7';
                    break;
                
                default:
                    # code...
                    break;
            }
            if(is_array($prize) && !empty($prize)){ //array('12345','45454','12121')
                
                foreach ($prize as $item) {
                    $returnString .= "<div class='prize-item ".$className."'>";

                    $arr = array_map('intval', str_split($item));
                    if(is_array($arr) && !empty($arr)){
                        
                        foreach ($arr as $key => $value) {
                            $id = $prefixId."_".$index;
                            $returnString .= "<span class='prize-number position-".$index."' id='".$id."'>";
                            $returnString .= $value;
                            $returnString .= "</span>";
                            $index ++;
                        }
                        
                    }

                    $returnString .= "</div>";

                }

            }else{
                $returnString .= "<div class='prize-item ".$className."'>";

                $arr = array_map('intval', str_split($prize));
                if(is_array($arr) && !empty($arr)){
                    
                    foreach ($arr as $key => $value) {

                        $id = $prefixId."_".$index;
                        $returnString .= "<span class='prize-number position-".$index."' id='".$id."'>";
                        $returnString .= $value;
                        $returnString .= "</span>";
                        $index ++;
                        
                    }
                    
                }

                $returnString .= "</div>";
            }

            return $returnString;
        }

        /**
        * Sắp xếp 1 dãy lucky loto theo thứ tự tăng dần
        * @author Ungnv
        * @param $arrayLoto
        */
        public function lotoSort(&$arrayLoto)
        {
            if(is_array($arrayLoto) && !empty($arrayLoto)){

                for ($i=1; $i < count($arrayLoto); $i++) { 

                    for ($j=0; $j < count($arrayLoto) - 1; $j++) { 

                        if( $arrayLoto[$j]['number'] >= $arrayLoto[$j+1]['number'] ){

                            $temp = $arrayLoto[$j];
                            $arrayLoto[$j] = $arrayLoto[$j+1];
                            $arrayLoto[$j+1] = $temp;

                        }

                    }

                }

            }
            return;
        }

        /**
        * Chuyển đổi ngày tháng kiểu int thành dạng DD-MM-YYYY
        * @author Ungnv
        * @param $date: kiểu int, định dạng YYYYMMDD
        */
        public function convertDateFromDb($date,$separator = '-'){

            if( !empty($date) && is_numeric($date) && $date > 9999999 && $date < 100000000 ){

                return substr($date, 6, 2).$separator.substr($date, 4, 2).$separator.substr($date, 0, 4);

            }
            return $date;

        }

        /**
        * Phân tách 1 mảng loto thành 1 mảng phân định theo đầu số của loto
        * @param $lotoArray: mảng loto, mỗi loto là 1 số gồm 2 chữ số
        */
        public function parseLotoArray($lotoArray){

            $returnArray = array();

            if (!empty($lotoArray)) {

                foreach ($lotoArray as $loto) {

                    $split = array_map('intval', str_split($loto));
                    $returnArray[$split[0]][] = $split[1];

                }

            }

            return $returnArray;

        }

        /**
        * Trả về chuỗi ghép từ các phần tử của mảng, phân tách nhau bởi ký tự định trước
        * @param $arrNumber: mảng cần ghép
        * @param $separator: ký tự để phân tách khi nối thành chuỗi
        */
        public function implodeArray($arrNumber, $separator = ','){

            $returnString = '';

            if ( is_array($arrNumber) && !empty($arrNumber)) {

                $returnString = implode($separator, $arrNumber);

            }

            return $returnString;

        }

        public function getTimeTurn($str = '') {
            if(isset($str)) {
                $time = str_split($str, 2);
                array_pop($time);
                return implode('h', $time);
            }
            return '';
        }

}
