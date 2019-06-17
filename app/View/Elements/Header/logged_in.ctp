<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 5/28/14
 * Time: 5:04 PM
 *
 * @var CodeCompletionView $this
 * @var                    $title_for_layout string
 * @var                    $nav_disable
 * @var array              $my_teams
 * @var                    $current_global_menu
 * @var                    $my_member_status
 * @var                    $is_evaluation_available
 * @var                    $evaluable_cnt
 * @var                    $unapproved_cnt
 * @var                    $all_alert_cnt
 * @var                    $is_mb_app
 * @var                    $serviceUseStatus
 * @var                    $isTeamAdmin
 * @var                    $stateEndDate
 */
?>

<?= $this->App->viewStartComment() ?>
<header class="header">
    <div id="goalousNavigation"
         class="navbar navbar-fixed-top navbar-default gl-navbar <?= $is_mb_app_ios_high_header ? "mb-app-nav" : null ?>"
         id="header">
        <div class="nav-container header-container">
            <?php
            // Condition that returns true if user is on a sub-page and is viewing on mobile app

            if ($userHasNoTeam) { ?>
                <?= $this->element('Header/navigation'); ?>
            <?php } elseif ($this->BackBtn->checkPage() && $is_mb_app) { ?>
                <?= $this->element('Header/back_btn') ?>
            <?php } else { ?>
                <?= $this->element('Header/navigation'); ?>
                <?= $this->element('Header/search'); ?>
            <?php } ?>
        </div>
    </div>

    <?php if (IS_DEMO):?>
      <div class="demo-alert">
        <div class="container">
          <p class="demo-alert-description">
              <?= __("The demo environment is a shared environment with other customers.")?>
              <?= __("Please do not post confidential or personal information.") ?>
          </p>
          <p class=""><?= __("Demo site data flush every day at %s o'clock", DEMO_RESET_TIME)?></p>
        </div>
      </div>
    <?php endif;?>

  <?php // Show header alert only team admin temporarily. [GL-6387]  ?>
    <?php if (!empty($isTeamAdmin) && $isTeamAdmin && !($this->request->params['controller'] == 'payments' && $this->request->params['action'] == 'apply')): ?>
        <?php
        // TODO.Payment: must refactoring.
        // Acquire only necessary information when necessary

        if (in_array($serviceUseStatus, [Team::SERVICE_USE_STATUS_FREE_TRIAL, Team::SERVICE_USE_STATUS_READ_ONLY])
            || (isset($teamCreditCardStatus) && in_array($teamCreditCardStatus,
                    [Team::STATUS_CREDIT_CARD_EXPIRED, Team::STATUS_CREDIT_CARD_EXPIRE_SOON]))
            || (isset($statusPaymentFailed) && $statusPaymentFailed)
        ) : ?>
            <div class="banner-alert font_verydark <?= $is_mb_app_ios_high_header ? "mod-mb-app" : "" ?>">
                <div class="container">
                    <button type="button" class="close js-disappear-banner" aria-label="Close"><span aria-hidden="true">&times;</span>
                    </button>
                    <?= $this->Banner->getBannerMessage($serviceUseStatus, $isTeamAdmin, $stateEndDate,
                        $teamCreditCardStatus ?? Team::STATUS_CREDIT_CARD_CLEAR, $teamCreditCardExpireDate ?? '',
                        $statusPaymentFailed ?? false) ?>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>

</header>
<?= $this->App->viewEndComment() ?>
