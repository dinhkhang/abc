<?php
$bitrates = Configure::read('sysconfig.Streamings.bitrates');
?>
<div class="panel panel-info" data-panel_index="<?php echo $panel_index ?>" data-unique="<?php echo $unique ?>">
    <div class="panel-heading">
        <h4 class="panel-title">
            <div class="row">
                <div class="col-sm-11">
                    <a href="#collapse-<?php echo $unique ?>"  data-toggle="collapse" style="width: 90%" class="streaming-panel-title">
                        <?php
                        if (empty($panel_title)) {

                            $panel_title = !empty($request_data['file_path']) ? $request_data['file_path'] : null;
                        }
                        ?>
                        <?php echo $panel_title ?>
                    </a>
                </div>
                <div class="col-sm-1">
                    <button class="btn btn-danger panel-input-remove" type="button"><i class="fa fa-trash"></i></button>
                </div>
            </div>
        </h4>
    </div>
    <div class="panel-collapse collapse in" id="collapse-<?php echo $unique ?>">
        <div class="panel-body">
            <div class="row panel-container">
                <div class="form-horizontal">
                    <div class="form-group">
                        <label class="col-sm-2 control-label"><?php echo __('streaming_file_path') ?> </label>
                        <div class="col-sm-10">
                            <?php
                            echo $this->Form->hidden($model_name . '.' . $unique . '.' . 'object_type', array(
                                'class' => 'form-control',
                                'div' => false,
                                'label' => false,
                                'value' => $object_type_id,
                            ));
                            ?>
                            <?php
                            if ($this->action == 'edit' && !empty($request_data['id'])):
                                ?>
                                <?php
                                echo $this->Form->hidden($model_name . '.' . $unique . '.' . 'id', array(
                                    'class' => 'form-control',
                                    'div' => false,
                                    'label' => false,
                                    'value' => !empty($request_data['id']) ? $request_data['id'] : null,
                                ));
                                ?>
                                <?php
                            endif;
                            ?>
                            <?php
                            echo $this->Form->input($model_name . '.' . $unique . '.' . 'file_path', array(
                                'class' => 'form-control streaming-file-path',
                                'div' => false,
                                'label' => false,
                                'default' => !empty($request_data['file_path']) ? $request_data['file_path'] : null,
                            ));
                            ?>
                            <div class="streaming-file-url">
                                <?php
                                if (!empty($request_data['file_path'])) {

                                    $file_urls = $this->Streaming->getUrls($request_data['file_path']);
                                }
                                ?>
                                <?php if (!empty($file_urls)): ?>
                                    <?php foreach ($file_urls as $k => $url): ?>
                                        <?php
                                        $streaming_url = $url['url'];
                                        $streaming_type = $url['type'];
                                        $streaming_mime = $url['mime'];
                                        ?>
                                        <?php if ($streaming_type == 'audio'): ?>
                                            <a href="#audio-<?php echo $unique ?>-<?php echo $k ?>" data-toggle="modal" data-type="audio" data-target="#audio-<?php echo $unique ?>-<?php echo $k ?>">
                                                <?php echo $streaming_url ?>
                                            </a>
                                            <br/>
                                            <div class="modal fade player" id="audio-<?php echo $unique ?>-<?php echo $k ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-type="audio">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                    <h4 class="modal-title" id="myModalLabel">   
                                                        <?php echo $streaming_url ?>
                                                    </h4>
                                                </div>
                                                <div class="modal-body">
                                                    <audio controls>
                                                        <source src="<?php echo $streaming_url ?>" type="<?php echo $streaming_mime ?>">
                                                        <?php
                                                        echo __('streaming_not_support_audio');
                                                        ?>
                                                    </audio>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __('close_btn') ?></button>
                                                </div>
                                            </div>
                                        <?php elseif ($streaming_type == 'video'): ?>
                                            <a href="#video-<?php echo $unique ?>-<?php echo $k ?>" data-toggle="modal" data-type="video" data-target="#video-<?php echo $unique ?>-<?php echo $k ?>">
                                                <?php echo $streaming_url ?>
                                            </a>
                                            <br/>
                                            <div class="modal fade player" id="video-<?php echo $unique ?>-<?php echo $k ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-type="video">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                    <h4 class="modal-title" id="myModalLabel">
                                                        <?php echo $streaming_url ?>
                                                    </h4>
                                                </div>
                                                <div class="modal-body">
                                                    <video  controls>
                                                        <source src="<?php echo $streaming_url ?>" type="<?php echo $streaming_mime ?>">
                                                        <?php
                                                        echo __('streaming_not_support_video');
                                                        ?>
                                                    </video>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __('close_btn') ?></button>
                                                </div>
                                            </div>
                                        <?php else: ?>
                                            <a href="<?php echo $streaming_url ?>" target="_blank">
                                                <?php echo $streaming_url ?>
                                            </a>
                                            <br/>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="hr-line-dashed"></div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label"><?php echo __('streaming_duration') ?> </label>
                        <div class="col-sm-10">
                            <?php
                            echo $this->Form->input($model_name . '.' . $unique . '.' . 'duration', array(
                                'class' => 'form-control',
                                'div' => false,
                                'label' => false,
                                'type' => 'number',
                                'default' => !empty($request_data['duration']) ? $request_data['duration'] : null,
                            ));
                            ?>
                        </div>
                    </div>
                    <div class="hr-line-dashed"></div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label"><?php echo __('streaming_bitrate') ?> </label>
                        <div class="col-sm-10">
                            <?php
                            echo $this->Form->input($model_name . '.' . $unique . '.' . 'bitrate', array(
                                'class' => 'form-control',
                                'div' => false,
                                'label' => false,
                                'options' => $bitrates,
                                'default' => !empty($request_data['bitrate']) ? $request_data['bitrate'] : null,
                                'empty' => '-------',
                            ));
                            ?>
                        </div>
                    </div>
                    <div class="hr-line-dashed"></div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label"><?php echo __('streaming_resolution_w') ?> </label>
                        <div class="col-sm-10">
                            <?php
                            echo $this->Form->input($model_name . '.' . $unique . '.' . 'resolution_w', array(
                                'class' => 'form-control',
                                'div' => false,
                                'label' => false,
                                'type' => 'number',
                                'default' => !empty($request_data['resolution_w']) ? $request_data['resolution_w'] : null,
                            ));
                            ?>
                        </div>
                    </div>
                    <div class="hr-line-dashed"></div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label"><?php echo __('streaming_resolution_h') ?> </label>
                        <div class="col-sm-10">
                            <?php
                            echo $this->Form->input($model_name . '.' . $unique . '.' . 'resolution_h', array(
                                'class' => 'form-control',
                                'div' => false,
                                'label' => false,
                                'type' => 'number',
                                'default' => !empty($request_data['resolution_h']) ? $request_data['resolution_h'] : null,
                            ));
                            ?>
                        </div>
                    </div>
                    <div class="hr-line-dashed"></div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label"><?php echo __('streaming_poster') ?> </label>
                        <div class="col-sm-10">
                            <?php
                            echo $this->element('JqueryFileUpload/basic_plus_ui', array(
                                'name' => $model_name . '.' . $unique . '.files.poster',
                                'options' => array(
                                    'id' => $model_name . $unique . 'poster',
                                ),
                                'upload_options' => array(
                                    'maxNumberOfFiles' => 1,
                                ),
                                'request_data_file' => !empty($request_data['files']['poster']) ? $request_data['files']['poster'] : null,
                            ));
                            ?>
                        </div>
                    </div>
                    <div class="hr-line-dashed"></div>
                </div>
            </div>
        </div>
    </div>
</div>
