<?= $this->App->viewStartComment() ?>
    <select class="form-control payment-nav-select" id="paymentMethodSelect" name="paymentMethodSelect"
            onchange="paymentMenuChanged(this)">
        <option <?= $this->params['action'] == 'index' ? 'selected="selected"' : '' ?> value="index"><?= __("Subscription") ?></option>
        <option <?= $this->params['action'] == 'history' ? 'selected="selected"' : '' ?> value="history"><?= __("Invoice history") ?></option>
        <option <?= $this->params['action'] == 'method' ? 'selected="selected"' : '' ?> value="method"><?= __("Payment method") ?></option>
        <option <?= $this->params['action'] == 'contact_settings' ? 'selected="selected"' : '' ?> value="contact_settings"><?= __("Contact Settings") ?></option>
    </select>
<?= $this->App->viewEndComment() ?>