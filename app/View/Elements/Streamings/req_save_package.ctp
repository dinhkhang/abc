<?php
$this->Helpers->load('Streaming');
?>
<div class="panel panel-primary">
    <div class="panel-heading">
        <div class="row">
            <div class="col-sm-11">
                <h4 class="panel-title">
                    <a href="#collapse-streaming"  data-toggle="collapse" style="width: 90%">
                        <?php echo __('streaming_save_title') ?>
                    </a>
                </h4>
            </div>

            <div class="col-sm-1">
                <button class="btn btn-success streaming-package-insert" type="button"><i class="fa fa-plus"></i></button>
            </div>
        </div>
    </div>

    <div class="panel-collapse collapse in" id="collapse-streaming">
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12 panel-package-container">
                    <?php if (!empty($this->request->data['Streaming']['id'])): ?>
                        <?php foreach ($this->request->data['Streaming'] as $k => $v): ?>
                            <?php
                            echo $this->element('Streamings/req_save_input', array(
                                'unique' => $v['id'],
                                'model_name' => 'Streaming',
                                'request_data' => $v,
                                'panel_index' => $k + 1,
                            ));
                            ?>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <?php
                        echo $this->element('Streamings/req_save_input', array(
                            'unique' => uniqid(),
                            'model_name' => 'Streaming',
                            'panel_title' => __('streaming_pannel_title'),
                            'panel_index' => 1,
                        ));
                        ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

</div>