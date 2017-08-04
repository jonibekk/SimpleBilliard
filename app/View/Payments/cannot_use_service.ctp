<?= $this->App->viewStartComment() ?>
<section class="panel service-disabled">
    <div class="panel-container">
        <h1 class="service-disabled-headline"><span class="fa fa-lock"></span></h1>
        <h3><?= __("Your team no longer has access to Goalous.") ?></h3>
    <?php
        if ($isTeamAdmin) {
    ?>
        <h3><?= __('If you want to resume normal usage, please subscribe to our payment plan.') ?></h3>
    <?php } ?>
    <a href="/payments/apply" class="btn btn-primary service-disabled-subscribe"><?= __('Subscribe'); ?></a>
    </div>
</section>
<?= $this->App->ViewEndComment() ?>
