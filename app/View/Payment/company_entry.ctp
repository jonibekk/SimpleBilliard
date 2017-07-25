<?php echo $this->Html->css('payments.min'); ?>
<?= $this->App->viewStartComment()?>
<section class="panel choose-payment">
    <div class="panel-container">
        <h3><?= __('Select Payment Method')?></h3>
        <?php $this->Form->create('Payment', [
            'url'           => ['controller' => 'payment', 'action' => 'companyEntry'],
            'inputDefaults' => [
                'wrapInput' => false,
                'class'     => 'form-control',
            ],
            'class'         => 'form-horizontal',
            'name'            => 'companyEntry',
        ]); ?>
    </div>
</section>