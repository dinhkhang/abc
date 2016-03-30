<?php if (in_array($controller, $allow_controllers)): ?>
        <?php
        $active_clss = "";
        if ($active_controller == $controller) {

                $active_clss = 'active';
        }
        $level_icon = '';
        if (!empty($level['icon'])) {

                $level_icon = '<i class="' . $level['icon'] . '"></i>';
        }
        ?>
        <?php
        if (in_array($controller . '/' . $level['action'], $permissions)):
                ?>
                <li class="<?php echo $active_clss ?>">
                    <a href="<?php echo Router::url(array('controller' => $controller, 'action' => $level['action'])) ?>">
                        <?php echo $level_icon ?> <span class="nav-label"><?php echo $level['title'] ?></span> 
                    </a>
                </li>
        <?php endif; ?>
<?php endif; ?>