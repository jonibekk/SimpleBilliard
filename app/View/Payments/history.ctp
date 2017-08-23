<?= $this->App->viewStartComment() ?>
<section class="panel payment-history">
    <div class="panel-container">
        <h3><?= __('Payment History') ?></h3>
        <table class="payment-history-table">
            <thead class="payment-history-table-head">
                <tr>
                    <td><?= __('ID');?></td>
                    <td><?= __('Date');?></td>
                    <td><?= __('Amount');?></td>
                    <td><?= __('Receipt');?></td>
                </tr>
            </thead>
            <tbody class="payment-history-table-body">
            <?php foreach ($histories as $v):?>
                <tr>
                    <td><?= $v['id'] ?></td>
                    <td><?= $this->TimeEx->formatYearDayI18n($v['charge_datetime']) ?></td>
                    <?php $resultIconClass = $v['result_type'] == ChargeHistory::TRANSACTION_RESULT_SUCCESS ? "fa fa-check payment-success" : "fa fa-close payment-failed"?>
                    <td><span class="<?=$resultIconClass?>"></span><?= $v['total']?></td>
                    <td><a href="#" class="btn payment-history-view-receipt-btn"><?=__('View');?></a></td>
                </tr>
            <?php endforeach;?>
            </tbody>
        </table>
    </div>
</section>
<?= $this->App->ViewEndComment() ?>
