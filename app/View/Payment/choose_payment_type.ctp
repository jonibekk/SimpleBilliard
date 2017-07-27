<?php echo $this->Html->css('payments.min'); ?>
<section class="panel choose-payment">
    <div class="panel-container">
        <h3><?= __('Select Country Location')?></h3>
        <?php
        $this->Form->create('Countries', [
            'url'           => ['controller' => 'countries'],
            'inputDefaults' => [
                'div'       => 'form-group',
                'wrapInput' => false,
                'class'     => 'form-control',
            ],
            'class'         => 'form-horizontal',
            'name'          => 'companyLocation'
        ]); 
        $this->Form->input('country', [
            'label'     => __("Company Location"),
            'type'      => 'select',
            'options'   => $countryList,
            'wrapInput' => 'user-setting-lang-select-wrap col col-sm-6'
        ]) ?>
        ?>
        <div class="payment-options">
            <h3><?= __('Select Payment Method')?></h3>
            <div class="payment-option" onClick="window.location='#'">
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
    </div>
</section>