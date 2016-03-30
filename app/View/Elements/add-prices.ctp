<?php
$unique = isset($key) ? $key : uniqid();
?>
<div class="ibox float-e-margins">
        <div class="ibox-title">
                <h5><?php echo __('price_types') ?></h5>
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
                        <label class="col-sm-2 control-label"><?php echo __('price_types_name') ?> <?php echo $this->element('required') ?></label>
                        <div class="col-sm-10">
                                <?php
                                echo $this->Form->input($model_name . '.types.' . $unique . '.name', array(
                                    'class' => 'form-control',
                                    'div' => false,
                                    'label' => false,
                                    'required' => true
                                ));
                                ?>
                        </div>
                </div>
                <div class="form-group">
                        <label class="col-sm-2 control-label"><?php echo __('price_types_code') ?> <?php echo $this->element('required') ?></label>

                        <div class="col-sm-10">
                                <?php
                                echo $this->Form->input($model_name . '.types.' . $unique . '.code', array(
                                    'class' => 'form-control',
                                    'div' => false,
                                    'label' => false,
                                    'required' => true
                                ));
                                ?>
                        </div>
                </div>
                <div class="form-group">
                        <label class="col-sm-2 control-label"><?php echo __('price_types_min_qty') ?></label>

                        <div class="col-sm-10">
                                <?php
                                echo $this->Form->input($model_name . '.types.' . $unique . '.min_qty', array(
                                    'class' => 'form-control',
                                    'div' => false,
                                    'label' => false,
                                ));
                                ?>
                        </div>
                </div>
                <div class="form-group">
                        <label class="col-sm-2 control-label"><?php echo __('price_types_max_qty') ?></label>

                        <div class="col-sm-10">
                                <?php
                                echo $this->Form->input($model_name . '.types.' . $unique . '.max_qty', array(
                                    'class' => 'form-control',
                                    'div' => false,
                                    'label' => false,
                                ));
                                ?>
                        </div>
                </div>
                <div class="form-group">
                        <label class="col-sm-2 control-label"><?php echo __('price_types_order') ?></label>

                        <div class="col-sm-10">
                                <?php
                                echo $this->Form->input($model_name . '.types.' . $unique . '.order', array(
                                    'class' => 'form-control',
                                    'div' => false,
                                    'label' => false,
                                    'type' => 'number',
                                ));
                                ?>
                        </div>
                </div>
                <div class="form-group">
                        <label class="col-sm-2 control-label"><?php echo __('price_types_description') ?></label>

                        <div class="col-sm-10">
                                <?php
                                echo $this->Form->input($model_name . '.types.' . $unique . '.description', array(
                                    'type' => 'textarea',
                                    'class' => 'form-control',
                                    'div' => false,
                                    'label' => false,
                                ));
                                ?>
                        </div>
                </div>
                <?php
                $user = CakeSession::read('Auth.User');
                // ẩn edit status đối với user có type là CONTENT_EDITOR
                if ($user['type'] !== 'CONTENT_EDITOR'):
                        ?>
                        <div class="form-group">
                                <label class="col-sm-2 control-label"><?php echo __('hotel_status') ?></label>
                                <div class="col-sm-10">
                                        <?php
                                        echo $this->Form->input($model_name . '.types.' . $unique . '.status', array(
                                            'class' => 'form-control',
                                            'div' => false,
                                            'label' => false,
                                            'default' => 1,
                                            'options' => $status,
                                        ));
                                        ?>
                                </div>
                        </div>
                        <div class="hr-line-dashed"></div>
                        <?php
                endif;
                ?>
        </div>
</div>