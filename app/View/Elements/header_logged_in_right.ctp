<!-- start app/View/Elements/header_logged_in_right -->
<div class="header-right-navigations clearfix">
    <a class="header-user-avatar" href="<?= $this->Html->url(['controller' => 'users', 'action' => 'view_goals', 'user_id' => $this->Session->read('Auth.User.id')]) ?>">
        <?=
        $this->Upload->uploadImage($this->Session->read('Auth'), 'User.photo', ['style' => 'small'],
                                   ['width' => '24', 'height' => '24', 'alt' => 'icon', 'class' => 'header-nav-avatar']) ?>
        <span class="header-user-name hidden-xxs header-home js-header-link">
            <?= $this->Session->read('Auth.User.display_first_name') ?>
        </span>
    </a>
    <a href="<?= $this->Html->url('/') ?>" class="header-user-home header-home js-header-link"><?= __d('gl', 'ホーム') ?></a>
    <div class="header-dropdown-add">
        <a href="#" data-toggle="dropdown" id="download" class="btn-addition-header">
            <i class="header-dropdown-icon-add fa fa-plus-circle js-header-link header-icons"></i>
        </a>
        <ul class="header-nav-add-contents dropdown-menu "
            aria-labelledby="download">
            <?php if ($this->Session->read('current_team_id')): ?>
                <li><a class="header-nav-add-contents-anchor" href="<?= $this->Html->url(['controller' => 'goals', 'action' => 'add']) ?>">
                        <i class="fa fa-flag header-drop-icons"></i>
                        <span class=""><?= __d('gl', 'ゴールを作成') ?></span>
                    </a>
                </li>
                <li>
                    <a class="header-nav-add-contents-anchor" href="#" data-toggle="modal" data-target="#modal_add_circle">
                        <i class="fa fa-circle-o header-drop-icons"></i>
                        <span class=""><?= __d('gl', 'サークルを作成') ?></span>
                    </a>
                </li>
                <li>
                    <a class="header-nav-add-contents-anchor" href="<?= $this->Html->url(['controller' => 'teams', 'action' => 'add_group_vision']) ?>">
                        <i class="fa fa-plane header-drop-icons"></i>
                        <span class=""><?= __d('gl', 'グループビジョンを作成') ?></span>
                    </a>
                </li>
                <?php if ($my_member_status['TeamMember']['admin_flg']): ?>
                    <li>
                        <a class="header-nav-add-contents-anchor" href="<?= $this->Html->url(['controller' => 'teams', 'action' => 'add_team_vision']) ?>">
                            <i class="fa fa-rocket header-drop-icons"></i>
                            <span class=""><?= __d('gl', 'チームビジョンを作成') ?></span>
                        </a>
                    </li>
                <?php endif; ?>
            <?php endif; ?>
            <li>
                <a class="header-nav-add-contents-anchor" href="<?= $this->Html->url(['controller' => 'teams', 'action' => 'add']) ?>">
                    <i class=" fa fa-users header-drop-icons"></i>
                    <span class=""><?= __d('gl', 'チームを作成') ?></span>
                </a>
            </li>
        </ul>
    </div>
    <div class="header-dropdown-message ">
        <a id="click-header-message" class="btn-message-header" data-toggle="dropdown" href="#">
            <i class="header-dropdown-icon-message fa fa-paper-plane-o js-header-link header-icons"></i>
            <div class="btn btn-xs bell-notify-box notify-bell-numbers" id="messageNum" style="opacity: 0;">
                <span>0</span><sup class="notify-plus none">+</sup>
            </div>
        </a>
        <div class="frame-arrow-notify  header-nav-message-contents-wrap none">
            <ul class="header-nav-notify-contents" id="message-dropdown" role="menu">
                <li class="notify-card-empty" id="messageNotifyCardEmpty">
                    <i class="fa fa-smile-o font_33px mr_8px"></i><span
                        class="notify-empty-text"><?= __d('gl', '未読のメッセージはありません。') ?></span>
                </li>
            </ul>
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
            <ul class="header-nav-notify-contents notify-dropdown-cards" id="bell-dropdown" role="menu"
                style="overflow-y:scroll">
                <li class="notify-card-empty" id="notifyCardEmpty">
                    <i class="fa fa-smile-o font_33px mr_8px header-icons"></i><span
                        class="notify-empty-text"><?= __d('gl', '未読の通知はありません。') ?></span>
                </li>
            </ul>
            <a id="NotifyDropDownReadMore" href="#"
               class="btn btn-link font_bold click-notify-read-more-dropdown none"
               get-url="<?= $this->Html->url(['controller' => 'notifications',
                                              'action' => 'ajax_get_old_notify_more', ]) ?>">
            </a>

            <a href="<?= $this->Html->url(['controller' => 'notifications', 'action' => 'index']) ?>">
                <div class="text-align_c notify-all-view-link">
                    <?= __d('gl', 'すべて見る') ?>
                </div>
            </a>
        </div>
    </div>
    <div class="header-dropdown-functions header-function">
        <a href="#"
           class="btn-function-header"
           data-toggle="dropdown"
           id="download">
            <i class="header-dropdown-icon-functions fa fa-cog header-function-icon header-drop-icons js-header-link header-icons"></i>
            <?php if ($all_alert_cnt > 0): ?>
                <div class="btn btn-xs notify-function-numbers">
                 <span>
                   <?= $all_alert_cnt ?>
                 </span>
                </div>
            <?php endif; ?>
        </a>
        <ul class="header-nav-function-contents dropdown-menu " role="menu"
            aria-labelledby="dropdownMenu1">
            <li>
                <?= $this->Html->link(__d('gl', 'ユーザ設定'),
                                      ['controller' => 'users', 'action' => 'settings']) ?>
            </li>
            <li>
                <a href="#" data-toggle="modal" data-target="#modal_tutorial">
                    <?= __d('gl', 'チュートリアル') ?>
                </a>
            </li>
            <li>
                <a href="#" class="youtube" rel="_J_wKHgKWLg" id="ExplainGoal">
                    <?= __d('gl', 'ゴールの説明') ?>
                </a>
            </li>
            <li>
                <?php if (isset($unapproved_cnt) === true && $unapproved_cnt > 0) { ?>
                    <div class="btn btn-danger btn-xs sub_cnt_alert">
                        <?php echo $unapproved_cnt; ?>
                    </div>
                <?php } ?>
                <?= $this->Html->link(__d('gl', 'ゴール認定'),
                                      ['controller' => 'goal_approval', 'action' => 'index']) ?>
            </li>
            <li><?=
                $this->Html->link(__d('gl', 'ログアウト'),
                                  ['controller' => 'users', 'action' => 'logout']) ?></li>
            <li class="divider"></li>
            <?php if ($is_evaluation_available): ?>
                <li>
                    <?php if (viaIsSet($evaluable_cnt) && $evaluable_cnt > 0): ?>
                        <div class="btn btn-danger btn-xs sub_cnt_alert"><?= $evaluable_cnt ?></div>
                    <?php endif; ?>

                    <?=
                    $this->Html->link(__d('gl', '評価'),
                                      ['controller' => 'evaluations', 'action' => 'index']) ?>
                </li>
            <?php endif; ?>
            <?php //TODO 一時的にチーム管理者はチーム招待リンクを表示
            if (viaIsSet($my_member_status['TeamMember']['admin_flg']) && $my_member_status['TeamMember']['admin_flg']):?>
                <li>
                    <?=
                    $this->Html->link(__d('gl', 'チーム設定'),
                                      ['controller' => 'teams', 'action' => 'settings']) ?>
                </li>
            <?php endif; ?>
            <li>
                <?=
                $this->Html->link(__d('home', 'Blog'), 'http://blog.goalous.com/',
                                  ['target' => '_blank']) ?>
            </li>
            <?php if (USERVOICE_API_KEY && $this->Session->read('Auth.User.id')): ?>
            <li>
                <a href="javascript:void(0)" data-uv-lightbox="classic_widget" data-uv-mode="full" data-uv-primary-color="#f0636f" data-uv-link-color="#007dbf" data-uv-default-mode="feedback" data-uv-forum-id="<?php
                         if ($is_isao_user)
                         {
                             echo USERVOICE_FORUM_ID_PRIVATE;
                         }
                         else
                         {
                             echo USERVOICE_FORUM_ID_PUBLIC;
                         }
                         ?>"><?=__d('gl','Feedback')?>
                </a>
            </li>
            <?php endif;?>
        </ul>
    </div>
</div>
<!-- end app/View/Elements/header_logged_in_right -->
