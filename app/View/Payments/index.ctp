<?= $this->App->viewStartComment() ?>
<section class="panel subscription-status <?= $serviceUseStatus == Goalous\Model\Enum\Team\ServiceUseStatus::PAID ? 'has-subnav' : '';?>">
    <?= $this->element('Payment/method_select') ?>
    <div class="panel-container">
        <h3 class="subscription-status-headline"><?= __("Team Summary") ?></h3>
        <h4 class="status-text">
            <?php if ($serviceUseStatus == Team::SERVICE_USE_STATUS_FREE_TRIAL): ?>
                <i class="fa fa-unlock-alt free-trial"></i>
                <?= __('Free Trial') ?>
            <?php elseif ($serviceUseStatus == Team::SERVICE_USE_STATUS_PAID): ?>
                <i class="fa fa-check subscribed"></i>
                <?= __('Paid Plan') ?>
            <?php elseif ($serviceUseStatus == Team::SERVICE_USE_STATUS_READ_ONLY): ?>
                <i class="fa fa-lock read-only"></i>
                <?= __('Read Only') ?>
            <?php else: ?>
                <i class="fa fa-lock deactivated"></i>
                <?= __('Deactivated') ?>
            <?php endif; ?>
        </h4>
        <?php if ($serviceUseStatus == Team::SERVICE_USE_STATUS_FREE_TRIAL): ?>
            <p class="subscription-status-detail"><?= __('Your team is currently using Goalous as a free trial. Your free trial will end on September 20, 2017'); ?></p>
        <?php elseif ($serviceUseStatus == Team::SERVICE_USE_STATUS_PAID): ?>
            <p class="subscription-status-detail"><?= __('Your team has full access to Goalous.<br /><br />Go achieve your goal!'); ?></p>
        <?php elseif ($serviceUseStatus == Team::SERVICE_USE_STATUS_READ_ONLY): ?>
            <p class="subscription-status-detail"><?= __('Your team can no longer create or edit content on Goalous. Your team account will be deactived on November 20, 2017'); ?></p>
        <?php else: //Deactivated ?>
            <p class="subscription-status-detail"><?= __('Your team no longer has access to  Goalous. Your team account will be deleted on December 20, 2017'); ?></p>
        <?php endif; ?>
        <div class="team-price-info">
            <h5 class="team-price-info-headline"><?= __('Monthly') ?></h5>
            <span class="team-price-info-number"><?= $amountPerUser ?><div class="team-price-info-detail">/<?= __('member'); ?>
                    /<?= __('month'); ?><sup class="team-price-info-super-script">1</sup></div></span>
        </div>
        <div class="team-price-info">
            <h5 class="team-price-info-headline"><?= __('Active Members') ?><sup
                    class="team-price-info-super-script">2</sup></h5>
            <i class="team-price-info-icon fa fa-user"></i>
            <span class="team-price-info-number"><?= $chargeMemberCount ?></span>
        </div>
        <div class="team-price-info">
            <h5 class="team-price-info-headline"><?= __('Estimated Total') ?></h5>
            <i class="team-price-info-icon fa fa-credit-card"></i>
            <span class="team-price-info-number"><?= $subTotal?><div
                    class="team-price-info-detail">/<?= __('month'); ?></div></span>
        </div>
        <?php if ($serviceUseStatus != Team::SERVICE_USE_STATUS_PAID): ?>
            <a href="/payments/apply" class="subscribe-btn btn btn-primary"><?= __('Upgrade Plan') ?></a>
            <ul class="pricing-info-features">
                <li class="team-price-info-feature-item"><i class="fa fa-check"></i><?= __('100MB file upload'); ?><sup
                        class="team-price-info-super-script">3</sup></li>
                <li class="team-price-info-feature-item"><i class="fa fa-check"></i><?= __('Unlimited uploads'); ?></li>
                <li class="team-price-info-feature-item"><i class="fa fa-check"></i><?= __('Chat Messaging'); ?></li>
                <li class="team-price-info-feature-item"><i class="fa fa-check"></i><?= __('Insight Analytics'); ?></li>
                <li class="team-price-info-feature-item"><i class="fa fa-check"></i><?= __('Team Administration'); ?>
                </li>
                <li class="team-price-info-feature-item"><i class="fa fa-check"></i><?= __('Free Online support'); ?>
                </li>
            </ul>
        <?php endif; ?>
        <ol class="team-price-info-footer-legal">
            <li class="team-price-info-footer-legal-item">
                <?= __("Added team member's usage fee will be charged based on daily rate."); ?><br>
            </li>
            <li class="team-price-info-footer-legal-item"><?= __('Team members who fit the following criteria are considered to be billable monthly active members:'); ?></li>
            <ul>
                <li><?=__('Team members who are active by the payment date (those not deactivated by the team administrator)');?></li>
                <li><?=__('In the event that team members were added by the team administrator between the current month’s payment date and 1 day prior to the following month’s payment date, the number of billable members will be more than the number of active members falling on the current month’s payment date. In addition, in that situation, added team member’s usage fee will be charged based on daily rate.');?></li>
            </ul>

            <?php if ($serviceUseStatus != Team::SERVICE_USE_STATUS_PAID): ?>
                <li class="team-price-info-footer-legal-item"><?= __('Maximum file upload for sharing files is 100MB. Posting photos allows up to 10MB'); ?></li>
            <?php endif; ?>
        </ol>
    </div>
</section>
<?= $this->App->ViewEndComment() ?>
