<?= $this->App->viewStartComment() ?>
<section class="panel service-disabled">
    <div class="panel-container">
        <span class="fa fa-lock service-disabled-headline"></span>
        <h3 class="payment-alert-text"><?= __("Your team no longer has access to Goalous.") ?></h3>
        <?php if ($isTeamAdmin): ?>
            <h3 class="payment-alert-text"><?= __('If you want to resume normal usage, please subscribe to our payment plan.') ?></h3>
            <a href="/payments" class="btn btn-primary service-subscribe"><?= __('Subscribe'); ?></a>
        <?php else: ?>
            <h3 class="payment-alert-text"><?= __('If you want to resume normal usage, please contact to your team administrators.') ?></h3>
        <?php endif; ?>
    </div>
</section>
<?= $this->App->ViewEndComment() ?>
