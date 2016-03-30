<div class="col-lg-6">
	<div class="ibox float-e-margins">
		<div class="ibox-title">
			<h5><?php echo __('qes_no_title_prefix') . ' ' . $index ?></h5>
			<div class="ibox-tools">
				<a class="collapse-link qes-collapse-link">
					<i class="fa fa-chevron-up"></i>
				</a>
				<a class="close-link">
					<i class="fa fa-times"></i>
				</a>
			</div>
		</div>
		<div class="ibox-content">
			<div class="form-group">

				<label class="col-lg-12 control-label qes-label">
					<?php echo __('qes_content') ?> <?php echo $this->element('required') ?>
				</label>

				<div class="col-lg-12">
					<?php
					echo $this->Form->hidden($input_name_prefix . '.index', array(
						'value' => $index - 1,
					));
					echo $this->Form->textarea($input_name_prefix . '.content', array(
						'class' => 'form-control',
						'div' => false,
						'label' => false,
						'required' => true,
						'rows' => 5,
					));
					?>
				</div>
			</div>
			<?php if ($this->action == 'edit'): ?>
				<div class="form-group">

					<label class="col-lg-12 control-label qes-label">
						<?php echo __('qes_content_unsigned') ?>
					</label>

					<div class="col-lg-12">
						<?php
						echo $this->Form->textarea($input_name_prefix . '.content_unsigned', array(
							'class' => 'form-control',
							'div' => false,
							'label' => false,
							'required' => true,
							'rows' => 5,
							'disabled' => true,
						));
						?>
					</div>
				</div>
			<?php endif; ?>
			<div class="form-group">

				<label class="col-lg-12 control-label qes-label">
					<?php echo __('qes_answer') ?> <?php echo $this->element('required') ?>
				</label>

				<div class="col-lg-12">
					<?php
					echo $this->Form->input($input_name_prefix . '.answer', array(
						'class' => 'form-control',
						'div' => false,
						'label' => false,
						'required' => true,
					));
					?>
				</div>
			</div>
			<div class="form-group">

				<label class="col-lg-12 control-label qes-label">
					<?php echo __('qes_point') ?> <?php echo $this->element('required') ?>
				</label>

				<div class="col-lg-12">
					<?php
					echo $this->Form->input($input_name_prefix . '.point', array(
						'class' => 'form-control',
						'div' => false,
						'label' => false,
						'required' => true,
						'type' => 'number',
					));
					?>
				</div>
			</div>
			<div class="form-group">

				<label class="col-lg-12 control-label qes-label">
					<?php echo __('qes_answer_time') ?>
				</label>

				<div class="col-lg-12">
					<?php
					echo $this->Form->input($input_name_prefix . '.answer_time', array(
						'class' => 'form-control',
						'div' => false,
						'label' => false,
//						'required' => true,
						'type' => 'number',
					));
					?>
				</div>
			</div>
		</div>
	</div>
</div>