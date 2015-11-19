<?php
/**
 * @var $is_mb_app
 */
?>
<!-- start app/View/Elements/header_logged_in_right -->
<div class="<?= $is_mb_app ? "header-right-navigations-mb-app" : "header-right-navigations" ?> clearfix"
     xmlns="http://www.w3.org/1999/html">
    <?php if (!$is_mb_app): ?>
        <a class="header-user-avatar"
           href="<?= $this->Html->url(['controller' => 'users', 'action' => 'view_goals', 'user_id' => $this->Session->read('Auth.User.id')]) ?>">
            <?=
            $this->Upload->uploadImage($this->Session->read('Auth'), 'User.photo', ['style' => 'small'],
                                       ['width' => '24', 'height' => '24', 'alt' => 'icon', 'class' => 'header-nav-avatar']) ?>
            <span class="header-user-name hidden-xxs header-home js-header-link">
            <?= $this->Session->read('Auth.User.display_first_name') ?>
        </span>
        </a>
        <a href="<?= $this->Html->url('/') ?>" class="header-user-home header-home js-header-link"><?= __d('gl',
                                                                                                           'ホーム') ?></a>
    <?php endif; ?>

    <div class="header-dropdown-add">
        <a href="#" data-toggle="dropdown" id="download" class="btn-addition-header">
            <i class="fa fa-plus-circle header-icons header-dropdown-icon-add <?= $is_mb_app ? "mb-app-nav-icon" : "header-drop-icons js-header-link" ?>"></i>
        </a>
        <ul class="header-nav-add-contents dropdown-menu "
            aria-labelledby="download">
            <?php if ($this->Session->read('current_team_id')): ?>
                <li class="header-nav-add-contents-goal">
                    <a class="header-nav-add-contents-anchor"
                       href="<?= $this->Html->url(['controller' => 'goals', 'action' => 'add']) ?>">
                        <i class="fa fa-flag header-drop-icons"></i>

                        <p class="header-nav-add-contents-goal"><?= __d('gl', 'ゴールを作成') ?></p>
                    </a>
                </li>
                <li class="header-nav-add-contents-circle">
                    <a class="header-nav-add-contents-anchor" href="#" data-toggle="modal"
                       data-target="#modal_add_circle">
                        <i class="fa fa-circle-o header-drop-icons"></i>

                        <p class="header-nav-add-contents-circle"><?= __d('gl', 'サークルを作成') ?></p>
                    </a>
                </li>
            <?php endif; ?>
            <li class="header-nav-add-contents-team">
                <a class="header-nav-add-contents-anchor"
                   href="<?= $this->Html->url(['controller' => 'teams', 'action' => 'add']) ?>">
                    <i class=" fa fa-users header-drop-icons"></i>

                    <p class="header-nav-add-contents-team"><?= __d('gl', 'チームを作成') ?></p>
                </a>
            </li>
        </ul>
    </div>
    <?php if (!$is_mb_app): ?>
        <div class="header-dropdown-message ">
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
                                class="notify-empty-text"><?= __d('gl', '未読のメッセージはありません。') ?></span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div id="HeaderDropdownNotify" class="header-dropdown-notify">
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
                        <span id="mark_all_read_txt"><?= __d('gl', 'Mark All as Read') ?></span>
                    </div>
                    <ul class="header-nav-notify-contents notify-dropdown-cards" id="bell-dropdown" role="menu"
                        style="overflow-y:scroll">
                        <li class="notify-card-empty" id="notifyCardEmpty">
                            <i class="fa fa-smile-o font_33px mr_8px header-icons"></i><span
                                class="notify-empty-text"><?= __d('gl', '未読の通知はありません。') ?></span>
                        </li>
                    </ul>
                </div>
                <a id="NotifyDropDownReadMore" href="#"
                   class="btn btn-link font_bold click-notify-read-more-dropdown none"
                   get-url="<?= $this->Html->url(['controller' => 'notifications',
                                                  'action'     => 'ajax_get_old_notify_more',]) ?>">
                </a>

                <a href="<?= $this->Html->url(['controller' => 'notifications', 'action' => 'index']) ?>">
                    <div class="notify-all-view-link">
                        <?= __d('gl', 'すべて見る') ?>
                    </div>
                </a>
            </div>
        </div>
    <?php endif; ?>
    <div class="header-dropdown-functions header-function">
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
                <?= $this->Html->link(__d('gl', 'ユーザ設定'),
                                      ['controller' => 'users', 'action' => 'settings'],
                                      ['class' => 'header-nav-function-contents-user-setting']) ?>
            </li>
            <li class="header-nav-function-contents-list">
                <a href="#" data-toggle="modal" data-target="#modal_tutorial"
                   class="header-nav-function-contents-tutorial">
                    <?= __d('gl', 'チュートリアル') ?>
                </a>
            </li>
            <li class="header-nav-function-contents-list">
                <a href="#" rel="_J_wKHgKWLg" id="ExplainGoal" class="youtube header-nav-function-contents-about-goal">
                    <?= __d('gl', 'ゴールについて') ?>
                </a>
            </li>
            <li class="header-nav-function-contents-list">
                <?php if (isset($unapproved_cnt) === true && $unapproved_cnt > 0) { ?>
                    <div class="btn btn-danger btn-xs sub_cnt_alert">
                        <?php echo $unapproved_cnt; ?>
                    </div>
                <?php } ?>
                <?= $this->Html->link(__d('gl', 'ゴール認定'),
                                      ['controller' => 'goal_approval', 'action' => 'index'],
                                      ['class' => 'header-nav-function-contents-approvement']) ?>
            </li>
            <li class="header-nav-function-contents-list">
                <?=
                $this->Html->link(__d('gl', 'ログアウト'),
                                  ['controller' => 'users', 'action' => 'logout'],
                                  ['class' => 'header-nav-function-contents-logout']) ?>
            </li>
            <?php if ($is_evaluation_available): ?>
                <li class="header-nav-function-contents-list">
                    <?php if (viaIsSet($evaluable_cnt) && $evaluable_cnt > 0): ?>
                        <div class="btn btn-danger btn-xs sub_cnt_alert"><?= $evaluable_cnt ?></div>
                    <?php endif; ?>

                    <?=
                    $this->Html->link(__d('gl', '評価'),
                                      ['controller' => 'evaluations', 'action' => 'index'],
                                      ['class' => 'header-nav-function-contents-evaluation']) ?>
                </li>
            <?php endif; ?>
            <?php //TODO 一時的にチーム管理者はチーム招待リンクを表示
            if (viaIsSet($my_member_status['TeamMember']['admin_flg']) && $my_member_status['TeamMember']['admin_flg']):?>
                <li class="header-nav-function-contents-list">
                    <?=
                    $this->Html->link(__d('gl', 'チーム設定'),
                                      ['controller' => 'teams', 'action' => 'settings'],
                                      ['class' => 'header-nav-function-contents-team-setting']) ?>
                </li>
            <?php endif; ?>
            <li class="header-nav-function-contents-list">
                <?=
                $this->Html->link(__d('home', 'Blog'), 'http://blog.goalous.com/',
                                  array('target' => '_blank', 'class' => 'header-nav-function-contents-blog')) ?>
            </li>
            <?php if (USERVOICE_API_KEY && $this->Session->read('Auth.User.id')): ?>
                <li class="header-nav-function-contents-list">
                    <a class="header-nav-function-contents-feedback" href="javascript:void(0)"
                       data-uv-lightbox="classic_widget" data-uv-mode="full"
                       data-uv-primary-color="#f0636f" data-uv-link-color="#007dbf" data-uv-default-mode="feedback"
                       data-uv-forum-id="<?php
                       if ($is_isao_user) {
                           echo USERVOICE_FORUM_ID_PRIVATE;
                       }
                       else {
                           echo USERVOICE_FORUM_ID_PUBLIC;
                       }
                       ?>"><?= __d('gl', 'Feedback') ?>
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</div>
<!-- end app/View/Elements/header_logged_in_right -->
