<?= $this->App->viewStartComment() ?>
<section class="panel payment has-subnav">
    <?= $this->element('Payment/method_select') ?>
    <div class="panel-container">
        <h3><?= __('Payment History') ?></h3>
        <aside class="visible-xxs">
            <span class="fa fa-calendar"></span> = <?= __('Monthly'); ?> &nbsp;
            <span class="fa fa-user"></span> = <?= __('New Member'); ?> &nbsp;
            <span class="fa fa-arrow-up"></span> = <?= __('Upgrade'); ?>
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
                    <?php
                        $icon = '';
                        $text = '';
                        switch ($v['charge_type']) {
                            case Goalous\Model\Enum\ChargeHistory\ChargeType::MONTHLY_FEE:
                                $icon = 'fa fa-calendar';
                                $text = __("Monthly");
                                break;
                            case  Goalous\Model\Enum\ChargeHistory\ChargeType::USER_INCREMENT_FEE:
                            case Goalous\Model\Enum\ChargeHistory\ChargeType::USER_ACTIVATION_FEE:
                                $icon = 'fa fa-user';
                                $text = __("Added Member");
                                break;
                            case Goalous\Model\Enum\ChargeHistory\ChargeType::UPGRADE_PLAN_DIFF:
                                $icon = 'fa fa-arrow-up';
                                $text = __('Upgrade');
                                break;
                        }
                    ?>
                    <td class="history-entry-type">
                        <span
                            class="visible-xxs <?= $icon ?>"></span>
                        <span
                            class="hidden-xxs"><?= $text ?></span>
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
