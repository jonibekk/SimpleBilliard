<?= $this->App->viewStartComment() ?>
<section class="panel subscription-status">
    <div class="panel-container">
        <h3><?= __("Subscription Status") ?></h3>
        <h4 class="status-text"><i class="fa fa-unlock-alt"></i><?= __('Free Trial') ?></h4>
        <p>Your team is currently using Goalous as a free trial. Your free trial will end on September 20, 2017</p>
        <a href="#" class="btn primary-btn"><?= __('Subscribe') ?></a>
        <div class="team-price-info">
            <h5><?= __('Number of Members')?></h5>
            <i class="fa fa-user"></i>
            <span>100</span>
        </div>
        <div class="team-price-info">
            <h5><?= __('Total Price')?></h5>
            <i class="fa fa-credit-card"></i>
            <span>$19,990.00</span>
        </div>
    </div>
</section>
<?= $this->App->ViewEndComment() ?>