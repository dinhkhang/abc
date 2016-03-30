<div class="child-group-container" data-input_flg="<?php echo $input_flg ?>">
	<div class="col-sm-10">
		<div class="panel panel-info">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a href="#collapse-<?php echo $panel_id ?>"  data-toggle="collapse" style="width: 90%">
						<?php  echo isset($items['description']) && strlen($items['description']) ? $items['description'] : "new element"; ?>
					</a>
				</h4>
			</div>
			<div class="panel-collapse collapse" id="collapse-<?php echo $panel_id ?>">
				<div class="panel-body">
					<div class="row panel-container">
						<?php foreach ($items as $kk => $vv): ?>
							<?php
							// nếu giá trị bên trong $vv là object, thì thực hiện chuyển sang kiểu chuỗi json
							if (is_array($vv)) {

								$vv = json_encode($vv);
							}
							echo $this->element('Configuration/input', array(
								'label' => $kk,
								'value' => $vv,
								'input_prefix' => $input_name_prefix . '.' . $kk,
								'input_flg' => $input_flg . '.' . $panel_key,
							));
							?>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-sm-2">
		<button class="btn btn-danger child-group-remove" type="button"><i class="fa fa-trash"></i></button>
		<button class="btn btn-success child-group-insert" type="button" data-items='<?php echo json_encode($items) ?>'><i class="fa fa-copy"></i></button>
	</div>
</div>
