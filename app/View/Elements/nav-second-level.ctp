<?php if (in_array($controller, $allow_controllers)): ?>
        <?php
        $active_clss = "";
        if ($active_controller == $controller) {

                $active_clss = 'active';
        }

        $level1_icon = '';
        if (!empty($level1['icon'])) {

                $level1_icon = '<i class="' . $level1['icon'] . '"></i>';
        }
        $level1['?'] = !empty($level1['?']) ? $level1['?'] : array();
        ?>
        <li class="<?php echo $active_clss ?>">
            <?php
            if (in_array($controller . '/' . $level1['action'], $permissions)):
                    ?>
                    <a href="<?php echo Router::url(array('controller' => $controller, $level1['action'], '?' => $level1['?'])) ?>">
                        <?php echo $level1_icon ?> <span class="nav-label"><?php echo $level1['title'] ?> </span><span class="fa arrow"></span>
                    </a>
                    <?php
            endif;
            ?>
            <ul class="nav nav-second-level">
                <?php foreach ($level2 as $lvl2): ?>
                        <?php
                        $lvl2['?'] = !empty($lvl2['?']) ? $lvl2['?'] : array();
                        if (in_array($controller . '/' . $lvl2['action'], $permissions)):
                                ?>
                                <li>
                                    <a href="<?php echo Router::url(array('controller' => $controller, 'action' => $lvl2['action'], '?' => $lvl2['?'])) ?>">
                                        <?php echo $lvl2['title'] ?>
                                    </a>
                                </li>
                                <?php
                        endif;
                        ?>
                <?php endforeach; ?>
            </ul>
        </li>
<?php endif; ?>