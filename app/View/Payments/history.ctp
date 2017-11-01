<?= $this->App->viewStartComment() ?>
<section class="panel payment has-subnav">
    <?= $this->element('Payment/method_select') ?>
    <div class="panel-container">
        <h3><?= __('Payment History') ?></h3>
        <aside class="visible-xxs">
            <span class="fa fa-calendar"></span> = <?= __('Monthly'); ?> &nbsp; <span class="fa fa-user"></span>
            = <?= __('New Member'); ?>
        </aside>
        <table class="payment-table">
            <thead>
            <tr>
                <td><?= __('ID'); ?></td>
                <td><?= __('Date'); ?></td>
                <td><?= __('Type'); ?></td>
                <td><?= __('Amount'); ?></td>
                <td>&nbsp;</td>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($histories as $v): ?>
                <tr>
                    <td><?= h($v['id']) ?></td>
                    <td><?= $this->TimeEx->formatYearDayI18n($v['charge_datetime']) ?></td>
                    <td class="history-entry-type">
                        <span
                            class="visible-xxs <?= $v['charge_type'] == Goalous\Model\Enum\ChargeHistory\ChargeType::MONTHLY_FEE ? "fa fa-calendar" : "fa fa-user" ?>"></span>
                        <span
                            class="hidden-xxs"><?= $v['charge_type'] == Goalous\Model\Enum\ChargeHistory\ChargeType::MONTHLY_FEE ? __("Monthly") : __("Added Member") ?></span>
                    </td>
                    <?php $resultIconClass = $v['result_type'] == Goalous\Model\Enum\ChargeHistory\ResultType::SUCCESS ? "fa fa-check success" : "fa fa-close error" ?>
                    <td><span class="<?= $resultIconClass ?>"></span><?= h($v['total']) ?></td>
                    <td class="history-entry-download">
                        <?= $this->Html->link("",
                            ['controller' => 'payments', 'action' => 'receipt', $v['id'] . '.pdf'],
                            ['class' => 'fa fa-eye', 'div' => false, 'target' => '_blank'])
                        ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
<?= $this->App->ViewEndComment() ?>
