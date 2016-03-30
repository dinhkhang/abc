<?php

App::uses('Component', 'Controller');
App::uses('Folder', 'Utility');
App::uses('File', 'Utility');








class ContentCommonComponent extends Component {

        public $controller = '';

        public function initialize(\Controller $controller) {

                parent::initialize($controller);

                $this->controller = $controller;
        } 

        /**
         * Xử lý chuẩn hóa nội dung Content
         * 
         */
        public function processDescription(&$save_data, $module_name = null) {

            if (empty($module_name)) {

                    $module_name = Configure::read('sysconfig.' . $this->controller->name . '.data_file_root');
            }

            if (empty($module_name)) {

                    throw new CakeException(__('Invalid sysconfig, make sure that %s was defined', 'sysconfig.' . $this->controller->name . '.data_file_root'));
            }

            if (empty($save_data)) {

                    return false;
            }

            if (empty($save_data['description'])) {

                   $save_data['description'] = "";
                   return;
            }

            $description = $save_data['description'];

            $result = ""; 
            $description_end = "";
            while(true)
            {
                $imgpos_start = strpos($description, "<img");//vd 10 

                if ($imgpos_start > 0)
                { 
                    $imgpos_end = strpos($description, ">", $imgpos_start);//vd 12
                    if ($imgpos_end > 0)
                    { 
                        //$imgpos_end = strpos($description, "/>");
                        $description_start = substr ( $description, 0, $imgpos_start + 4);
                        $description_end = substr ( $description, $imgpos_end);
                        $imgtag = substr( $description, $imgpos_start + 4, $imgpos_end - $imgpos_start);

                        echo "imgtag:($imgtag)";

                        if (strpos($imgtag, "width") <= 0)
                        {
                            $imgtag = " width=\"100%\"" . $imgtag;
                        }
                        if (strpos($imgtag, "height") <= 0)
                        {
                            $imgtag = " height=\"100%\"" . $imgtag;
                        }
                        $result .= $description_start . $imgtag;
                        $description = $description_end; 
                    } 
                    else
                    {                    
                        $description_end = "";
                    }
                }
                else
                {
                    $result .= $description_end;
                    break;
                }

            }
            
            $save_data['description'] = $result; 
            $this->log($result);
        } 
}
