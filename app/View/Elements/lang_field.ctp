<?php
echo $this->Form->hidden('id');
echo $this->Form->hidden('ref_id');
?>
<div class="form-group">
    <label class="col-sm-2 control-label"><?php echo __('lang_code') ?></label>

    <div class="col-sm-10">
        <?php
        if (isset($this->request->data) && isset($_GET['lang_code'])) {
            $this->request->data[$model_name]['lang_code'] = $_GET['lang_code'];
        }
        echo $this->Form->input($model_name . '.lang_code', array(
            'class' => 'form-control update-lang',
            'div' => false,
            'label' => false,
            'default' => isset($_GET['lang_code']) ? $_GET['lang_code'] : '',
            'options' => Configure::read('S.Lang'),
        ));
        ?>
    </div>
</div>
<div class="hr-line-dashed"></div>