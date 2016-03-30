<?php
echo $this->element('js/validate');
?>
<?php
echo $this->start('script');
?>
<script>
    $(function () {

        var $form = $('.longitude').closest('form');
        $form.validate({
            ignore: ".editor"
        });
        $(".longitude").rules("add", {
            number: true,
            range: [-180, 180]
        });
        $(".latitude").rules("add", {
            number: true,
            range: [-90, 90]
        });
    });
</script>
<?php
echo $this->end();
?>
<?php
if (!empty($this->request->data[$model_name]['location']['_id'])) {

    $location_id = $this->request->data[$model_name]['location']['_id'];
    echo $this->Form->hidden($model_name . '.location._id', array(
        'value' => $location_id,
    ));
}
//if (!empty($this->request->data[$model_name]['location']['region'])) {
//
//        $region = $this->request->data[$model_name]['location']['region'];
//        echo $this->Form->hidden($model_name . '.location.region', array(
//            'value' => $region,
//        ));
//}
if (!empty($this->request->data[$model_name]['location']['object_type'])) {

    $object_type = $this->request->data[$model_name]['location']['object_type'];
    echo $this->Form->hidden($model_name . '.location.object_type', array(
        'value' => $object_type,
    ));
}
if (!empty($this->request->data[$model_name]['loc']['type'])) {

    $loc_type = $this->request->data[$model_name]['loc']['type'];
    echo $this->Form->hidden($model_name . '.loc.type', array(
        'value' => $loc_type,
    ));
}

if (!isset($required)) {

    $required = true;
}
?>
<?php
$longitude_err = $this->Form->error($model_name . '.loc.coordinates.0');
$longitude_err_class = !empty($longitude_err) ? 'has-error' : '';
?>
<div class="form-group <?php echo $longitude_err_class ?>">
    <label class="col-sm-2 control-label">
        <?php echo __('location_common_longitude') ?>
        <?php
        if ($required) {

            echo $this->element('required');
        }
        ?>
    </label>

    <div class="col-sm-10">
        <?php
        echo $this->Form->input($model_name . '.loc.coordinates.0', array(
            'class' => 'form-control longitude',
            'div' => false,
            'label' => false,
            'required' => $required,
            'default' => 0,
        ));
        ?>
    </div>
</div>
<div class="hr-line-dashed"></div>
<?php
$latitude_err = $this->Form->error($model_name . '.loc.coordinates.1');
$latitude_err_class = !empty($latitude_err) ? 'has-error' : '';
?>
<div class="form-group <?php echo $latitude_err_class ?>">
    <label class="col-sm-2 control-label">
        <?php echo __('location_common_latitude') ?>
        <?php
        if ($required) {

            echo $this->element('required');
        }
        ?>
    </label>

    <div class="col-sm-10">
        <?php
        echo $this->Form->input($model_name . '.loc.coordinates.1', array(
            'class' => 'form-control latitude',
            'div' => false,
            'label' => false,
            'required' => $required,
            'default' => 0,
        ));
        ?>
    </div>
</div>
<div class="hr-line-dashed"></div>