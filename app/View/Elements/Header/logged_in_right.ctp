<?php
/**
 * @var $is_mb_app
 */
?>
<?= $this->App->viewStartComment() ?>
<div class="<?= $is_mb_app ? "header-right-navigations-mb-app" : "header-right-navigations" ?> clearfix"
     xmlns="http://www.w3.org/1999/html">
    <?php if (!$is_mb_app): ?>
        <a class="header-user-avatar"
           href="<?= $this->Html->url([
               'controller' => 'users',
               'action'     => 'view_goals',
               'user_id'    => $this->Session->read('Auth.User.id')
           ]) ?>">
            <?=
            $this->Upload->uploadImage($my_prof, 'User.photo', ['style' => 'small'],
                ['width' => '24', 'height' => '24', 'alt' => 'icon', 'class' => 'header-nav-avatar']) ?>
            <span class="header-user-name js-header-link">
            <?= h($this->Session->read('Auth.User.display_first_name')) ?>
        </span>
        </a>
        <a href="<?= $this->Html->url('/') ?>" class="header-user-home  js-header-link"><?= __(
                'Home') ?></a>
    <?php endif; ?>

    <div class="<?= $is_mb_app ? "mb-app-header-setup" : "header-setup" ?> header-icon-zoom" id="setup">
        <a href="/setup/top/" class="btn-header-setup">
            <i class="fa fa-book  header-icons header-dropdown-icon-add header-function-icon header-icons <?= $is_mb_app ? "mb-app-nav-icon" : "header-drop-icons js-header-link" ?>"
               id="setupIcon"></i>
            <?php if (isset($setup_rest_count) && $setup_rest_count >= 1): ?>
                <div class="btn btn-xs bell-notify-box notify-setup-numbers" id="setupNum">
                    <span><?= $setup_rest_count ?></span>
                </div>
            <?php endif; ?>
        </a>
    </div>

    <div class="<?= $is_mb_app ? "mb-app-header-dropdown-add" : "header-dropdown-add" ?> header-icon-zoom">
        <a href="#" data-toggle="dropdown" id="download" class="btn-addition-header">
            <i class="fa fa-plus-circle header-icons header-dropdown-icon-add <?= $is_mb_app ? "mb-app-nav-icon" : "header-drop-icons js-header-link" ?>"></i>
        </a>
        <ul class="header-nav-add-contents dropdown-menu "
            aria-labelledby="download">
            <?php if ($this->Session->read('current_team_id')): ?>
                <li class="header-nav-add-contents-goal">
                    <a class="header-nav-add-contents-anchor"
                       href="<?= $this->Html->url(['controller' => 'goals', 'action' => 'create', 'step1']) ?>">
                        <i class="fa fa-flag header-drop-icons"></i>

                        <p class="header-nav-add-contents-goal"><?= __('Create a goal') ?></p>
                    </a>
                </li>
                <li class="header-nav-add-contents-circle">
                    <a class="header-nav-add-contents-anchor" href="#" data-toggle="modal"
                       data-target="#modal_add_circle">
                        <i class="fa fa-circle-o header-drop-icons"></i>

                        <p class="header-nav-add-contents-circle"><?= __('Create a circle') ?></p>
                    </a>
                </li>
            <?php endif; ?>
            <li class="header-nav-add-contents-team">
                <a class="header-nav-add-contents-anchor"
                   href="<?= $this->Html->url(['controller' => 'teams', 'action' => 'add']) ?>">
                    <i class=" fa fa-users header-drop-icons"></i>
                    <p class="header-nav-add-contents-team"><?= __('Create a team') ?></p>
                </a>
            </li>
        </ul>
    </div>
    <div class="header-dropdown-message has-notify-dropdown header-icon-zoom <?= $is_mb_app ? "hide" : null ?>">
        <a id="click-header-message" class="btn-message-header" data-toggle="dropdown" href="#">
            <i class="header-dropdown-icon-message fa fa-paper-plane-o js-header-link header-icons"></i>

            <div class="btn btn-xs bell-notify-box notify-bell-numbers" id="messageNum" style="opacity: 0;">
                <span>0</span><sup class="notify-plus none">+</sup>
            </div>
        </a>

        <div class="frame-arrow-notify  header-nav-message-contents-wrap none">
            <div class="header-nav-message-contents-scrolling">
                <ul class="header-nav-message-contents" id="message-dropdown" role="menu">
                    <li class="notify-card-empty" id="messageNotifyCardEmpty">
                        <i class="fa fa-smile-o font_33px mr_8px"></i><span
                            class="notify-empty-text"><?= __('No new message') ?></span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div id="HeaderDropdownNotify" class="header-dropdown-notify has-notify-dropdown header-icon-zoom <?= $is_mb_app ? "hide" : null ?>">
        <a id="click-header-bell" class="btn-notify-header" data-toggle="dropdown" href="#">
            <i class="header-dropdown-icon-notify fa fa-flag fa-bell-o header-drop-icons js-header-link header-icons"></i>

            <div class="btn btn-xs bell-notify-box notify-bell-numbers"
                 id="bellNum" style="opacity: 0;">
                <span>0</span><sup class="notify-plus none">+</sup>
            </div>
        </a>

        <div class="dropdown-menu header-nav-notify-contents-wrap">
            <div class="header-nav-notify-contents-scrolling">
                <div class=" btn-link notify-mark-allread" style='color:#d2d4d5'>
                    <i class="fa fa-check" id="mark_all_read"></i>
                    <span id="mark_all_read_txt"><?= __('Mark All as Read') ?></span>
                </div>
                <ul class="header-nav-notify-contents notify-dropdown-cards" id="bell-dropdown" role="menu"
                    style="overflow-y:scroll">
                    <li class="notify-card-empty" id="notifyCardEmpty">
                        <i class="fa fa-smile-o font_33px mr_8px header-icons"></i><span
                            class="notify-empty-text"><?= __('No new notification') ?></span>
                    </li>
                </ul>
            </div>
            <a id="NotifyDropDownReadMore" href="#"
               class="btn btn-link font_bold click-notify-read-more-dropdown"
               style="display:none;"
               get-url="<?= $this->Html->url([
                   'controller' => 'notifications',
                   'action'     => 'ajax_get_old_notify_more',
               ]) ?>">
            </a>

            <a href="<?= $this->Html->url(['controller' => 'notifications', 'action' => 'index']) ?>" get-url="<?= $this->Html->url(['controller' => 'notifications', 'action' => 'index']) ?>"
               class="call-notifications">
                <div class="notify-all-view-link">
                    <?= __('See All') ?>
                </div>
            </a>
        </div>
    </div>
    <div class="<?= $is_mb_app ? "mb-app-header-dropdown-functions" : "header-dropdown-functions" ?> header-icon-zoom header-function">
    <a href="#"
       class="btn-function-header"
       data-toggle="dropdown"
       id="header-cog-dropdown">
        <i class="header-dropdown-icon-functions fa fa-cog header-function-icon header-icons <?= $is_mb_app ? "mb-app-nav-icon" : "header-drop-icons js-header-link" ?>"></i>
        <?php if ($all_alert_cnt > 0): ?>
            <div class="btn btn-xs notify-function-numbers">
                 <span>
                   <?= $all_alert_cnt ?>
                 </span>
            </div>
        <?php endif; ?>
    </a>
    <ul class="header-nav-function-contents dropdown-menu" role="menu"
        aria-labelledby="dropdownMenu1">
        <li class="header-nav-function-contents-list">
            <?= $this->Html->link(__('User Setting'),
                ['controller' => 'users', 'action' => 'settings'],
                ['class' => 'header-nav-function-contents-user-setting']) ?>
        </li>
        <?php //TODO 一時的にチーム管理者はチーム招待リンクを表示
        if (Hash::get($my_member_status, 'TeamMember.admin_flg') && $my_member_status['TeamMember']['admin_flg']):?>
            <li class="header-nav-function-contents-list">
                <?=
                $this->Html->link(__('Team Setting'),
                    ['controller' => 'teams', 'action' => 'settings'],
                    ['class' => 'header-nav-function-contents-team-setting']) ?>
            </li>
        <?php endif; ?>
        <?php if ($is_evaluation_available): ?>
            <li class="header-nav-function-contents-list">
                <?=
                $this->Html->link(__('Evaluation'),
                    ['controller' => 'evaluations', 'action' => 'index'],
                    ['class' => 'header-nav-function-contents-evaluation'])
                ?>
                <?php if (viaIsSet($evaluable_cnt) && $evaluable_cnt > 0): ?>
                    <span class="header-nav-function-eval-count"><?= $evaluable_cnt ?></span>
                <?php endif; ?>
            </li>
        <?php endif; ?>
        <li class="header-nav-function-contents-list">
            <?= $this->Html->link(__('Goal Approval'),
                ['controller' => 'goals', 'action' => 'approval', 'list'],
                ['class' => 'header-nav-function-contents-approvement'])
            ?>
            <?php if (isset($unapproved_cnt) === true && $unapproved_cnt > 0) { ?>
                <div class="header-nav-function-approve-count">
                    <?php echo $unapproved_cnt; ?>
                </div>
            <?php } ?>
        </li>
        <li class="header-nav-function-contents-list">
            <?=
            $this->Html->link(__('User Guide (jp)'),
                '/document/UserGuidelines.pdf',
                ['class' => 'header-nav-function-contents-user-guidelines', 'target' => '_blank']) ?>
        </li>
        <li class="header-nav-function-contents-list">
            <a href="#" rel="_J_wKHgKWLg" id="ExplainGoal" class="youtube header-nav-function-contents-about-goal">
                <?= __('How to make a Goal (jp)') ?>
            </a>
        </li>
        <li class="header-nav-function-contents-list">
            <a href="#" data-toggle="modal" data-target="#modal_tutorial"
               class="header-nav-function-contents-tutorial">
                <?= __('Tutorial') ?>
            </a>
        </li>
        <?php if (defined('INTERCOM_APP_ID') && INTERCOM_APP_ID): ?>
            <li class="header-nav-function-contents-list" id="IntercomLink">
                <a href="mailto:<?= INTERCOM_APP_ID ?>@incoming.intercom.io"
                   class="intercom-launcher header-nav-function-contents-support"><?= __('Support') ?></a>
            </li>
        <?php endif; ?>
        <li class="header-nav-function-contents-list">
            <?=
            $this->Html->link(__('Logout'),
                ['controller' => 'users', 'action' => 'logout'],
                ['class' => 'header-nav-function-contents-logout']) ?>
        </li>
    </ul>
</div>
</div>
<div class="header-white-bg <?= $is_mb_app ? "mod-mb-app" : "" ?>"></div>
<?= $this->App->viewEndComment() ?>
