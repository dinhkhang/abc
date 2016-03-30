<?php
$report_controller = array(
    'ReportDailyAccessLogins',
    'DailyReports'
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
        if (in_array('ReportDailyAccessLogins/index', $permissions)):
            ?>
            <a href="<?php echo Router::url(array('controller' => 'ReportDailyAccessLogins', 'action' => 'index')) ?>">
                <i class="fa fa-bar-chart"></i> <span class="nav-label"><?php echo __('report_nav_title') ?> </span><span class="fa arrow"></span>
            </a>
            <?php
        endif;
        ?>
        <ul class="nav nav-second-level">
            <?php
            if (in_array('ReportDailyAccessLogins/index', $permissions)):
                ?>
                <li>
                    <a href="<?php echo Router::url(array('controller' => 'ReportDailyAccessLogins', 'action' => 'index')) ?>">
                        <?php echo __('report_daily_access_login_title') ?>
                    </a>
                </li>
                <li>
                    <a href="<?php echo Router::url(array('controller' => 'DailyReports', 'action' => 'index')) ?>">
                        <?php echo __('daily_report_title') ?>
                    </a>
                </li>
                <li>
                    <a href="<?php echo Router::url(array('controller' => 'DailyReports', 'action' => 'general')) ?>">
                        <?php echo __('daily_general_report_title') ?>
                    </a>
                </li>
                <?php
            endif;
            ?>
        </ul>
    </li>
<?php endif; ?>