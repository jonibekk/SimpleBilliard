<?php echo $this->Html->css('payments.min'); ?>
<section class="panel choose-payment">
    <div class="panel-container">
        <h3><?= __('Select Payment Method')?></h3>
        <div class="payment-option">
            <h4><?= __('Credit Card') ?></h4>
            <i class="fa fa-credit-card"></i>
            <p><?= __("Use a credit card to setup automatic, reoccuring payments for your Goalous team.") ?></p>
            <a href="#"><?= __('Setup') ?></a>
        </div>
        <div class="payment-option upcoming">
            <h4><?= __('Invoice') ?></h4>
            <i class="fa fa-leaf"></i>
            <p><?= __("Setup a monthly invoice with Goalous.") ?></p>
            <p class="coming-soon"><?= __('Coming Soon') ?></a>
        </div>
    </div>
</section>