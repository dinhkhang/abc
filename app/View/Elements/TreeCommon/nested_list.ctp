<ol class="<?php echo $ol_clss ?>">
    <?php foreach ($list_data as $v): ?>
        <li class="<?php echo $li_clss ?>" data-id="<?php echo $v[$model_name]['id'] ?>">
            <div class="<?php echo $div_clss ?> row">
                <span class="pull-right">
                    <?php if (in_array('Categories_' . $object_type_code . '_cloneRecord', $permissions)): ?>
                        <a href="<?php echo Router::url(array('action' => 'cloneRecord', $v[$model_name]['id'], '?' => $this->request->query)) ?>" class="btn btn-info dd-nodrag" title="<?= __('cloneRecord') ?>">
                            <i class="fa fa-files-o"></i>
                        </a>
                    <?php endif; ?>
                    <?php if (in_array('Categories_' . $object_type_code . '_edit', $permissions)): ?>
                        <a href="<?php echo Router::url(array('action' => 'edit', $v[$model_name]['id'], '?' => $this->request->query)) ?>" class="btn btn-primary dd-nodrag" title="<?php echo __('edit_btn') ?>">
                            <i class="fa fa-edit"></i>
                        </a>
                    <?php endif; ?>
                    <?php if (in_array('Categories_' . $object_type_code . '_reqDelete', $permissions)): ?>
                        <a href="<?php echo Router::url(array('action' => 'reqDelete', $v[$model_name]['id'], '?' => $this->request->query)) ?>" class="btn btn-danger remove dd-nodrag" title="<?php echo __('delete_btn') ?>">
                            <i class="fa fa-trash"></i>
                        </a>
                    <?php endif; ?>
                </span>
                <?php echo $v[$model_name][$key] ?> (<?php echo $status[$v[$model_name]['status']] ?>)
                <br/>
                <small><?php echo __('lang_code') ?>: <?= Configure::read('S.Lang')[$v[$model_name]['lang_code']] ?></small>
                <br>
                <small><?php echo __('category_modified') ?>: <?php echo $this->Common->parseDateTime($v[$model_name]['modified']) ?></small>
                <br/>
                <small><?php echo __('category_user') ?>: <?php echo!empty($v['User']['username']) ? $v['User']['username'] : __('unknown') ?></small>
            </div>
            <?php
            $children = $v['children'];
            if (!empty($children)) {

                echo $this->element('TreeCommon/nested_list', array(
                    'list_data' => $children,
                    'ol_clss' => $ol_clss,
                    'li_clss' => $li_clss,
                    'div_clss' => $div_clss,
                    'key' => $key,
                ));
            }
            ?>
        </li>
    <?php endforeach; ?>
</ol>
