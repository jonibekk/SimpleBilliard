<?= $this->App->viewStartComment() ?>
<section class="panel pricing-info">
    <div class="panel-container">
        <h3><?= __('Pricing Information') ?></h3>
        <div class="pricing-info-per-user">
            <h5 class="pricing-info-per-user-headline"><?= __('Monthly')?></h5>
            <span class="pricing-info-per-user-number">¥1980<sup class="pricing-info-super-script">1</sup></span>
        </div>
        <ul class="pricing-info-features">
            <li class="pricing-info-feature-item"><i class="fa fa-check"></i><?= __('100MB file upload');?><sup class="pricing-info-super-script">2</sup></li>
            <li class="pricing-info-feature-item"><i class="fa fa-check"></i><?= __('Unlimited uploads');?></li>
            <li class="pricing-info-feature-item"><i class="fa fa-check"></i><?= __('Chat Messaging');?></li>
            <li class="pricing-info-feature-item"><i class="fa fa-check"></i><?= __('Insight Analytics');?></li>
            <li class="pricing-info-feature-item"><i class="fa fa-check"></i><?= __('Team Administration');?></li>
            <li class="pricing-info-feature-item"><i class="fa fa-check"></i><?= __('Free Online support');?></li>
        </ul>
        <div class="pricing-info-team-price-info">
            <h5 class="team-price-info-headline"><?= __('Number of Members')?></h5>
            <i class="team-price-info-icon fa fa-user"></i>
            <span class="team-price-info-number"><?= $teamMemberCount ?></span>
        </div>
        <div class="pricing-info-team-price-info">
            <h5 class="team-price-info-headline"><?= __('Total Price')?></h5>
            <i class="team-price-info-icon fa fa-credit-card"></i>
            <span class="team-price-info-number">$19,990.00</span>
        </div>
        <?php if($serviceUseStatus!=Team::SERVICE_USE_STATUS_PAID): ?>
            <a href="#" class="subscribe-btn btn btn-primary"><?= __('Subscribe') ?></a>
        <?php endif; ?>
        <p class="pricing-info-footer-legal">
            1: Adding a user will result in an immediate charge based on the remaining days of your billing period.
            <br /><br />
            2: Maximum file upload for sharing files is 100MB. Posting photos allows up to 10MB
        </p>
    </div>
</section>
<?= $this->App->ViewEndComment() ?>