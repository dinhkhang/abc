<?php
$report_controller = array(
    'Winner',
);
$intersect = array_intersect($report_controller, $allow_controllers);
?>
<?php if (!empty($intersect)): ?>
    <?php
    $active_clss = "";
    if (in_array($active_controller, $report_controller)) {

        $active_clss = 'active';
    }
    ?>
    <li class="<?php echo $active_clss ?>">
        <?php
        if (in_array('Winner/index', $permissions)):
            ?>
            <a href="<?php echo Router::url(array('controller' => 'Winner', 'action' => 'index')) ?>">
                <i class="fa fa-won"></i> <span class="nav-label"><?php echo __('win_nav_title') ?> </span><span class="fa arrow"></span>
            </a>
            <?php
        endif;
        ?>
        <ul class="nav nav-second-level">
            <?php
            if (in_array('Winner/index', $permissions)):
                ?>
                <li>
                    <a href="<?php echo Router::url(array('controller' => 'Winner', 'action' => 'index')) ?>">
                        <?php echo __('win_title') ?>
                    </a>
                </li>
                <li>
                    <a href="<?php echo Router::url(array('controller' => 'Winner', 'action' => 'chooseDaily')) ?>">
                        <?php echo __('poi_dai_title') ?>
                    </a>
                </li>
                <li>
                    <a href="<?php echo Router::url(array('controller' => 'Winner', 'action' => 'chooseWeekly')) ?>">
                        <?php echo __('poi_week_title') ?>
                    </a>
                </li>
                <li>
                    <a href="<?php echo Router::url(array('controller' => 'Winner', 'action' => 'chooseMonthly')) ?>">
                        <?php echo __('poi_month_title') ?>
                    </a>
                </li>
                <?php
            endif;
            ?>
        </ul>
    </li>
<?php endif; ?>