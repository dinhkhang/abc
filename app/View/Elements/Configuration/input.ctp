<div class="input-container" data-input_flg="<?php echo$input_flg ?>">
	<div class="form-group">
		<div class="col-sm-3">
			<?php
			echo $this->Form->input($input_prefix . '.key', array(
				'label' => false,
				'div' => false,
				'class' => 'form-control',
				'value' => $label,
				'required' => true,
				'maxlength' => 255,
			));
			?>
		</div>
		<div class="col-sm-7">
			<?php
			if (mb_strlen($value) <= 50) {

				echo $this->Form->input($input_prefix . '.value', array(
					'label' => false,
					'div' => false,
					'class' => 'form-control',
					'value' => $value,
				));
			} else {

				echo $this->Form->textarea($input_prefix . '.value', array(
					'label' => false,
					'div' => false,
					'class' => 'form-control',
					'value' => $value,
				));
			}
			?>
		</div>
		<div class="col-sm-2">
			<button class="btn btn-danger input-remove" type="button"><i class="fa fa-trash"></i></button>
			<button class="btn btn-success input-insert" type="button"><i class="fa fa-plus"></i></button>
		</div>
	</div>
	<div class="hr-line-dashed"></div>
</div>