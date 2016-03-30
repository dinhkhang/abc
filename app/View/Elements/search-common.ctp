<?php
if (!(isset($hidden) && is_array($hidden) && in_array('user', $hidden))) :
        ?>
        <?php if (AuthComponent::user()['type'] != 'CONTENT_EDITOR'): ?>
                <div class="col-md-4">
                        <div class="form-group">
                                <?php
                                echo $this->Form->input('user', array(
                                    'div' => false,
                                    'class' => 'form-control',
                                    'label' => __('tour_user'),
                                    'options' => $users,
                                    'empty' => '-------',
                                    'default' => $this->request->query('user'),
                                ));
                                ?>
                        </div>
                </div>
        <?php endif; ?>
<?php endif; ?>
<?php
if (!(isset($hidden) && is_array($hidden) && in_array('country', $hidden))) :
        ?>
        <div class="col-md-4">
                <div class="form-group">
                        <?php
                        echo $this->Form->input('country', array(
                            'div' => false,
                            'class' => 'form-control select-ajax',
                            'label' => __('activity_country'),
                            'type' => 'select',
                            'id' => 'Country',
                            'data-ajax--url' => Router::url('/activities/search/Country'),
                            'default' => $this->request->query('country'),
                            'options' => isset($locationInfo, $locationInfo['country']) ? $locationInfo['country'] : [],
                        ));
                        ?>
                </div>
        </div>
<?php endif; ?>
<?php
if (!(isset($hidden) && is_array($hidden) && in_array('region', $hidden))) :
        ?>
        <div class="col-md-4">
                <div class="form-group">
                        <?php
                        echo $this->Form->input('region', array(
                            'div' => false,
                            'class' => 'form-control select-ajax',
                            'label' => __('activity_region'),
                            'type' => 'select',
                            'id' => 'Region',
                            'data-ajax--url' => Router::url('/activities/search/Region'),
                            'default' => $this->request->query('region'),
                            'options' => isset($locationInfo, $locationInfo['region']) ? $locationInfo['region'] : [],
                        ));
                        ?>
                </div>
        </div>
<?php endif; ?>
<!--<div class="col-md-4">
        <div class="form-group">
<?php
//                echo $this->Form->input('lang_code', array(
//                    'empty' => '---',
//                    'div' => false,
//                    'class' => 'form-control',
//                    'label' => __('activity_lang_code'),
//                    'type' => 'select',
//                    'default' => $this->request->query('lang_code'),
//                    'options' => Configure::read('sysconfig.App.languages'),
//                ));
?>
        </div>
</div>-->
