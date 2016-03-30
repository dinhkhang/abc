<?php if (!$this->request->is('ajax')): ?>
    <?php
    echo $this->start('script');
    ?>
    <script>
        $(function () {

            $('body').on('change', '.object_type', function () {

                var object_type_id = $(this).val();
                var object_type_code = $(this).find('option:selected').text();
                $('.object-text').html('<?= __("Chọn") ?> ' + object_type_code + '<span style="color: red">*</span>');
                var $region = $(this).closest('.object-by-object-type').find('.object');
                var request = '<?php echo $this->Html->url(array('action' => 'reqObjectByObjectType')) ?>';
                var req = $.post(request, {
                    object_type_id: object_type_id,
                    lang_code: Global.getUrlVars()['lang_code']
                }, function (data) {

                    if (data.error_code) {

                        alert(data.message);
                        $region.html("");
                        $region.trigger("chosen:updated");
                        return;
                    }

                    $region.html(data);
                    $region.trigger("chosen:updated");

                });

                req.error(function (xhr, status, error) {

                    alert("An AJAX error occured: " + status + "\nError: " + error + "\nError detail: " + xhr.responseText);
                });
            });
        });
    </script>
    <?php
    echo $this->end();
    ?>
<?php endif; ?>
<?php
    $disabled = isset($this->request->data[$model_name]['id']) && $this->request->data[$model_name]['id'];
?>
<div class="object-by-object-type">
    <div class="form-group">
        <label class="col-sm-2 control-label">
            <?php echo __('Object type') ?><?php echo $this->element('required'); ?>
        </label>

        <div class="col-sm-10">
            <?php
            echo $this->Form->input('object_type', array(
                'class'    => 'form-control object_type chosen-select',
                'div'      => false,
                'label'    => false,
                'required' => false,
                'disabled' => $disabled,
                'options'  => $objectType,
                'empty'    => '-------',
            ));
            ?>
        </div>
    </div>
    <div class="hr-line-dashed"></div>
    <div class="form-group">
        <label class="col-sm-2 control-label object-text">
            <?php echo __('Object') ?><?php echo $this->element('required'); ?>
        </label>

        <div class="col-sm-10">
            <?php
            echo $this->Form->input('object_id', array(
                'class'    => 'form-control object chosen-select',
                'div'      => false,
                'label'    => false,
                'required' => false,
                'disabled' => $disabled,
                'options' => isset($objects) ? $objects : []
            ));
            ?>
        </div>
    </div>
    <div class="hr-line-dashed"></div>
</div>