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
                <?= $this->element('Header/back_btn') ?>
            <?php } else { ?>
                <?= $this->element('Header/navigation'); ?>
                <?= $this->element('Header/logged_in_right'); ?>
            <?php } ?>
        </div>
    </div>
</header>
<?= $this->App->viewEndComment()?>