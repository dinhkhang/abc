<?php
echo $this->element('js/ajax_chosen');
?>
<script>
	$(function () {

		$(".select-ajax").ajaxChosen({
			type: 'POST',
			url: '<?php echo $this->Html->url(array('action' => 'reqLocation')) ?>',
			dataType: 'json'
		}, function (data) {
			var results = [];
			$.each(data.items, function (i, val) {
				results.push({value: val.id, text: val.name});
			});
			return results;
		});
	});
</script>
<?php
$country_field = 'country';
$country_err = $this->Form->error($model_name . '.' . $country_field);
$country_err_class = !empty($country_err) ? 'has-error' : '';
?>
<div class="form-group <?php echo $country_err_class ?>">
	<label class="col-sm-2 control-label"><?php echo __('select2_country') ?> <?php echo $this->element('required') ?></label>

	<div class="col-sm-10">
		<?php
		echo $this->element('Select2/input', array(
			'input_field' => $country_field,
			'input_id' => $country_field,
			'required' => true,
			'model_name' => $model_name,
		));
		?>
	</div>
</div>
<div class="hr-line-dashed"></div>
<?php
$region_field = 'region';
$region_err = $this->Form->error($model_name . '.' . $region_field);
$region_err_class = !empty($region_err) ? 'has-error' : '';
?>
<div class="form-group <?php echo $region_err_class ?>">
	<label class="col-sm-2 control-label"><?php echo __('select2_region') ?> <?php echo $this->element('required') ?></label>

	<div class="col-sm-10">
		<?php
		echo $this->element('Select2/input', array(
			'input_field' => $region_field,
			'input_id' => $region_field,
			'model_name' => $model_name,
			'required' => true,
		));
		?>
		<span class="text-navy"><?= __('Input country first'); ?></span>
	</div>
</div>
<div class="hr-line-dashed"></div>
<?php
$location_field = 'location';
$location_err = $this->Form->error($model_name . '.' . $location_field);
$location_err_class = !empty($location_err) ? 'has-error' : '';
?>
<div class="form-group <?php echo $location_err_class ?>">
	<label class="col-sm-2 control-label"><?php echo __('select2_location') ?></label>

	<div class="col-sm-10">
		<?php
		echo $this->element('Select2/input', array(
			'input_field' => $location_field,
			'input_id' => $location_field,
			'model_name' => $model_name,
		));
		?>
	</div>
</div>
<div class="hr-line-dashed"></div>