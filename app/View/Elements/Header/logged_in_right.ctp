<?= $this->App->viewStartComment() ?>
<?php
/**
 * @var $is_mb_app
 */
?>
<ul class="glHeaderPc-nav">
    <?php if (empty($userHasNoTeam)): ?>
        <li class="glHeaderPc-nav-menu mod-userAvatar">
            <a class=""
           href="<?= $this->Html->url([
               'controller' => 'users',
               'action'     => 'view_goals',
               'user_id'    => $this->Session->read('Auth.User.id')
           ]) ?>">
            <?=
            $this->Upload->uploadImage($my_prof, 'User.photo', ['style' => 'medium_large'],
                [
                    'width'  => '32',
                    'height' => '32',
                    'alt'    => 'icon',
                    'class'  => 'header-nav-avatar'
                ]) ?>
            </a>
        </li>
        <li class="glHeaderPc-nav-menu">
            <a href="/search" class="glHeaderPc-nav-menu-link">
                <i class="material-icons">search</i>
                <span>Search</span>
            </a>
        </li>
        <li class="glHeaderPc-nav-menu">
            <a id="GlHeaderMenuDropdown-Create" href="#" class="glHeaderPc-nav-menu-link" data-toggle="dropdown">
                <i class="material-icons">add_circle</i>
                <span>Create</span>
            </a>
            <ul class="dropdown-menu glHeader-nav-dropdown"
                aria-labelledby="GlHeaderMenuDropdown-Create">
                <?php if ($this->Session->read('current_team_id')): ?>
                    <li class="glHeader-nav-dropdown-menu">
                        <a class="glHeader-nav-dropdown-menu-link"
                           href="<?= $this->Html->url(['controller' => 'goals', 'action' => 'create', 'step1']) ?>">
                            <div class="glHeader-nav-dropdown-menu-link-left">
                                <i class="material-icons">flag</i>
                            </div>
                            <p class=""><?= __('Create a goal') ?></p>
                        </a>
                    </li>
                    <li class="glHeader-nav-dropdown-menu">
                        <a class="glHeader-nav-dropdown-menu-link" href="#" data-toggle="modal"
                           data-target="#modal_add_circle">
                            <div class="glHeader-nav-dropdown-menu-link-left">
                                <i class="material-icons">group_work</i>
                            </div>
                            <p class=""><?= __('Create a circle') ?></p>
                        </a>
                    </li>
                <?php endif; ?>
                <li class="glHeader-nav-dropdown-menu">
                    <a class="glHeader-nav-dropdown-menu-link"
                       href="<?= $this->Html->url(['controller' => 'teams', 'action' => 'add']) ?>">
                        <div class="glHeader-nav-dropdown-menu-link-left">
                            <i class="material-icons">people</i>
                        </div>
                        <p class=""><?= __('Create a team') ?></p>
                    </a>
                </li>
            </ul>

        </li>
        <li class="glHeaderPc-nav-menu has-notify-dropdown">
            <a href="#" class="glHeaderPc-nav-menu-link click-header-message" data-toggle="dropdown">
                <i class="material-icons">send</i>
                <span>Message</span>
                <div class="btn btn-xs notify-bell-numbers messageNum" style="opacity: 0;">
                    <span>0</span><sup class="notify-plus none">+</sup>
                </div>
            </a>
            <div class="header-nav-message-contents-wrap none">
                <div class="header-nav-message-contents-scrolling">
                    <ul class="header-nav-message-contents message-dropdown" role="menu">
                        <li class="notify-card-empty">
                            <i class="fa fa-smile-o font_33px mr_8px"></i><span
                                class="notify-empty-text"><?= __('No new message') ?></span>
                        </li>
                    </ul>
                </div>
            </div>

        </li>
        <li class="glHeaderPc-nav-menu has-notify-dropdown">
            <a href="#" class="glHeaderPc-nav-menu-link click-header-bell btn-notify-header" data-toggle="dropdown">
                <i class="material-icons">notifications</i>
                <span>Notification</span>
            </a>
            <div class="btn btn-xs bell-notify-box notify-bell-numbers bellNum"
                 style="opacity: 0;">
                <span>0</span><sup class="notify-plus none">+</sup>
            </div>

            <div class="dropdown-menu header-nav-notify-contents-wrap">
                <div class="header-nav-notify-contents-scrolling">
                    <div class=" btn-link notify-mark-allread" style='color:#d2d4d5'>
                        <i class="fa fa-check" class="mark_all_read"></i>
                        <span class="mark_all_read_txt"><?= __('Mark All as Read') ?></span>
                    </div>
                    <ul class="header-nav-notify-contents notify-dropdown-cards bell-dropdown" role="menu"
                        style="overflow-y:scroll">
                        <li class="notify-card-empty">
                            <i class="fa fa-smile-o font_33px mr_8px header-icons"></i><span
                                class="notify-empty-text"><?= __('No new notification') ?></span>
                        </li>
                    </ul>
                </div>
                <a href="#"
                   class="NotifyDropDownReadMore btn btn-link font_bold click-notify-read-more-dropdown"
                   style="display:none;"
                   get-url="<?= $this->Html->url([
                       'controller' => 'notifications',
                       'action'     => 'ajax_get_old_notify_more',
                   ]) ?>">
                </a>

                <a href="<?= $this->Html->url(['controller' => 'notifications', 'action' => 'index']) ?>"
                   get-url="<?= $this->Html->url(['controller' => 'notifications', 'action' => 'index']) ?>"
                   class="call-notifications">
                    <div class="notify-all-view-link">
                        <?= __('See All') ?>
                    </div>
                </a>
            </div>

        </li>
    <?php endif; ?>
    <li class="glHeaderPc-nav-menu">
        <a href="#"
           data-toggle="dropdown"
           class="glHeaderPc-nav-menu-link"
           id="GlHeaderMenuDropdown-More"
            <?php if (!empty($userHasNoTeam) && $userHasNoTeam): ?> style="float:right; margin-right:8px;" <?php endif; ?>>
            <i class="material-icons">dehaze</i>
            <span>More</span>

            <?php if (!empty($all_alert_cnt) && $all_alert_cnt > 0): ?>
                <div class="btn btn-xs notify-function-numbers <?= $is_mb_app ? "mb-header-badge-shift" : "" ?>">
                 <span>
                   <?= $all_alert_cnt ?>
                 </span>
                </div>
            <?php endif; ?>
        </a>
        <ul class="header-nav-function-contents dropdown-menu"
            role="menu"
            aria-labelledby="GlHeaderMenuDropdown-more">
            <?php if (empty($userHasNoTeam)): ?>
                <li class="header-nav-function-contents-list">
                    <?= $this->Html->link(__('User Setting'),
                        ['controller' => 'users', 'action' => 'settings'],
                        ['class' => 'header-nav-function-contents-user-setting']) ?>
                </li>
                <?php //TODO 一時的にチーム管理者はチーム招待リンクを表示
                if (!empty($my_member_status) && Hash::get($my_member_status, 'TeamMember.admin_flg', false)):?>
                    <li class="header-nav-function-contents-list">
                        <?=
                        $this->Html->link(__('Team Setting'),
                            ['controller' => 'teams', 'action' => 'settings'],
                            ['class' => 'header-nav-function-contents-team-setting']) ?>
                    </li>
                    <li class="header-nav-function-contents-list">
                        <?=
                        $this->Html->link(__('Billing'),
                            ['controller' => 'payments', 'action' => 'index'],
                            ['class' => 'header-nav-function-contents-billing']) ?>
                    </li>
                <?php endif; ?>
                <?php if (!empty($is_evaluation_available) && $is_evaluation_available): ?>
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
            <?php endif; ?>
            <li class="header-nav-function-contents-list">
                <a href=<?= $this->Lang->getLangCode() == 'en' ? 'https://drive.google.com/open?id=17c2lbrWEuqQYvOlVSb3Sn1dyFatXq2XD' : 'https://drive.google.com/open?id=1HZBsB3EdS1dciMLY3RXuc1FYHU1uuW73'; ?>
                   class="header-nav-function-contents-user-guidelines"><?= __('User Guide'); ?></a>
            </li>
            <li class="header-nav-function-contents-list">
                <a href="#" data-toggle="modal" data-target="#modal_tutorial"
                   class="header-nav-function-contents-tutorial">
                    <?= __('Tutorial') ?>
                </a>
            </li>
            <?php if (defined('INTERCOM_APP_ID') && INTERCOM_APP_ID): ?>
                <li class="header-nav-function-contents-list">
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
    </li>
</ul>
<?= $this->App->viewEndComment() ?>
