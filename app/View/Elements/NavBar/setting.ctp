<?php
$settings_controller = array(
);
?>
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
        ?>
        <li class="<?php echo $active_clss ?>">
            <?php
            if (in_array($controller . '/' . $level1['action'], $permissions)):
                    ?>
                    <a href="<?php echo Router::url(array('controller' => $controller, $level1['action'])) ?>">
                        <?php echo $level1_icon ?> <span class="nav-label"><?php echo $level1['title'] ?> </span><span class="fa arrow"></span>
                    </a>
                    <?php
            endif;
            ?>
            <ul class="nav nav-second-level">
                <?php foreach ($level2 as $lvl2): ?>
                        <?php
                        if (in_array($controller . '/' . $lvl2['action'], $permissions)):
                                ?>
                                <li>
                                    <a href="<?php echo Router::url(array('controller' => $controller, 'action' => $lvl2['action'])) ?>">
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