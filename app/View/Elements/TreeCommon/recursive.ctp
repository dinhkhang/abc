<ol class="<?php echo $ol_clss ?>">
    <?php foreach ($list_data as $v): ?>
            <li class="<?php echo $li_clss ?>" data-id="<?php echo $v[$model_name]['id'] ?>">
                <div class="<?php echo $div_clss ?> row">
                    <span class="pull-right">
                        <a href="<?php echo Router::url(array('action' => 'edit', $v[$model_name]['id'])) ?>" class="btn btn-primary dd-nodrag" title="<?php echo __('edit_btn') ?>">
                            <i class="fa fa-edit"></i>
                        </a>
                        <a href="<?php echo Router::url(array('action' => 'reqDelete', $v[$model_name]['id'])) ?>" class="btn btn-danger remove dd-nodrag" title="<?php echo __('delete_btn') ?>">
                            <i class="fa fa-trash"></i>
                        </a>
                    </span>
                    <?php echo $v[$model_name][$key] ?>
                </div>
                <?php
                $children = $v['children'];
                if (!empty($children)) {

                        echo $this->element('TreeCommon/recursive', array(
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