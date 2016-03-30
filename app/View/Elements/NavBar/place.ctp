<?php
$level1_controller = $level['controller'];
$level1_header = $level['header'];
$level1_items = $level['items'];
if (
        !empty(array_intersect($allow_controllers, $level1_controller))
):
        ?>
        <?php
        $active_level1_clss = "";
        if (in_array($active_controller, $level1_controller)) {

                $active_level1_clss = 'active';
        }

        $level1_icon = '';
        if (!empty($level1_header['icon'])) {

                $level1_icon = '<i class="' . $level1_header['icon'] . '"></i>';
        }
        $level1_header['?'] = !empty($level1_header['?']) ? $level1_header['?'] : array();
        ?>
        <li class="<?php echo $active_level1_clss ?>">
            <?php
            if (in_array($level1_header['controller'] . '/' . $level1_header['action'], $permissions)):
                    ?>
                    <a href="<?php echo Router::url(array('controller' => $level1_header['controller'], $level1_header['action'], '?' => $level1_header['?'])) ?>">
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
                                $level2_controller = $level1_item['controller'];
                                $level2_action = $level1_item['action'];
                                $level2_title = $level1_item['title'];
                                $level2_query = !empty($level1_item['?']) ? $level1_item['?'] : array();
                                if (in_array($level2_controller . '/' . $level2_action, $permissions)):
                                        ?>
                                        <?php
                                        $active_level2_clss = "";
                                        if ($level2_action == $active_action) {

                                                $active_level2_clss = 'active';
                                        }
                                        ?>
                                        <li class="<?php echo $active_level2_clss ?>">
                                            <a href="<?php echo Router::url(array('controller' => $level2_controller, 'action' => $level2_action, '?' => $level2_query)) ?>">
                                                <?php echo $level2_title ?>
                                            </a>
                                        </li>
                                        <?php
                                endif;
                                ?>
                        <?php else: ?>
                                <?php
                                $level2_controller = $level1_item['controller'];
                                $level2_header = $level1_item['header'];
                                $level2_header['?'] = !empty($level1_item['header']['?']) ? $level1_item['header']['?'] : array();
                                $level2_items = $level1_item['items'];
                                if (
                                        !empty(array_intersect($allow_controllers, $level2_controller))
                                ):
                                        ?>
                                        <?php
                                        $active_level2_clss = "";
                                        if (in_array($active_controller, $level2_controller)) {

                                                $active_level2_clss = 'active';
                                        }
                                        ?>
                                        <li class="<?php echo $active_level2_clss ?>">
                                            <?php
                                            if (in_array($level2_header['controller'] . '/' . $level2_header['action'], $permissions)):
                                                    ?>
                                                    <a href="<?php echo Router::url(array('controller' => $level2_header['controller'], $level2_header['action'], '?' => $level2_header['?'])) ?>">
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
                                                        $level3_controller = $level2_item['controller'];
                                                        $level3_action = $level2_item['action'];
                                                        $level3_title = $level2_item['title'];
                                                        $level3_query = !empty($level2_item['?']) ? $level2_item['?'] : array();
                                                        if (in_array($level3_controller . '/' . $level3_action, $permissions)):
                                                                ?>
                                                                <?php
                                                                $active_level3_clss = "";
                                                                if ($level3_action == $active_action) {

                                                                        $active_level3_clss = 'active';
                                                                }
                                                                ?>
                                                                <li class="<?php echo $active_level3_clss ?>">
                                                                    <a href="<?php echo Router::url(array('controller' => $level3_controller, 'action' => $level3_action, '?' => $level3_query)) ?>">
                                                                        <?php echo $level3_title ?>
                                                                    </a>
                                                                </li>
                                                                <?php
                                                        endif;
                                                        ?>
                                                <?php endforeach; ?>
                                            </ul>
                                        </li>
                                <?php endif; ?>
                        <?php endif; ?>
                <?php endforeach; ?>
            </ul>
        </li>
<?php endif; ?>
