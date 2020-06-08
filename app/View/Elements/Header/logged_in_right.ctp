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
            <a href="/search" class="glHeaderPc-nav-menu-link <?= Router::url() === '/search' ? 'active' : '' ?>" >
                <i class="material-icons">search</i>
                <span><?= __('Search')?></span>
            </a>
        </li>
        <li class="glHeaderPc-nav-menu">
            <a id="GlHeaderMenuDropdown-Create" href="#" class="glHeaderPc-nav-menu-link" data-toggle="dropdown">
                <i class="material-icons">add_circle</i>
                <span><?= __('Create')?></span>
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
                        <a class="glHeader-nav-dropdown-menu-link" href="/circles/create">
                            <div class="glHeader-nav-dropdown-menu-link-left">
                                <i class="material-icons">group_work</i>
                            </div>
                            <p class=""><?= __('Create a circle') ?></p>
                        </a>
                    </li>
                <?php endif; ?>
                <?php if (IS_DEMO != true): ?>
                    <li class="glHeader-nav-dropdown-menu">
                        <a class="glHeader-nav-dropdown-menu-link"
                        href="<?= $this->Html->url(['controller' => 'teams', 'action' => 'add']) ?>">
                            <div class="glHeader-nav-dropdown-menu-link-left">
                                <i class="material-icons">people</i>
                            </div>
                            <p class=""><?= __('Create a team') ?></p>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>

        </li>
        <li class="glHeaderPc-nav-menu has-notify-dropdown">
<!--            TODO.Renewal: Fix to add dropdown bosh new/old Goalous -->
<!--            <a href="#" class="glHeaderPc-nav-menu-link click-header-message" data-toggle="dropdown">-->
            <a href="/topics" class="glHeaderPc-nav-menu-link">
                <i class="material-icons">send</i>
                <span><?= __('Message')?></span>
                <div class="btn btn-xs notify-bell-numbers messageNum" style="opacity: 0;">
                    <span>0</span><sup class="notify-plus none">+</sup>
                </div>
            </a>
            <?php // TODO.Renewal: Fix badge count processing when init display/realtime change ?>
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
        <li class="glHeaderPc-nav-menu">
            <a href="/circles" class="glHeaderPc-nav-menu-link">
                <i class="material-icons">group_work</i>
                <span><?= __('Circle')?></span>
                    <div class="btn btn-xs notify-function-numbers mod-small" id="nav-circle-badge" style="opacity: <?= !empty($circle_badge_cnt) ? 1: 0 ?>;">
                         <span>
                         </span>
                    </div>
            </a>
        </li>

        <li class="glHeaderPc-nav-menu has-notify-dropdown">
            <a href="#" class="glHeaderPc-nav-menu-link click-header-bell btn-notify-header" data-toggle="dropdown">
                <i class="material-icons">notifications</i>
                <span><?= __('Notification')?></span>
                <?php // TODO.Renewal: Fix badge count processing when init display/realtime change ?>
                <div class="btn btn-xs bell-notify-box notify-bell-numbers bellNum" style="opacity:0;">
                    <span>0</span>
                </div>
            </a>

            <div class="dropdown-menu notiListFlyout">
                <div class="notiListFlyout-inner">
                    <div class="header">
                        <div class="left">
                            <a href="/users/settings#notification">
                                <i class="material-icons">settings</i>
                            </a>
                        </div>
                        <div class="center">
                            <span class="title"><?= __("Notification")?></span>
                        </div>
                        <div class="right"><a class="mark_all_read"><?= __("Dismiss All")?></a></div>
                    </div>
                    <div class="content-container" id="NotiListScroll">
                        <ul class="header-nav-notify-contents notify-dropdown-cards" role="menu">
                        </ul>
                        <a
                            href="#"
                            style="display: none;"
                            class="noti-loading NotifyDropDownReadMore click-notify-read-more-dropdown"
                            get-url="<?= $this->Html->url([
                                'controller' => 'notifications',
                                'action'     => 'ajax_get_old_notify_more',
                            ]) ?>"
                        >
                            <svg class="spinner" width="24px" height="24px" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg"><circle class="path" cx="50%" cy="50%" r="7"></circle></svg>
                        </a>
                    </div>
                    <div class="footer js-notiListFlyout-footer" style="display:none;">
                        <a href="/notifications" class="view-all-link">
                            <?= __("View All Notifications")?>
                        </a>
                    </div>
                </div>
            </div>

        </li>
    <?php endif; ?>
    <li class="glHeaderPc-nav-menu">
        <a href="/others"
           class="glHeaderPc-nav-menu-link"
           id="GlHeaderMenuDropdown-More"
            <?php if (!empty($userHasNoTeam) && $userHasNoTeam): ?> style="float:right; margin-right:8px;" <?php endif; ?>>
            <i class="material-icons">dehaze</i>
            <span><?= __('More')?></span>
            <?php if (!empty($all_alert_cnt) && $all_alert_cnt > 0): ?>
                <div class="btn btn-xs notify-function-numbers">
                 <span class="<?= $all_alert_cnt > 99 ? 'oval' : ''?>">
                   <?= $all_alert_cnt ?>
                 </span>
                </div>
            <?php endif; ?>
        </a>
    </li>
</ul>
<?= $this->App->viewEndComment() ?>
