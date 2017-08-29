<?= $this->App->viewStartComment() ?>
<select class="form-control" id="paymentMethodSelect" name="paymentMethodSelect"
        onchange="paymentMenuChanged(this)">
    <option <?= $this->params['action'] == 'subscription' ? 'selected="selected"' : '' ?> value="subscription"><?= __("Subscription") ?></option>
    <option <?= $this->params['action'] == 'history' ? 'selected="selected"' : '' ?> value="history"><?= __("Invoice history") ?></option>
    <option <?= $this->params['action'] == 'method' ? 'selected="selected"' : '' ?> value="method"><?= __("Payment method") ?></option>
    <option <?= $this->params['action'] == 'settings' ? 'selected="selected"' : '' ?> value="settings"><?= __("Settings") ?></option>
</select>
<?= $this->App->viewEndComment() ?>