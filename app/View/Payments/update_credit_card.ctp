<?= $this->App->viewStartComment()?>
<section class="panel enter-cc-info">
    <h3><?= __('Enter your card information')?></h3>
    <?=
    $this->Form->create('Payments', [
        'default'         => false,
        'url'             => '',
        'class'           => 'form-horizontal',
        'type'            => 'file',
        'novalidate'      => true,
        'name'            => 'enterCCInfo',
        'id'              => 'enterCCInfo'
    ]);
    ?>
        <div class="form-group">
            <label for="cardholder-name"><?= __('Name on Card');?></label>
            <input name="cardholder-name" class="form-control" placeholder="><?= __('Jane Doe');?>" required />
        </div>
        <div class="form-group">
            <label><?= __('Card Number');?></label>
            <div id="card-element" class="form-control cc-field"></div>
        </div>
        <div class="outcome">
            <div class="error" role="alert"></div>
        </div>
        <div class="panel-footer setting_pannel-footer">
            <a class="btn btn-link design-cancel bd-radius_4px" href="/payments">
                <?= __("Cancel") ?>
            </a>
            <input type="submit" class="btn btn-primary" value="<?= __("Update") ?>"  />
        </div>
    <?= $this->Form->end() ?>
</section>
<?= $this->App->viewEndComment()?>

