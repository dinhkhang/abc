<?php
$unique = isset($key) ? $key : uniqid();
?>
<div class="ibox float-e-margins">
        <div class="ibox-title">
                <h5><?php echo __('hotel_room_types') ?></h5>
                <div class="ibox-tools">
                        <a class="collapse-link">
                                <i class="fa fa-chevron-up"></i>
                        </a>
                        <a class="close-link">
                                <i class="fa fa-times"></i>
                        </a>
                </div>
        </div>
        <div class="ibox-content">

                <div class="form-group">
                        <label class="col-sm-2 control-label"><?php echo __('hotel_room_types_type') ?></label>
                        <div class="col-sm-10">
                                <?php
                                echo $this->Form->input($model_name . '.room_types.' . $unique . '.type', array(
                                    'class' => 'form-control',
                                    'div' => false,
                                    'label' => false,
                                ));
                                ?>
                        </div>
                </div>
                <div class="form-group">
                        <label class="col-sm-2 control-label"><?php echo __('hotel_room_types_total') ?></label>

                        <div class="col-sm-10">
                                <?php
                                echo $this->Form->input($model_name . '.room_types.' . $unique . '.total', array(
                                    'class' => 'form-control',
                                    'div' => false,
                                    'label' => false,
                                    'type' => 'number',
                                ));
                                ?>
                        </div>
                </div>
                <div class="form-group">
                        <label class="col-sm-2 control-label"><?php echo __('hotel_room_types_capacity') ?></label>

                        <div class="col-sm-10">
<?php
echo $this->Form->input($model_name . '.room_types.' . $unique . '.capacity', array(
    'class' => 'form-control',
    'div' => false,
    'label' => false,
    'type' => 'number',
));
?>
                        </div>
                </div>
                <div class="form-group">
                        <label class="col-sm-2 control-label"><?php echo __('hotel_room_types_description') ?></label>

                        <div class="col-sm-10">
<?php
echo $this->Form->input($model_name . '.room_types.' . $unique . '.description', array(
    'class' => 'form-control',
    'div' => false,
    'label' => false,
    'type' => 'textarea',
));
?>
                        </div>
                </div>
                <div class="form-group">
                        <label class="col-sm-2 control-label"><?php echo __('hotel_room_types_order') ?></label>

                        <div class="col-sm-10">
<?php
echo $this->Form->input($model_name . '.room_types.' . $unique . '.order', array(
    'class' => 'form-control',
    'div' => false,
    'label' => false,
    'type' => 'number',
));
?>
                        </div>
                </div>
                <div class="form-group">
                        <label class="col-sm-2 control-label"><?php echo __('hotel_room_types_max') ?></label>

                        <div class="col-sm-10">
<?php
echo $this->Form->input($model_name . '.room_types.' . $unique . '.price_range.max', array(
    'class' => 'form-control',
    'div' => false,
    'label' => false,
    'type' => 'number',
));
?>
                        </div>
                </div>
                <div class="form-group">
                        <label class="col-sm-2 control-label"><?php echo __('hotel_room_types_min') ?></label>

                        <div class="col-sm-10">
<?php
echo $this->Form->input($model_name . '.room_types.' . $unique . '.price_range.min', array(
    'class' => 'form-control',
    'div' => false,
    'label' => false,
    'type' => 'number',
));
?>
                        </div>
                </div>
        </div>
</div>