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
<section class="panel payment has-subnav">
    <?= $this->element('Payment/method_select') ?>
    <div class="panel-container">
        <h3 class=""><?= __("Credit Card")?></h3>

        <p><strong><?= __('Name on Card');?></strong></p>
        <p><?= $nameOnCard ?></p>
        <br />
        <p><strong><?= __('Card Number');?></strong></p>
        <p><?= $brand ?> **** **** **** <?= $lastDigits; ?></p>
        <br />
        <p><strong><?= __('Expiration date');?></strong></p>
        <p class="<?= $isExpired ? 'mod-alert' : '' ?>"><?= $expMonthName ?>, <?= $expYear ?></p>

        <?php if ($isExpired) : ?>
            <div class="has-error">
                <small class="help-block"><?=__("Card is expired. Please update your card's information.")?></small>
            </div>
        <?php endif; ?>
    </div>
    <footer class="panel-footer setting_pannel-footer">
        <a href="/payments/update_cc_info" class="btn btn-primary pull-right"><?=__('Update Card');?></a>
    </footer>
</section>
<?= $this->App->ViewEndComment() ?>

