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
 */
?>
<?= $this->App->viewStartComment()?>

<header class="header">
    <div class="navbar navbar-fixed-top navbar-default gl-navbar <?= $is_mb_app ? "mb-app-nav" : null ?>"
         id="header">
        <div class="nav-container header-container">
            <?php
            // Condition that returns true if user is on a sub-page and is viewing on mobile app
            if ( $this->BackBtn->checkPage() &&  $is_mb_app) { ?>
                <?= $this->element('/header_back_btn') ?>
            <?php } else { ?>
                <?= $this->element('Header/navigation'); ?>
                <?= $this->element('Header/logged_in_right'); ?>
            <?php } ?>
        </div>
    </div>

    <?php if ($serviceUseStatus == Team::SERVICE_USE_STATUS_READ_ONLY): ?>
    <div class="read-only-alert font_verydark">
        <div class="container">
            <?php
            $readOnlyEndDate = $this->TimeEx->formatYearDayI18nFromDate($readOnlyEndDate);
            if ($isTeamAdmin) {
                'このチームは、<strong>%sまで</strong>読み取り専用期間です。それ以降はご利用いただけません。通常のご利用を再開したい場合は、<a href="/payments/apply">こちら</a>から有料プランを契約してください。';

                echo __('Your team will remain in a read-only state until <strong>%s</strong>. Following this date, you will no longer be able to use Goalous. If you want to resume normal usage, please <a href="/payments/apply">subscribe</a> to our payment plan.', $readOnlyEndDate);
            } else {
                'このチームは、<strong>%sまで</strong>読み取り専用期間です。それ以降はご利用いただけません。通常のご利用を再開したい場合は、チーム管理者にお問い合わせください。';

                echo __('Your team will remain in a read-only state until <strong>%s</strong>. Following this date, you will no longer be able to use Goalous. If you want to resume normal usage, please contact to your team administrators.', $readOnlyEndDate);
            }
            ?>
        </div>
    </div>
    <?php endif; ?>

</header>
<?= $this->App->viewEndComment()?>
