<?php
// sử dụng công cụ soạn thảo
echo $this->element('js/tinymce');
// sử dụng upload file
echo $this->element('JqueryFileUpload/basic_plus_ui_assets');
echo $this->element('js/datetimepicker');
echo $this->Html->css('plugins/bootstrap-tagsinput/bootstrap-tagsinput');
echo $this->Html->script('plugins/bootstrap-tagsinput/bootstrap-tagsinput');
echo $this->Html->script('location');
echo $this->Html->script('search');
echo $this->Html->script('plugins/slugify/jquery.slugify');
$user = CakeSession::read('Auth.User');
$permissions = $user['permissions'];
?>
<script>
    $('document').ready(function () {
        $('#slug').slugify('#title');
        $('.bootstrap-tagsinput').addClass('col-sm-12');
    });
</script>
<div class="row">
    <div class="col-lg-12">
        <div class="ibox float-e-margins">
            <div class="ibox-content">
                <?php
                echo $this->Form->create($model_name, array(
                    'class' => 'form-horizontal',
                ));
                ?>
                <?php
                $name_err = $this->Form->error($model_name . '.parent');
                $name_err_class = !empty($name_err) ? 'has-error' : '';
                ?>
                <div class="form-group <?php echo $name_err_class ?>">
                    <label
                        class="col-sm-2 control-label"><?php echo __('blog_topic') ?> <?php echo $this->element('required') ?></label>

                    <div class="col-sm-10">
                        <?php
                        echo $this->Form->input($model_name . '.topic', array(
                            'class' => 'form-control',
                            'div' => false,
                            'label' => false,
                            'required' => true,
                            'empty' => '-------',
                            'options' => $topics,
                        ));
                        ?>
                    </div>
                </div>
                <div class="hr-line-dashed"></div>
                <?php
                $name_err = $this->Form->error($model_name . '.name');
                $name_err_class = !empty($name_err) ? 'has-error' : '';
                ?>
                <div class="form-group <?php echo $name_err_class ?>">
                    <label
                        class="col-sm-2 control-label"><?php echo __('blog_name') ?> <?php echo $this->element('required') ?></label>

                    <div class="col-sm-10">
                        <?php
                        echo $this->Form->input($model_name . '.name', array(
                            'id' => 'title',
                            'class' => 'form-control',
                            'div' => false,
                            'label' => false,
                            'required' => true,
                            'maxlength' => 160,
                        ));
                        ?>
                    </div>
                </div>
                <div class="hr-line-dashed"></div>
                <?php
                $name_err = $this->Form->error($model_name . '.seo_url');
                $name_err_class = !empty($name_err) ? 'has-error' : '';
                ?>
                <div class="form-group <?php echo $name_err_class ?>">
                    <label
                        class="col-sm-2 control-label"><?php echo __('blog_seo_url') ?> <?php echo $this->element('required') ?></label>

                    <div class="col-sm-10">
                        <?php
                        echo $this->Form->input($model_name . '.seo_url', array(
                            'id' => 'slug',
                            'class' => 'form-control',
                            'div' => false,
                            'label' => false,
                            'required' => true,
                        ));
                        ?>
                    </div>
                </div>
                <div class="hr-line-dashed"></div>
                <?php
                $name_err = $this->Form->error($model_name . '.meta_tags');
                $name_err_class = !empty($name_err) ? 'has-error' : '';
                ?>
<!--                <div class="form-group --><?php //echo $name_err_class ?><!--">-->
<!--                    <label-->
<!--                        class="col-sm-2 control-label">--><?php //echo __('blog_meta_tags') ?><!--</label>-->
<!---->
<!--                    <div class="col-sm-10">-->
<!--                        --><?php
//                        echo $this->Form->input($model_name . '.meta_tags', array(
//                            'type' => 'textarea',
//                            'class' => 'form-control',
//                            'div' => false,
//                            'label' => false,
//                        ));
//                        ?>
<!--                    </div>-->
<!--                </div>-->
                <div class="hr-line-dashed"></div>
                <?php
                $name_err = $this->Form->error($model_name . '.keyword');
                $name_err_class = !empty($name_err) ? 'has-error' : '';
                ?>
                <div class="form-group <?php echo $name_err_class ?>">
                    <label
                        class="col-sm-2 control-label"><?php echo __('blog_keyword') ?> <?php echo $this->element('required') ?></label>

                    <div class="col-sm-10">
                        <?php
                        echo $this->Form->input($model_name . '.keyword', array(
                            'class' => 'form-control',
                            'div' => false,
                            'label' => false,
                            'required' => true,
                            'maxlength' => 160,
                        ));
                        ?>
                    </div>
                </div>
                <div class="hr-line-dashed"></div>
<!--                <div class="form-group">-->
<!--                    <label class="col-sm-2 control-label">--><?php //echo __('blog_short_description') ?><!--</label>-->
<!---->
<!--                    <div class="col-sm-10">-->
<!--                        --><?php
//                        echo $this->Form->input($model_name . '.short_description', array(
//                            'type' => 'textarea',
//                            'class' => 'form-control',
//                            'div' => false,
//                            'label' => false,
//                            'maxlength' => 500,
//                        ));
//                        ?>
<!--                    </div>-->
<!--                </div>-->
                <div class="hr-line-dashed"></div>
                <div class="form-group">
                    <label class="col-sm-2 control-label"><?php echo __('blog_description') ?></label>

                    <div class="col-sm-10">
                        <?php
                        echo $this->Form->input($model_name . '.description', array(
                            'type' => 'textarea',
                            'class' => 'form-control editor',
                            'div' => false,
                            'label' => false,
                            'cols'=>"40",
                            'rows'=>"20"
                        ));
                        ?>
                    </div>
                </div>
                <div class="hr-line-dashed"></div>
                <?php
                $name_err = $this->Form->error($model_name . '.tags');
                $name_err_class = !empty($name_err) ? 'has-error' : '';
                ?>
                <div class="form-group <?php echo $name_err_class ?>">
                    <label
                        class="col-sm-2 control-label"><?php echo __('blog_tags') ?></label>

                    <div class="col-sm-10">
                        <?php
                        echo $this->Form->input($model_name . '.tags', array(
                            'class' => 'form-control',
                            'div' => false,
                            'label' => false,
                            'data-role' => 'tagsinput'
                        ));
                        ?>
                    </div>
                </div>
                <div class="hr-line-dashed"></div>
                <div class="form-group">
                    <label class="col-sm-2 control-label"><?php echo __('blog_weight') ?></label>

                    <div class="col-sm-10">
                        <?php
                        echo $this->Form->input($model_name . '.weight', array(
                            'type' => 'number',
                            'class' => 'form-control',
                            'div' => false,
                            'label' => false,
                        ));
                        ?>
                    </div>
                </div>
                <div class="hr-line-dashed"></div>
                <div class="form-group">
                    <label class="col-sm-2 control-label"><?php echo __('Logo file') ?></label>

                    <div class="col-sm-10">
                        <?php
                        echo $this->element('JqueryFileUpload/basic_plus_ui', array(
                            'name' => $model_name . '.files.logo',
                            'options' => array(
                                'id' => 'logo',
                            ),
                            'upload_options' => array(
                                'maxNumberOfFiles' => 1,
                            ),
                        ));
                        ?>
                    </div>
                </div>
                <div class="hr-line-dashed"></div>
                <?php
                // ẩn edit status đối với user có type là CONTENT_EDITOR
                if (in_array('Topics_edit_status_field', $permissions)):
                    ?>
                    <div class="form-group">
                        <label class="col-sm-2 control-label"><?php echo __('blog_status') ?></label>

                        <div class="col-sm-10">
                            <?php
                            echo $this->Form->input($model_name . '.status', array(
                                'class' => 'form-control',
                                'div' => false,
                                'label' => false,
                                'default' => 1,
                                'options' => $status,
                            ));
                            ?>
                        </div>
                    </div>
                    <div class="hr-line-dashed"></div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label"><?php echo __('blog_publish_time') ?></label>

                        <div class="col-sm-10">
                            <?php
                            echo $this->Form->input($model_name . '.publish_time', array(
                                'class' => 'form-control datepicker',
                                'div' => false,
                                'label' => false,
                                'required' => false,
                                'value' => isset($this->request->data[$model_name]['publish_time']) ? $this->Common->parseDateTime($this->request->data[$model_name]['publish_time']) : '',
                            ));
                            ?>
                        </div>
                    </div>
                    <div class="hr-line-dashed"></div>
                    <?php
                endif;
                ?>
                <div class="form-group">
                    <div class="col-sm-4 col-sm-offset-2">
                        <a href="<?php echo Router::url(array('action' => 'index', '?' => array('object_type_code' => $this->request->query('object_type_code')))) ?>"
                           class="btn btn-white"><i class="fa fa-ban"></i> <span><?php echo __('cancel_btn') ?></span>
                        </a>
                        <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i>
                            <span><?php echo __('save_btn') ?></span></button>
                    </div>
                </div>
                <?php
                echo $this->Form->end();
                ?>
            </div>
        </div>
    </div>
</div>