<?php if (!$this->request->is('ajax')): ?>
        <?php
        echo $this->start('script');
        ?>
        <script>
                $(function () {

                    $('body').on('change', '.country_code', function () {

                        var country_code = $(this).val();
                        var $region = $(this).closest('.region-by-country').find('.region');
                        var request = '<?php echo $this->Html->url(array('action' => 'reqRegionByCountry')) ?>';
                        var req = $.post(request, {country_code: country_code, lang_code: Global.getUrlVars()['lang_code']}, function (data) {

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
<div class="region-by-country">
    <div class="form-group">
        <label class="col-sm-2 control-label">
            <?php echo __('location_common_country') ?><?php echo $this->element('required'); ?>
        </label>

        <div class="col-sm-10">
            <?php
            echo $this->Form->input($model_name . '.location.country_code', array(
                'class' => 'form-control country_code chosen-select',
                'div' => false,
                'label' => false,
                'required' => true,
                'options' => $this->request->data($model_name . '.location.country_codes'),
                'empty' => '-------',
            ));
            ?>
        </div>
    </div>
    <div class="hr-line-dashed"></div>
    <div class="form-group">
        <label class="col-sm-2 control-label">
            <?php echo __('location_common_region') ?><?php echo $this->element('required'); ?>
        </label>

        <div class="col-sm-10">
            <?php
            echo $this->Form->input($model_name . '.location.region', array(
                'class' => 'form-control region chosen-select',
                'div' => false,
                'label' => false,
                'required' => true,
                'options' => $this->request->data($model_name . '.location.regions'),
            ));
            ?>
        </div>
    </div>
    <div class="hr-line-dashed"></div>
</div>