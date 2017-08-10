<?= $this->App->viewStartComment() ?>
<section class="panel subscription-status">
    <div class="panel-container">
        <h3 class="subscription-status-headline"><?= __("Team Summary") ?></h3>
        <h4 class="status-text">
            <?php if($serviceUseStatus==Team::SERVICE_USE_STATUS_FREE_TRIAL): ?>
                <i class="fa fa-unlock-alt free-trial"></i>
                <?= __('Free Trial') ?>
            <?php elseif($serviceUseStatus==Team::SERVICE_USE_STATUS_PAID): ?>
                <i class="fa fa-check subscribed"></i>
                <?= __('Subscribed') ?>
            <?php elseif($serviceUseStatus==Team::SERVICE_USE_STATUS_READ_ONLY): ?>
                <i class="fa fa-lock read-only"></i>
                <?= __('Read Only') ?>
            <?php else: ?>
                <i class="fa fa-lock deactivated"></i>
                <?= __('Deactivated') ?>
            <?php endif; ?>
        </h4>
        <?php if($serviceUseStatus==Team::SERVICE_USE_STATUS_FREE_TRIAL): ?>
                <p class="subscription-status-detail"><?= __('Your team is currently using Goalous as a free trial. Your free trial will end on September 20, 2017'); ?></p>
        <?php elseif($serviceUseStatus==Team::SERVICE_USE_STATUS_PAID): ?>
                <p class="subscription-status-detail"><?= __('Your team has full access to Goalous.<br /><br />Go achieve your goal!'); ?></p>
        <?php elseif($serviceUseStatus==Team::SERVICE_USE_STATUS_READ_ONLY): ?>
                <p class="subscription-status-detail"><?= __('Your team can no longer create or edit content on Goalous. Your team account will be deactived on November 20, 2017'); ?></p>
        <?php else: //Deactivated ?>
                <p class="subscription-status-detail"><?= __('Your team no longer has access to  Goalous. Your team account will be deleted on December 20, 2017'); ?></p>
        <?php endif; ?>
        <div class="team-price-info">
            <h5 class="team-price-info-headline"><?= __('Monthly')?></h5>
            <span class="team-price-info-number">&yen;1,980 <div class="team-price-info-detail">/member /month<sup class="team-price-info-super-script">1</sup></div></span>
        </div>
        <div class="team-price-info">
            <h5 class="team-price-info-headline"><?= __('Members')?></h5>
            <i class="team-price-info-icon fa fa-user"></i>
            <span class="team-price-info-number"><?= $teamMemberCount ?></span>
        </div>
        <div class="team-price-info">
            <h5 class="team-price-info-headline"><?= __('Estimated Total')?></h5>
            <i class="team-price-info-icon fa fa-credit-card"></i>
            <span class="team-price-info-number">&yen;9,900<div class="team-price-info-detail">/month</div></span>
        </div>
        <?php if($serviceUseStatus!=Team::SERVICE_USE_STATUS_PAID): ?>
        <a href="#" class="subscribe-btn btn btn-primary"><?= __('Subscribe') ?></a>
        <ul class="pricing-info-features">
            <li class="team-price-info-feature-item"><i class="fa fa-check"></i><?= __('100MB file upload');?><sup class="team-price-info-super-script">2</sup></li>
            <li class="team-price-info-feature-item"><i class="fa fa-check"></i><?= __('Unlimited uploads');?></li>
            <li class="team-price-info-feature-item"><i class="fa fa-check"></i><?= __('Chat Messaging');?></li>
            <li class="team-price-info-feature-item"><i class="fa fa-check"></i><?= __('Insight Analytics');?></li>
            <li class="team-price-info-feature-item"><i class="fa fa-check"></i><?= __('Team Administration');?></li>
            <li class="team-price-info-feature-item"><i class="fa fa-check"></i><?= __('Free Online support');?></li>
        </ul>
        <?php endif; ?>
        <ol class="team-price-info-footer-legal">
            <li class="team-price-info-footer-legal-item">Adding a user will result in an immediate charge based on the remaining days of your billing period.</li>
            <?php if($serviceUseStatus!=Team::SERVICE_USE_STATUS_PAID): ?>
                <li class="team-price-info-footer-legal-item">Maximum file upload for sharing files is 100MB. Posting photos allows up to 10MB</li>
            <?php endif; ?>
        </ol>
    </div>
</section>
<?= $this->App->ViewEndComment() ?>