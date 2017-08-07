<?= $this->App->viewStartComment() ?>
<section class="panel subscription-status">
    <div class="panel-container">
        <h3 class="subscription-status-headline"><?= __("Subscription Status") ?></h3>
        <h4 class="status-text">
            <i class="fa fa-unlock-alt free-trial"></i>
            <?= __('Free Trial') ?>
        </h4>
        <p class="subscription-status-detail">Your team is currently using Goalous as a free trial. Your free trial will end on September 20, 2017</p>
        <a href="#" class="subscribe-btn btn btn-primary"><?= __('Subscribe') ?></a>
        <div class="team-price-info">
            <h5 class="team-price-info-headline"><?= __('Number of Members')?></h5>
            <i class="team-price-info-icon fa fa-user"></i>
            <span class="team-price-info-number">100</span>
        </div>
        <div class="team-price-info">
            <h5 class="team-price-info-headline"><?= __('Total Price')?></h5>
            <i class="team-price-info-icon fa fa-credit-card"></i>
            <span class="team-price-info-number">$19,990.00</span>
        </div>
    </div>
</section>
<?= $this->App->ViewEndComment() ?>