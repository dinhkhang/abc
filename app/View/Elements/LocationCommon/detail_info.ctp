<div>
    <small><?php echo __('location_common_country') ?>: 
        <?php
        echo!empty($data['Country']) ?
                $data['Country']['name'] . ' (' . $data['Country']['code'] . ')' : __('unknown');
        ?>
    </small>
    <br/>
    <small><?php echo __('location_common_region') ?>: 
        <?php
        echo!empty($data['Region']) ?
                $data['Region']['name'] : __('unknown');
        ?>
    </small>
    <br/>
    <small><?php echo __('location_common_longitude') ?>: 
        <?php
        echo!empty($data[$model_name]['loc']['coordinates'][0]) ?
                $data[$model_name]['loc']['coordinates'][0] : '';
        ?>
    </small>
    <br/>
    <small><?php echo __('location_common_latitude') ?>: 
        <?php
        echo!empty($data[$model_name]['loc']['coordinates'][1]) ?
                $data[$model_name]['loc']['coordinates'][1] : '';
        ?>
    </small>
    <br/>
</div>