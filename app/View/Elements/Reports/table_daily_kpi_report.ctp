<div class="ibox-content">
    <div class="table-responsive">
        <table class="table table-hover table-bordered">
            <thead>
            <tr class="bg-info">
                <th>Ngày</th>
                <?php if ((isset($general) && $general) || (isset($packageDay) && $packageDay)): ?>
                <th>Doanh thu gói ngày</th>
                <?php endif; ?>
                <?php if ((isset($general) && $general) || (isset($packageWeek) && $packageWeek)): ?>
                    <th>Doanh thu gói tuần</th>
                <?php endif; ?>
                <?php if (isset($general) && $general): ?>
                    <th>Doanh thu tra cứu lẻ</th>
                <?php endif; ?>
                <th>Doanh thu truy thu cước (retry)</th>
                <th>Tổng doanh thu</th>
                <th>Doanh thu lũy kế THÁNG</th>
                <th>So với cùng kỳ THÁNG trước</th>
                <th>Thuê bao ĐK mới</th>
                <th>Hủy dịch vụ</th>
                <th>Tổng TB(các gói TB)</th>
                <?php if (isset($general) && $general): ?>
                    <th>Thuê bao gói mặc định</th>
                <?php endif; ?>
                <th>Thuê bao PSC</th>
                <th>Tỷ lệ trừ cước thành công</th>
            </tr>
            </thead>
            <tbody>
            <?php if (!empty($kpi)): ?>
                <?php foreach ($kpi as $date => $item): ?>
                    <tr>
                        <td width="10%"><?php echo $this->Common->parseDate($date); ?></td>
                        <?php if ((isset($general) && $general) || (isset($packageDay) && $packageDay)): ?>
                            <td><?php echo number_format($item['revenue_day'] - $item['retry_revenue_day']); ?></td>
                        <?php endif; ?>
                        <?php if ((isset($general) && $general) || (isset($packageWeek) && $packageWeek)): ?>
                            <td><?php echo number_format($item['revenue_week'] - $item['retry_revenue_week']); ?></td>
                        <?php endif; ?>
                        <?php if (isset($general) && $general): ?>
                            <td><?php echo number_format($item['revenue_mua']); ?></td>
                        <?php endif; ?>
                        <td><?php echo number_format($item['retry_renew_revenue']); ?></td>
                        <td><?php echo number_format($item['revenue']); ?></td>
                        <td><?php echo number_format($item['accumulated_month_revenue']); ?></td>
                        <td>
                            <?php
                            if ($item['last_accumulated_month_revenue'] > 0) {
                                $percent = ($item['accumulated_month_revenue'] - $item['last_accumulated_month_revenue']) * 100 / $item['last_accumulated_month_revenue'];
                                echo number_format($percent, 2).'%';
                            } else {
                                echo '--';
                            }
                            ?>
                        </td>
                        <td><?php echo number_format($item['register_success']); ?></td>
                        <td><?php echo number_format($item['deactive']); ?></td>
                        <td><?php echo number_format($item['subscriber']); ?></td>
                        <?php if ((isset($general) && $general)): ?>
                            <td><?php echo number_format($item['subscriber_default']); ?></td>
                        <?php endif; ?>
                        <td><?php echo number_format($item['psc_subscriber']); ?></td>
                        <td>
                            <?php
                            $percent = 0;
                            if ($item['charge_subscriber'] > 0) {
                                $percent = $item['psc_subscriber']*100/$item['charge_subscriber'];
                            }
                            echo number_format($percent, 2) . '%';
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="14" style="text-align: center"><?php echo __('no_result') ?></td>
                </tr>
            <?php endif; ?>
            </tbody>
            <tfoot>
            <?php if (!empty($kpi)): ?>
                <tr class="daily bg-info">
                    <td><?php echo __('Total'); ?></td>
                    <?php if ((isset($general) && $general) || (isset($packageDay) && $packageDay)): ?>
                        <td><?php echo number_format(array_sum(array_column($kpi, 'revenue_day')) - array_sum(array_column($kpi, 'retry_revenue_day'))); ?></td>
                    <?php endif; ?>
                    <?php if ((isset($general) && $general) || (isset($packageWeek) && $packageWeek)): ?>
                        <td><?php echo number_format(array_sum(array_column($kpi, 'revenue_week')) - array_sum(array_column($kpi, 'retry_revenue_week'))); ?></td>
                    <?php endif; ?>
                    <?php if (isset($general) && $general): ?>
                        <td><?php echo number_format(array_sum(array_column($kpi, 'revenue_mua'))); ?></td>
                    <?php endif; ?>
                    <td><?php echo number_format(array_sum(array_column($kpi, 'retry_renew_revenue'))); ?></td>
                    <td><?php echo number_format(array_sum(array_column($kpi, 'revenue'))); ?></td>
                    <td><?php echo '--'; ?></td>
                    <td><?php echo '--'; ?></td>
                    <td><?php echo number_format(array_sum(array_column($kpi, 'register_success'))); ?></td>
                    <td><?php echo number_format(array_sum(array_column($kpi, 'deactive'))); ?></td>
                    <td><?php echo number_format(array_column($kpi, 'subscriber')[0]); ?></td>
                    <?php if ((isset($general) && $general)): ?>
                        <td><?php echo number_format(array_column($kpi, 'subscriber_default')[0]); ?></td>
                    <?php endif; ?>
                    <td><?php echo number_format(array_sum(array_column($kpi, 'psc_subscriber'))); ?></td>
                    <td>
                        <?php
                        $percent = 0;
                        if (array_sum(array_column($kpi, 'charge_subscriber')) > 0) {
                            $percent = array_sum(array_column($kpi, 'psc_subscriber'))*100 / array_sum(array_column($kpi, 'charge_subscriber'));
                        }
                        echo number_format($percent, 2) . '%';
                        ?>
                    </td>
                </tr>
            <?php endif; ?>
            </tfoot>
        </table>
    </div>
</div>