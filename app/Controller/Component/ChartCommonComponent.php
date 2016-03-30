<?php

App::uses('Component', 'Controller');
App::import('Vendor', 'pData', array('file' => 'pChart' . DS . 'class' . DS . 'pData.class.php'));
App::import('Vendor', 'pDraw', array('file' => 'pChart' . DS . 'class' . DS . 'pDraw.class.php'));
App::import('Vendor', 'pImage', array('file' => 'pChart' . DS . 'class' . DS . 'pImage.class.php'));

class ChartCommonComponent extends Component {

    public function initialize(\Controller $controller) {

        parent::initialize($controller);

        $this->controller = $controller;
    }

    public function line($date, $chart_name, $file_name, $arr_se_points, $detect, $line_1 = false, $line_2 = false, $line_3 = false, $line_4 = false) {
        $this->autoRender = false;
        /* Create and populate the pData object */
        $MyData = new pData();
        $check_current_month = false;
        $check_last_month = false;
        if ($detect == 'revenue') {
            if (!empty($line_1)) {
                $MyData->addPoints($line_1, "Tháng " . date('m-Y', strtotime($date)));
                $MyData->setSerieWeight("Tháng " . date('m-Y', strtotime($date)), 2);
            }
            if ($line_2 && !empty($line_2)) {
                $MyData->addPoints($line_2, "Tháng " . date('m-Y', strtotime("-1 month", strtotime($date))));
                $MyData->setSerieTicks("Tháng " . date('m-Y', strtotime("-1 month", strtotime($date))), 4);
            }
        } elseif ($detect == 'user') {
            if ($line_1 && !empty($line_1)) {
                $MyData->addPoints($line_1, "DK tháng " . date('m-Y', strtotime($date)));
                $MyData->setSerieWeight("DK tháng " . date('m-Y', strtotime($date)), 2);
            }
            if ($line_2 && !empty($line_2)) {
                $MyData->addPoints($line_2, "HUY tháng " . date('m-Y', strtotime($date)));
                $MyData->setSerieTicks("HUY tháng " . date('m-Y', strtotime($date)), 3);
            }
            if ($line_3 && !empty($line_3)) {
                $MyData->addPoints($line_3, "DK tháng " . date('m-Y', strtotime("-1 month", strtotime($date))));
                $MyData->setSerieWeight("DK tháng " . date('m-Y', strtotime("-1 month", strtotime($date))), 7);
            }
            if ($line_4 && !empty($line_4)) {
                $MyData->addPoints($line_4, "HUY tháng " . date('m-Y', strtotime("-1 month", strtotime($date))));
                $MyData->setSerieTicks("HUY tháng " . date('m-Y', strtotime("-1 month", strtotime($date))), 5);
            }
        } elseif ($detect == 'sub') {
            if ($line_1 && !empty($line_1)) {
                $MyData->addPoints($line_1, "TBTC tháng " . date('m-Y', strtotime($date)));
                $MyData->setSerieWeight("TBTC tháng " . date('m-Y', strtotime($date)), 2);
            }
            if ($line_2 && !empty($line_2)) {
                $MyData->addPoints($line_2, "TBTC tháng " . date('m-Y', strtotime("-1 month", strtotime($date))), 3);
                $MyData->setSerieTicks("TBTC tháng " . date('m-Y', strtotime("-1 month", strtotime($date))), 3);
            }
        } elseif ($detect == 'top') {
            if ($line_1 && !empty($line_1)) {
                for ($i = 0; $i < count($line_1); $i++) {
                    $MyData->addPoints($line_1[$i]['data'], $line_1[$i]['name']);
                    if (($i / 2) == 0) {
                        $MyData->setSerieWeight($line_1[$i]['name'], $i);
                    } else {
                        $MyData->setSerieTicks($line_1[$i]['name'], $i);
                    }
                }
            }
        }
        $MyData->setAxisName(0, $chart_name);
        $MyData->addPoints($arr_se_points, "Labels");
        $MyData->setSerieDescription("Labels", "Months");
        $MyData->setAbscissa("Labels");

        /* Create the pChart object */
        $myPicture = new pImage(950, 450, $MyData);

        /* Write the chart title */
        $myPicture->setFontProperties(array("FontName" => APP . "Vendor/pChart/fonts/Forgotte.ttf", "FontSize" => 11));
        $myPicture->drawText(250, 55, $chart_name, array("FontSize" => 20, "Align" => TEXT_ALIGN_BOTTOMMIDDLE));

        /* Draw the scale and the 1st chart */
        $myPicture->setGraphArea(60, 60, 930, 420);
        $myPicture->drawFilledRectangle(60, 60, 930, 420, array("R" => 255, "G" => 255, "B" => 255, "Surrounding" => -200, "Alpha" => 10));
        $myPicture->drawScale(array("DrawSubTicks" => TRUE));
        $myPicture->setShadow(TRUE, array("X" => 1, "Y" => 1, "R" => 0, "G" => 0, "B" => 0, "Alpha" => 10));
        $myPicture->setFontProperties(array("FontName" => APP . "Vendor/pChart/fonts/pf_arma_five.ttf", "FontSize" => 6));
        $myPicture->drawLineChart(array("DisplayValues" => TRUE, "DisplayColor" => DISPLAY_AUTO));
        $myPicture->setShadow(FALSE);

        /* Write the chart legend */
        if ($detect == 'revenue') {
            $myPicture->drawLegend(780, 50, array("Style" => LEGEND_NOBORDER, "Mode" => LEGEND_HORIZONTAL));
        } elseif ($detect == 'user' || $detect == 'top' || $detect == 'sub') {
            $myPicture->drawLegend(580, 50, array("Style" => LEGEND_NOBORDER, "Mode" => LEGEND_HORIZONTAL));
        }

        if (!empty($myPicture->render(APP . $file_name))) {
            return '';
        } else {
            return $file_name;
        }
    }

}
