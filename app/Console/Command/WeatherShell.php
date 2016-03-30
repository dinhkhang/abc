<?php

require VENDORS . 'autoload.php';
//App::uses('HttpSocket', 'Network/Http');
use GuzzleHttp\Client;
set_time_limit(-1);
ini_set('memory_limit', '256M');

class WeatherShell extends AppShell {

        const PRE_QUERY = 'http://query.yahooapis.com/v1/public/yql?q=';
        const QUERY = 'select * from weather.forecast where woeid in (select woeid from geo.places(1) where text="';
        const POST_QUERY = '") and u="c"&format=json&env=store://datatables.org/alltableswithkeys';
        const GET_API_FAIL = ' cant get info from yahoo.';
        const NETWORK_ERROR = ' network error.';
        const SAVE_DB_FAIL = ' cant get save to database.';
        const SAVE_DB_OK = ' save ok.';

        public $uses = array('Weather', 'Country', 'Region', 'WeatherDescription');
        public $key;
        public function main() {
                $this->out('Start: ' . date('d-m-Y H:i:s'));
                $listCountry = $this->Country->getListCountryCode();
                $listRegion = $this->Region->find('all', [
                    'conditions' => ['status' => 2],
                    'fields' => ['id', 'name', 'code_name', 'country_code'],
                ]);
                $listUnSuccess = array();
                foreach ($listRegion AS $key => $region) {
                        $this->key = $key;
                        $listUnSuccess = [];
                        $country_name = $listCountry[$region['Region']['country_code']];
                        $region_name = $region['Region']['name'];
                        $textSpecial = $this->convert_vi_to_en("$region_name, $country_name");

                        $link = self::PRE_QUERY . self::QUERY . $textSpecial . self::POST_QUERY;
                        try {
                                //$HttpSocket = new HttpSocket();
                                //$getWeather = $HttpSocket->get($link);
								$getWeather = $this->__getDataFromYahoo($link);
                                $weather = json_decode($getWeather, TRUE);
                                if (isset($weather['query']['results']['channel']['item']['forecast']) &&
                                        count($weather['query']['results']['channel']['item']['forecast'])) {
                                        $this->_save($getWeather, $weather, $region, $link, $textSpecial);
                                } else {
                                        $listUnSuccess[] = array(
                                            'response' => $getWeather,
                                            'text' => $textSpecial,
                                            'region' => $region['Region']['id'],
                                            'link' => $link,
                                            'error' => self::NETWORK_ERROR
                                        );
                                }
                        } catch (Exception $e) {
                                $listUnSuccess[] = array(
                                    'response' => $e->getMessage(),
                                    'text' => $textSpecial,
                                    'region' => $region['Region']['id'],
                                    'link' => $link,
                                    'error' => self::GET_API_FAIL
                                );
                        }
                }
                $this->_updateFail($listUnSuccess);
                $this->out('End: ' . date('d-m-Y H:i:s'));
        }

        protected function _updateFail($listUnSuccess = array()) {
                if (count($listUnSuccess)) {
                        foreach ($listUnSuccess AS $item) {
                                // find old region
                                $oldRegionWeathers = $this->Weather->find('first', ['conditions' => ['region' => new MongoId($item['region'])]]);
                                if (!$oldRegionWeathers) {
                                        $this->Weather->create();
                                        $oldRegionWeathers['Weather'] = array(
                                            'region' => new MongoId($item['region']),
                                            'retry' => 1,
                                            'type' => 'C',
                                        );
                                } else {
                                        $oldRegionWeathers['Weather']['retry'] += 1;
                                }
                                $oldRegionWeathers['Weather'] = array(
                                    'payload' => $item['link'],
                                    'remote_status' => 0,
                                    'message' => isset($item['message']->body) ? $item['message']->body : $item['message'],
                                    'message_variables' => isset($item['message']->raw) ? $item['message']->raw : $item['message'],
                                );
                                $this->Weather->save($oldRegionWeathers);
                                $this->out('UN-Save: [' . date('d-m-Y H:i:s') . ']['.$this->key.']=> ' . $item['error']);
                        }
                }
        }

        protected function _save($getWeather, $weather, $region, $linkYahoo, $text) {
                // find old region
                $oldRegionWeathers = $this->Weather->find('first', ['conditions' => ['region' => new MongoId($region['Region']['id'])]]);
                if (!$oldRegionWeathers) {
                        $this->Weather->create();
                        $oldRegionWeathers['Weather'] = array(
                            'region' => new MongoId($region['Region']['id']),
                            'type' => 'C',
                        );
                }

                // build object
                $infos = $save = [];
                foreach ($weather['query']['results']['channel']['item']['forecast'] AS $order => $day) {
                        $info['order'] = (int) $order;
                        $info['temperature_max'] = (int) $day['high'];
                        $info['temperature_min'] = (int) $day['low'];
                        $info['weather_description_code'] = $day['code'];
                        $info['date_affected'] = new MongoDate(strtotime($day['date']));
                        $infos[] = $info;
                }

                // save to db
                $oldRegionWeathers['Weather']['informations'] = $infos;
                $oldRegionWeathers['Weather']['current'] = [
                    'temperature' => $weather['query']['results']['channel']['item']['condition']['temp'],
                    'weather_description_code' => $weather['query']['results']['channel']['item']['condition']['code'],
                ];
                $oldRegionWeathers['Weather']['payload'] = $linkYahoo;
                $oldRegionWeathers['Weather']['remote_status'] = 1;
                $oldRegionWeathers['Weather']['retry'] = 0;
                $oldRegionWeathers['Weather']['message'] = $getWeather;
                $oldRegionWeathers['Weather']['message_variables'] = $getWeather;

                $dateTime = date('d-m-Y H:i:s');
                if ($this->Weather->save($oldRegionWeathers)) {
                        $this->out('Save: [' . $dateTime . ']['.$this->key.']=> ' . $text . self::SAVE_DB_OK);
                } else {
                        $this->out('Save: [' . $dateTime . ']['.$this->key.']=> ' . $text . self::SAVE_DB_FAIL . ' - ' . $linkYahoo);
                }
        }

        // * convert_vi_to_en method
        // * hàm chuyền đổi tiếng việt có dấu sang tiếng việt không dấu
        // * @param string $str
        // * @return string
        //
        public function convert_vi_to_en($str) {
                $str = preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/", 'a', $str);
                $str = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/", 'e', $str);
                $str = preg_replace("/(ì|í|ị|ỉ|ĩ)/", 'i', $str);
                $str = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/", 'o', $str);
                $str = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/", 'u', $str);
                $str = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/", 'y', $str);
                $str = preg_replace("/(đ)/", 'd', $str);
                $str = preg_replace("/(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)/", 'A', $str);
                $str = preg_replace("/(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)/", 'E', $str);
                $str = preg_replace("/(Ì|Í|Ị|Ỉ|Ĩ)/", 'I', $str);
                $str = preg_replace("/(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)/", 'O', $str);
                $str = preg_replace("/(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)/", 'U', $str);
                $str = preg_replace("/(Ỳ|Ý|Ỵ|Ỷ|Ỹ)/", 'Y', $str);
                $str = preg_replace("/(Đ|Ð)/", 'D', $str);
                return $str;
        }

		private function __getDataFromYahoo($link) {
			$client = new Client();
            $res = $client->get($link, array('timeout' =>  600)); // time out 10 minutes
            $getWeather = $res->getBody()->getContents();
			return $getWeather;
		}
}
