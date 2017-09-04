<?= $this->App->viewStartComment() ?>
<section class="panel payment-history has-subnav">
    <?= $this->element('Payment/method_select') ?>
    <div class="panel-container">
        <h3><?= __('Payment History') ?></h3>
        <aside class="payment-history-key visible-xxs">
            <span class="fa fa-calendar"></span> = Monthly &nbsp; <span class="fa fa-user"></span> = New Member
        </aside>
        <table class="payment-history-table">
            <thead class="payment-history-table-head">
                <tr>
                    <td><?= __('ID');?></td>
                    <td><?= __('Date');?></td>
                    <td><?= __('Type');?></td>
                    <td><?= __('Amount');?></td>
                    <td>&nbsp;</td>
                </tr>
            </thead>
            <tbody class="payment-history-table-body">
            <?php foreach ($histories as $v):?>
                <tr>
                    <td><?= $v['id'] ?></td>
                    <td><?= $this->TimeEx->formatYearDayI18n($v['charge_datetime']) ?></td>
                    <td class="history-entry-type">
                        <span class="visible-xxs <?= $v['charge_type'] == Goalous\Model\Enum\ChargeHistory\ChargeType::MONTHLY_FEE ? "fa fa-calendar" : "fa fa-user"?>"></span>
                        <span class="hidden-xxs"><?= $v['charge_type'] == Goalous\Model\Enum\ChargeHistory\ChargeType::MONTHLY_FEE ? "Monthly" : "Added Member"?></span>
                    </td>
                    <?php $resultIconClass = $v['result_type'] == Goalous\Model\Enum\ChargeHistory\ResultType::SUCCESS ? "fa fa-check payment-success" : "fa fa-close payment-failed"?>
                    <td><span class="<?=$resultIconClass?>"></span><?= $v['total']?></td>
                    <td class="history-entry-download">
                        <?= $this->Html->link("<span class='fa fa-download'></span>",
                            ['controller' => 'payments', 'action' => 'receipt', $v['id'] . '.pdf'],
                            ['class' => 'btn payment-history-view-receipt-btn', 'div' => false])
                        ?>
                    </td>
                </tr>
            <?php endforeach;?>
            </tbody>
        </table>
    </div>
</section>
<?= $this->App->ViewEndComment() ?>
