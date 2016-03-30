<?php
$level1_header = $header;
$level1_items = $items;
$level1_allow_permissions = $allow_permissions;

if (empty($level1_header['permission_code'])) {

    $level1_header['permission_code'] = $level1_header['path'];
}
if (
        !empty(array_intersect($permissions, $level1_allow_permissions))
):
    ?>
    <?php
    $active_level1_clss = "";
    if (in_array($active_permission_code, $level1_allow_permissions)) {

        $active_level1_clss = 'active';
    }

    $level1_icon = '';
    if (!empty($level1_header['icon'])) {

        $level1_icon = '<i class="' . $level1_header['icon'] . '"></i>';
    }
    ?>
    <li class="<?php echo $active_level1_clss ?>">
        <?php
        if (in_array($level1_header['permission_code'], $permissions)):
            ?>
            <a href="<?php echo Router::url('/' . $level1_header['path'], true) ?>">
                <?php echo $level1_icon ?> <span class="nav-label"><?php echo $level1_header['title'] ?> </span><span class="fa arrow"></span>
            </a>
            <?php
        endif;
        ?>
        <ul class="nav nav-second-level">
            <?php
            foreach ($level1_items as $level1_item):
                ?>
                <?php if (empty($level1_item['items'])): ?>
                    <?php
                    $level2_title = $level1_item['title'];
                    $level2_path = $level1_item['path'];
                    $level2_permission_code = !empty($level1_item['permission_code']) ?
                            $level1_item['permission_code'] : $level1_item['path'];
                    ?>
                    <?php
                    $active_level2_clss = "";
                    if ($level2_permission_code == $active_permission_code) {

                        $active_level2_clss = 'active';
                    }
                    ?>
                    <?php if (in_array($level2_permission_code, $permissions)): ?>
                        <li class="<?php echo $active_level2_clss ?>">
                            <a href="<?php echo Router::url('/' . $level2_path, true) ?>">
                                <?php echo $level2_title ?>
                            </a>
                        </li>
                    <?php endif; ?>
                <?php else: ?>
                    <?php
                    $level2_header = $level1_item['header'];
                    $level2_items = $level1_item['items'];
                    $level2_allow_permissions = $level1_item['allow_permissions'];
                    if (empty($level2_header['permission_code'])) {

                        $level2_header['permission_code'] = $level2_header['path'];
                    }
                    if (
                            !empty(array_intersect($permissions, $level2_allow_permissions))
                    ):
                        ?>
                        <?php
                        $active_level2_clss = "";
                        if (in_array($active_permission_code, $level2_allow_permissions)) {

                            $active_level2_clss = 'active';
                        }
                        ?>
                        <li class="<?php echo $active_level2_clss ?>">
                            <?php
                            if (in_array($level2_header['permission_code'], $permissions)):
                                ?>
                                <a href="<?php echo Router::url('/' . $level2_header['path'], true) ?>">
                                    <span class="nav-label"><?php echo $level2_header['title'] ?> </span><span class="fa arrow"></span>
                                </a>
                                <?php
                            endif;
                            ?>
                            <ul class="nav nav-third-level">
                                <?php
                                foreach ($level2_items as $level2_item):
                                    ?>
                                    <?php
                                    $level3_title = $level2_item['title'];
                                    $level3_path = $level2_item['path'];
                                    $level3_permission_code = !empty($level2_item['permission_code']) ?
                                            $level2_item['permission_code'] : $level2_item['path'];
                                    ?>
                                    <?php
                                    $active_level3_clss = "";
                                    if ($level3_permission_code == $active_permission_code) {

                                        $active_level3_clss = 'active';
                                    }
                                    ?>
                                    <?php if (in_array($level3_permission_code, $permissions)): ?>
                                        <li class="<?php echo $active_level3_clss ?>">
                                            <a href="<?php echo Router::url('/' . $level3_path, true) ?>">
                                                <?php echo $level3_title ?>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </ul>
                        </li>
                    <?php endif; ?>
                <?php endif; ?>
            <?php endforeach; ?>
        </ul>
    </li>
<?php endif; ?>
