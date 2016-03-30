<div class="ibox-content m-b-sm border-bottom">
    <div class="row">
        <?php
        $typePrize = array(
            'Đặc Biệt',
            'Giải Nhất',
            'Giải Nhì',
            'Giải Ba',
            'Giải Tư',
            'Giải Năm',
            'Giải Sáu',
            'Giải Bảy',
            'Giải Tám',
        );
        if (isset($datas['DateResult']['realtime_numbers']) && is_array($datas['DateResult']['realtime_numbers'])) {
            ksort($datas['DateResult']['realtime_numbers']);
            foreach ($datas['DateResult']['realtime_numbers'] AS $key => $prize) {
                echo '<div class="col-md-1"><h5>';
                echo $typePrize[$key];
                echo '</h5></div>';
                echo '<div class="col-md-11"><strong>';
                echo is_string($prize) ? $prize : implode('</strong> - <strong>', $prize);
                echo '</strong></div>';
                echo '<div class="clearfix"></div>';
            }
        } elseif (isset($datas['DateResult']['numbers']) && is_array($datas['DateResult']['numbers'])) {
            ksort($datas['DateResult']['numbers']);
            foreach ($datas['DateResult']['numbers'] AS $key => $prize) {
                echo '<div class="col-md-1"><h5>';
                echo $typePrize[$key];
                echo '</h5></div>';
                echo '<div class="col-md-11"><strong>';
                echo is_string($prize) ? $prize : implode('</strong> - <strong>', $prize);
                echo '</strong></div>';
                echo '<div class="clearfix"></div>';
            }
        }
        ?>
        <div class="clearfix"></div>

        <div class="col-md-1">Lô Tô</div>
        <div class="col-md-11">
        <span>
            <?php
            echo implode(' - ', $datas['DateResult']['lotos']);
            ?>
        </span>
        </div>
        <div class="clearfix"></div>
        <div class="hr-line-dashed"></div>

        <div class="col-md-2">Thời điểm bắt đầu nhập</div>
        <div class="col-md-10">
        <span>
            <?php
            echo isset($datas['DateResult']['created']) ? $this->Common->parseDateTime($datas['DateResult']['created']) : '';
            ?>
        </span>
        </div>
        <div class="clearfix"></div>

        <div class="col-md-2">Thời điểm kết thúc nhập</div>
        <div class="col-md-10">
        <span>
            <?php
            echo isset($datas['DateResult']['modified']) ? $this->Common->parseDateTime($datas['DateResult']['modified']) : '';
            ?>
        </span>
        </div>
        <div class="clearfix"></div>

        <div class="col-md-2">Người nhập</div>
        <div class="col-md-10">
        <span>
            <?php
            echo isset($datas['User']['username']) ? $datas['User']['username'] : '';
            ?>
        </span>
        </div>
    </div>
</div>
