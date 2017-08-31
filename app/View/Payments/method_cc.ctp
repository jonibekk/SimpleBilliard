<?php
/**
 * @var string  $brand
 * @var string  $lastDigits
 * @var string  $expMonthName
 * @var int     $expYear
 * @var bool    $isExpired
 */
?>
<?= $this->App->viewStartComment() ?>
<section class="panel paymentMethod">
    <div class="paymentMethod-inner">
        <?= $this->element('Payment/method_select') ?>
        <h3 class=""><?= __("Credit Card")?></h3>
        <!-- canormal start-->
        <table class="paymentMethod-cc-table">
            <thead class="paymentMethod-cc-table-head">
            <tr>
                <td><?= __('Card');?></td>
                <td><?= __('Expire');?></td>
            </tr>
            </thead>
            <tbody class="paymentMethod-cc-table-body">
            <tr>
                <td><?= $brand ?> *******<?= $lastDigits; ?></td>
                <td>
                    <span class="paymentMethod-cc-table-expireDate mr_8px <?= $isExpired ? 'mod-alert' : '' ?>"><?= $expMonthName ?>, <?= $expYear ?></span>
                    <a href="/payments/update_cc_info" class="btn btn-primary pull-right"><?=__('Update Card');?></a>
                </td>
            </tr>
            </tbody>
        </table>
        <!-- normal end-->
        <!-- card expire start-->
        <?php if ($isExpired) : ?>
        <div class="has-error">
            <small class="help-block"><?=__("Card is expired. Please update your card's information.")?></small>
        </div>
        <?php endif; ?>
        <!-- card expire end-->
    </div>
</section>
<?= $this->App->ViewEndComment() ?>

