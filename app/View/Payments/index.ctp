<?php
/**
 * @var $team
 * @var $payment
 * @var $chargeMemberCount
 * @var $serviceUseStatus
 * @var $chargeInfo
 * @var $subTotal
 * @var $amountPerUser
 * @var $isCampaignTeam
 * @var $campaignUsers
 * @var $campaignPrice
 */
?>
<?= $this->App->viewStartComment() ?>
<section
    class="panel payment subscription-status <?= $serviceUseStatus == Goalous\Model\Enum\Team\ServiceUseStatus::PAID ? 'has-subnav' : ''; ?>">
    <?= $this->element('Payment/method_select') ?>
    <div class="panel-container">
        <h3><?= __("Team Summary") ?></h3>
        <h5><?= __('Team Status') ?></h5>
        <?php if ($serviceUseStatus == Team::SERVICE_USE_STATUS_FREE_TRIAL): ?>
        <h4 class="status-text free-trial">
            <i class="fa fa-ticket"></i>
            <?= __('Free Trial') ?>
            <?php elseif ($serviceUseStatus == Team::SERVICE_USE_STATUS_PAID): ?>
            <h4 class="status-text subscribed">
                <i class="fa fa-ticket"></i>
                <?php if ($isCampaignTeam): ?>
                    <?= __('%d members', $campaignUsers); ?>
                <?php else: ?>
                    <?= __('Paid Plan') ?>
                <?php endif; ?>
                <?php elseif ($serviceUseStatus == Team::SERVICE_USE_STATUS_READ_ONLY): ?>
                <h4 class="status-text read-only">
                    <i class="fa fa-ticket"></i>
                    <?= __('Read Only') ?>
                    <?php else: ?>
                    <h4 class="status-text deactivated">
                        <i class="fa fa-ticket "></i>
                        <?= __('Deactivated') ?>
                        <?php endif; ?>
                    </h4>
                    <p class="subscription-status-detail">
                        <?php if ($serviceUseStatus == Team::SERVICE_USE_STATUS_FREE_TRIAL): ?>
                            <?= __('Your team is currently using Goalous as a free trial. Your free trial will end on %s',
                                $this->TimeEx->formatYearDayI18nFromDate($team['service_use_state_end_date'])); ?>
                        <?php elseif ($serviceUseStatus == Team::SERVICE_USE_STATUS_PAID && $isCampaignTeam): ?>
                            <?= __('Using %d of %d members', $chargeMemberCount, $campaignUsers); ?>
                        <?php elseif ($serviceUseStatus == Team::SERVICE_USE_STATUS_PAID): ?>
                            <?= __('Your team has full access to Goalous.<br />Go achieve your goal!'); ?>
                        <?php elseif ($serviceUseStatus == Team::SERVICE_USE_STATUS_READ_ONLY): ?>
                            <?= __('Your team can no longer create or edit content on Goalous. Your team account will be deactivated on %s',
                                $this->TimeEx->formatYearDayI18nFromDate($team['service_use_state_end_date'])); ?>
                        <?php else: //Deactivated ?>
                            <?= __('Your team no longer has access to  Goalous. Your team account will be deleted on %s',
                                $this->TimeEx->formatYearDayI18nFromDate($team['service_use_state_end_date'])); ?>
                        <?php endif; ?>
                    </p>
                    <?php if ($serviceUseStatus != Team::SERVICE_USE_STATUS_PAID): ?>
                        <a href="/payments/apply" class="btn btn-primary"><?= __('Upgrade to Paid Plan') ?></a>
                    <?php elseif ($isCampaignTeam): ?>
                        <a href="/payments/upgrade_plan" class="btn btn-primary"><?= __('Upgrade Plan') ?></a>
                    <?php endif; ?>
                    <div class="hr"></div>
                    <?php if (!$isCampaignTeam): ?>
                        <div class="team-price-info">
                            <h5><?= __('Monthly') ?></h5>
                            <span class="team-price-info-number"><?= h($amountPerUser) ?>
                                <div class="team-price-info-detail">/<?= __('member'); ?>
                                    /<?= __('month'); ?><sup class="team-price-info-super-script">*1</sup></div></span>
                        </div>
                    <?php endif; ?>
                    <div class="team-price-info">
                        <h5><?= __('Active Members') ?><sup
                                class="team-price-info-super-script">*2</sup></h5>
                        <i class="team-price-info-icon fa fa-user"></i>
                        <span class="team-price-info-number"><?= h($chargeMemberCount) ?></span>
                    </div>
                    <?php if (!$isCampaignTeam): ?>
                        <div class="team-price-info">
                            <h5><?= __('Estimated Total') ?></h5>
                            <i class="team-price-info-icon fa fa-credit-card"></i>
                            <span class="team-price-info-number"><?= h($subTotal) ?>
                                <div class="team-price-info-detail">/<?= __('month'); ?></div>
                        </span>
                        </div>
                    <?php elseif (($serviceUseStatus == Team::SERVICE_USE_STATUS_PAID)): ?>
                        <div class="team-price-info">
                            <h5><?= __('Estimated Total') ?></h5>
                            <i class="team-price-info-icon fa fa-credit-card"></i>
                            <span class="team-price-info-number"><?= h($campaignPrice) ?>
                                <div class="team-price-info-detail">/<?= __('month'); ?></div>
                        </span>
                        </div>
                    <?php endif; ?>
                    <?php if ($serviceUseStatus != Team::SERVICE_USE_STATUS_PAID): ?>
                        <div class="feature-category">
                            <strong class="icon icon-heart"><?= __('Goal features'); ?></strong>
                            <ul>
                                <li><?= __('Create &amp; share Goals for your project'); ?></li>
                                <li><?= __('Team members can join to collaborate towards your Goal.'); ?></li>
                                <li><?= __('Organize Goals into separate <q>Key Results</q>'); ?></li>
                                <li><?= __('Post <q>Actions</q> that contribute to the <q>Key Result</q>'); ?></li>
                                <li><?= __('Graphical progress that helps motivate your team'); ?></li>
                            </ul>
                        </div>
                        <div class="feature-category">
                            <strong class="icon icon-message"><?= __('Communication features'); ?></strong>
                            <ul>
                                <li><?= __('Create private Circles where members can communicate'); ?></li>
                                <li><?= __('Give reactions to posts with likes and comments'); ?></li>
                                <li><?= __('Realtime chat messaging'); ?></li>
                                <li><?= __('Attach and share files up to 100MB per file'); ?></li>
                                <li><?= __('See how many members have read your posts and comments'); ?></li>
                            </ul>
                        </div>
                        <div class="feature-category">
                            <strong class="icon icon-lock"><?= __('Other Features'); ?></strong>
                            <ul>
                                <li><?= __('Unlimited team members'); ?></li>
                                <li><?= __('Two-Step verification for stronger security'); ?></li>
                                <li><?= __('English and Japanese language available'); ?></li>
                                <li><?= __('Live chat support (Initial response will be within the next business day)'); ?></li>
                                <li><?= __('iOS(8.4 or higher) and Android(6.0 or higher) APPs'); ?></li>
                            </ul>
                        </div>
                        <a href="/pricing?backBtn=true" target="_blank"
                           class="feature-more-link"><?= __("View more details") ?> <span
                                class="fa fa-angle-right"></span></a>
                    <?php endif; ?>
                    <ol class="team-price-info-legal">
                        <?php if ($isCampaignTeam): ?>
                            <li><?= __("Your team qualifies for a special campaign price (annual contract, paid monthly)."); ?></li>
                            <li><?= __("You cannot downgrade the plan during the contract period. However, you may downgrade the plan during the last month of the current contract period."); ?></li>
                            <li><?= __("If you cancel during the contract period, you’ll be charged 100% of your remaining contract obligation and your service will immediately end."); ?></li>
                        <?php else: ?>
                            <li><?= __("Added team member's usage fee will be charged based on daily rate."); ?></li>
                            <li><?= __('Team members who fit the following criteria are considered to be billable monthly active members:'); ?>
                                <ul class="team-price-info-legal">
                                    <li><?= __('Team members who are active by the payment date (those not deactivated by the team administrator)'); ?></li>
                                    <li><?= __('In the event that team members were added by the team administrator between the current month’s payment date and 1 day prior to the following month’s payment date, the number of billable members will be more than the number of active members falling on the current month’s payment date. In addition, in that situation, added team member’s usage fee will be charged based on daily rate.'); ?></li>
                                </ul>
                            </li>
                            <?php if ($serviceUseStatus != Team::SERVICE_USE_STATUS_PAID): ?>
                                <li><?= __('Maximum file upload for sharing files is 100MB. Posting photos allows up to 10MB'); ?></li>
                            <?php endif; ?>
                        <?php endif; ?>
                    </ol>
    </div>
</section>
<?= $this->App->ViewEndComment() ?>
