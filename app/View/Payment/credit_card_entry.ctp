<?php echo $this->Html->css('payments.min'); ?>
<?= $this->App->viewStartComment()?>
<section class="panel enter-cc-info">
        <h3><?= __('Enter Payment Information')?></h3>
        <form class="form-horizontal">
            <div class="form-group">
                <label for="cardholder-name">Name</label>
                <input name="cardholder-name" class="form-control" />
            </div>
            <div class="form-group">
                <label>Card</label>
                <div id="card-element" class="form-control cc-field"></div>
            </div>
            <div class="payment-info-group">
                <strong><?= __('Price per user'); ?>:&nbsp;</strong><span class="cc-info-value">$19.99</span><br />
                <strong><?= __('Number of users'); ?>:&nbsp;</strong><span class="cc-info-value">100</span><br />
                <strong><?= __('Sub Total'); ?>:&nbsp;</strong><span class="cc-info-value">$1999.00</span><br />
                <strong><?= __('Tax'); ?>:&nbsp;</strong><span class="cc-info-value">$159.92</span><br />
                <hr>
                <strong><?= __('Total'); ?>:&nbsp;</strong><span class="cc-info-value">$2158.92</span>
            </div>
            <div class="panel-footer setting_pannel-footer">
                <a class="btn btn-link design-cancel bd-radius_4px" href="/Payment/">
                    <?= __("Cancel") ?>
                </a>
                <input type="submit" class="btn btn-primary" value="Submit" />
                <div class="outcome">
                    <div class="error" role="alert"></div>
                    <div class="success">
                    Success! Your Stripe token is <span class="token"></span>
                    </div>
                </div>
            </div>
        </form>
</section>
<script src="https://js.stripe.com/v3/"></script>