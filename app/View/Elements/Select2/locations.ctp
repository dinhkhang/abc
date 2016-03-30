<?php
echo $this->element('js/select2');
?>
<script>
//	$.fn.select2.amd.require(
//			['select2/data/array', 'select2/utils'],
//			function (ArrayData, Utils) {
//				function CustomData($element, options) {
//					CustomData.__super__.constructor.call(this, $element, options);
//				}
//
//				Utils.Extend(CustomData, ArrayData);
//				CustomData.prototype.current = function (callback) {
//					var data = [];
//					var currentVal = this.$element.val();
//					var currentText = [];
//					if (!this.$element.prop('multiple')) {
//						currentVal = [currentVal];
//					}
//
//					this.$element.find('option:selected').each(function () {
//
//						var text = $(this).text();
//						currentText.push(text);
//					});
//
//					for (var v = 0; v < currentVal.length; v++) {
//						var id = currentVal[v];
//						var name = currentText[v];
//						data.push({
//							id: id,
//							name: name
//						});
//					}
//
//					callback(data);
//				};
//
//				$(".select-ajax").select2({
//					tag: true,
//					dataAdapter: CustomData,
//					ajax: {
//						type: 'POST',
//						dataType: 'json',
//						delay: 0,
//						data: function (params) {
//							return {
//								name: params.term,
//								country: $("#country").val(),
//								region: $("#region").val()
//							};
//						},
//						processResults: function (data) {
//
//							// check input region, but doesnt input contry, return false
//							if (data.type === 'country') {
//
//								$('#region').find('option').remove();
//								$("#region").select2("val", "");
//								$('#location').find('option').remove();
//								$("#location").select2("val", "");
//							}
//
//							if (data.type === 'region') {
//
//								$('#location').find('option').remove();
//								$("#location").select2("val", "");
//							}
//
//							// return data
//							return {
//								results: data.items
//							};
//						}
//					},
//					escapeMarkup: function (markup) {
//
//						return markup;
//					}, // let our custom formatter work
//					minimumInputLength: 1,
//					templateResult: formatRepo, // omitted for brevity, see the source of this page
//					templateSelection: formatRepoSelection // omitted for brevity, see the source of this page
//				});
//				// giao diện selectbox giả lập 
//				function formatRepo(repo) {
//
//					if (repo.loading) {
//
//						return repo.name;
//					}
//					var markup = '<div class="clearfix">' +
//							'<div clas="col-sm-12">' + repo.name + '</div>';
//					markup += '</div>';
//					return markup;
//				}
//				// label sẽ được trả về trong selectbox
//				function formatRepoSelection(repo) {
//					return repo.name;
//				}
//			});

		$(function () {

			$(".select-ajax").select2({
				tag: true,
				ajax: {
					type: 'POST',
					dataType: 'json',
					delay: 0,
					data: function (params) {
						return {
							name: params.term,
							country: $("#country").val(),
							region: $("#region").val()
						};
					},
					processResults: function (data) {

						// check input region, but doesnt input contry, return false
						if (data.type === 'country') {

							$('#region').find('option').remove();
							$("#region").select2("val", "");
							$('#location').find('option').remove();
							$("#location").select2("val", "");
						}

						if (data.type === 'region') {

							$('#location').find('option').remove();
							$("#location").select2("val", "");
						}

						// return data
						return {
							results: data.items
						};
					}
				},
				escapeMarkup: function (markup) {

					return markup;
				}, // let our custom formatter work
				minimumInputLength: 1,
				templateResult: formatRepo, // omitted for brevity, see the source of this page
				templateSelection: formatRepoSelection // omitted for brevity, see the source of this page
			})
					.on("change", function (e) {

						var $el = $(e.currentTarget);
						if (!$el.data('first-load')) {
							$(e.currentTarget).empty();
							$el.data('first-load', 1);
						}
					});
			// giao diện selectbox giả lập 
			function formatRepo(repo) {

				if (repo.loading) {

					return repo.name;
				}
				var markup = '<div class="clearfix">' +
						'<div clas="col-sm-12">' + repo.name + '</div>';
				markup += '</div>';
				return markup;
			}
			// label sẽ được trả về trong selectbox
			function formatRepoSelection(repo) {
				return repo.name;
			}

			$('.select2-selection__rendered').each(function () {
				$(this).text($(this).attr('title'));
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