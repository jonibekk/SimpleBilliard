<?= $this->App->viewStartComment() ?>
<section class="panel payment subscription-status <?= $serviceUseStatus == Goalous\Model\Enum\Team\ServiceUseStatus::PAID ? 'has-subnav' : '';?>">
    <?= $this->element('Payment/method_select') ?>
    <div class="panel-container">
        <h3><?= __("Team Summary") ?></h3>
        <h5><?= __('Team Status') ?></h5>
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
        <p class="subscription-status-detail">
            <?php if ($serviceUseStatus == Team::SERVICE_USE_STATUS_FREE_TRIAL): ?>
            <?= __('Your team is currently using Goalous as a free trial. Your free trial will end on %s', $this->TimeEx->formatYearDayI18nFromDate($team['service_use_state_end_date'])); ?>
            <?php elseif ($serviceUseStatus == Team::SERVICE_USE_STATUS_PAID): ?>
            <?= __('Your team has full access to Goalous.<br />Go achieve your goal!'); ?>
            <?php elseif ($serviceUseStatus == Team::SERVICE_USE_STATUS_READ_ONLY): ?>
            <?= __('Your team can no longer create or edit content on Goalous. Your team account will be deactivated on %s', $this->TimeEx->formatYearDayI18nFromDate($team['service_use_state_end_date'])); ?>
            <?php else: //Deactivated ?>
                <?= __('Your team no longer has access to  Goalous. Your team account will be deleted on %s', $this->TimeEx->formatYearDayI18nFromDate($team['service_use_state_end_date'])); ?>
            <?php endif; ?>
        </p>
        <a href="/payments/apply" class="btn btn-primary"><?= __('Upgrade to Paid Plan') ?></a>
        <div class="hr"></div>
        <div class="team-price-info">
            <h5><?= __('Monthly') ?></h5>
            <span class="team-price-info-number"><?= $amountPerUser ?><div class="team-price-info-detail">/<?= __('member'); ?>
                    /<?= __('month'); ?><sup class="team-price-info-super-script">*1</sup></div></span>
        </div>
        <div class="team-price-info">
            <h5><?= __('Active Members') ?><sup
                    class="team-price-info-super-script">*2</sup></h5>
            <i class="team-price-info-icon fa fa-user"></i>
            <span class="team-price-info-number"><?= $chargeMemberCount ?></span>
        </div>
        <div class="team-price-info">
            <h5><?= __('Estimated Total') ?></h5>
            <i class="team-price-info-icon fa fa-credit-card"></i>
            <span class="team-price-info-number"><?= $subTotal?><div
                    class="team-price-info-detail">/<?= __('month'); ?></div></span>
        </div>
        <?php if ($serviceUseStatus != Team::SERVICE_USE_STATUS_PAID): ?>
            <ul>
                <li><i class="fa fa-check"></i><?= __('100MB file upload'); ?><sup
                        class="team-price-info-super-script">*3</sup></li>
                <li><i class="fa fa-check"></i><?= __('Unlimited uploads'); ?></li>
                <li><i class="fa fa-check"></i><?= __('Chat Messaging'); ?></li>
                <li><i class="fa fa-check"></i><?= __('Insight Analytics'); ?></li>
                <li><i class="fa fa-check"></i><?= __('Team Administration'); ?>
                </li>
                <li><i class="fa fa-check"></i><?= __('Free Online support'); ?>
                </li>
            </ul>
        <?php endif; ?>
        <ol class="team-price-info-legal">
            <li><?= __("Added team member's usage fee will be charged based on daily rate."); ?></li>
            <li><?= __('Team members who fit the following criteria are considered to be billable monthly active members:'); ?>
                <ul class="team-price-info-legal">
                    <li><?=__('Team members who are active by the payment date (those not deactivated by the team administrator)');?></li>
                    <li><?=__('In the event that team members were added by the team administrator between the current month’s payment date and 1 day prior to the following month’s payment date, the number of billable members will be more than the number of active members falling on the current month’s payment date. In addition, in that situation, added team member’s usage fee will be charged based on daily rate.');?></li>
                </ul>
            </li>
            <?php if ($serviceUseStatus != Team::SERVICE_USE_STATUS_PAID): ?>
                <li><?= __('Maximum file upload for sharing files is 100MB. Posting photos allows up to 10MB'); ?></li>
            <?php endif; ?>
        </ol>
    </div>
</section>
<?= $this->App->ViewEndComment() ?>
