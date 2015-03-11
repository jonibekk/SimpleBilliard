<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 5/28/14
 * Time: 5:04 PM
 *
 * @var       $title_for_layout string
 * @var       $this             CodeCompletionView
 * @var       $nav_disable
 * @var array $my_teams
 * @var       $current_global_menu
 * @var       $avail_sub_menu
 */
?>
<!-- START app/View/Elements/header_logged_in.ctp -->
<header class="navbar navbar-fixed-top navbar-default gl-navbar" id="header" xmlns="http://www.w3.org/1999/html">
    <div class="navbar-toggle-box">
        <button type="button" class="navbar-toggle hamburger header-toggle-icon" data-toggle="offcanvas"
                data-target=".navbar-offcanvas">
            <i class="fa fa-navicon toggle-icon"></i>
            <!--
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
            -->
        </button>
    </div>
    <div class="nav-container header-container">
        <div class="navbar-offcanvas offcanvas navmenu-fixed-left top_50px">
            <ul class="nav navbar-nav">
                <li class="mtb_5px mtb-sm_0">
                    <a class="header-logo header_l-icons hoverPic <?= $current_global_menu == "home" ? "activeColumn" : null ?>"
                       href="<?= $this->Html->url('/') ?>"><!--suppress HtmlUnknownTarget -->
                        <div class="ta-sm_c">
                            <img src="<?= $this->Html->url('/img/logo_off.png') ?>" class="header-logo-img"
                                 alt="Goalous2.0" width="20px" height="20px">

                            <p class="font_11px font_heavyGray header_icon-text hidden-xs header-link"><?= __d('gl',
                                                                                                               "ホーム") ?></p>
                            <span class="visible-xs-inline va_bl ml_5px"><?= __d('gl', "ホーム") ?></span>
                        </div>
                    </a>
                </li>
                <li class="mtb_5px mtb-sm_0">
                    <a class="header-goal header_l-icons <?= $current_global_menu == "goal" ? "activeColumn" : null ?>"
                       href="<?= $this->Html->url(['controller' => 'goals', 'action' => 'index']) ?>">
                        <div class="ta-sm_c">
                            <i class="fa fa-flag header-link header-icon nav-xxs-icon"></i>

                            <p class="font_11px header_icon-text hidden-xs header-link"><?= __d('gl', "ゴール") ?></p>
                            <span class="visible-xs-inline ml_5px"><?= __d('gl', "ゴール") ?></span>

                        </div>
                    </a>
                </li>
                <li class="mtb_5px mtb-sm_0">
                    <a href="#" class="header-team header_l-icons develop--forbiddenLink">
                        <div class="ta-sm_c">
                            <i class="fa fa-users header-link header-icon nav-xxs-icon"></i>

                            <p class="font_11px header_icon-text hidden-xs header-link"><?= __d('gl', "チーム") ?></p>
                            <span class="visible-xs-inline ml_5px"><?= __d('gl', "チーム") ?></span>
                        </div>
                    </a>
                </li>
                <li>
                    <form class="nav-form-group" role="search">
                        <?
                        echo $this->Form->input('current_team',
                                                array(
                                                    'type'      => 'select',
                                                    'options'   => !empty($my_teams) ? $my_teams : [__d('gl',
                                                                                                        'チームがありません')],
                                                    'value'     => $this->Session->read('current_team_id'),
                                                    'id'        => 'SwitchTeam',
                                                    'label'     => false,
                                                    'div'       => false,
                                                    'class'     => 'form-control nav-team-select font_12px disable-change-warning',
                                                    'wrapInput' => false,
                                                ))
                        ?>
                    </form>
                </li>
                <li class="header-search-group">
                    <form class="nav-form-group" role="search">
                        <i class="fa fa-search nav-form-icon"></i>
                        <input type="text"
                               class="form-control nav-search font_12px disable-change-warning develop--search"
                               placeholder="Search">
                    </form>
                </li>
                <li class="circle-list-in-hamburger visible-xxs hidden-xs">
                    <?= $this->element('circle_list_in_hamburger') ?>
                </li>
            </ul>
        </div>
        <div class="navbar-header navbar-right">
            <div class="pull-right nav-icons">
                <div class="dropdown navbar-nav-fix">
                    <a href="#"
                       class="dropdown-toggle me-menu-image font_verydark no-line header-user-profile pull-right"
                       data-toggle="dropdown"
                       id="download">
                        <?=
                        $this->Upload->uploadImage($this->Session->read('Auth'), 'User.photo', ['style' => 'small'],
                                                   ['width' => '26px', 'height' => '26px', 'alt' => 'icon', 'class' => 'pull-left img-circle mtb_3px']) ?>
                        <i class="fa fa-caret-down header-profile-icon visible-xxs pull-right"></i>
                        <span
                            class="font_11px hidden-xxs header-home header-link pr_5px mlr_5px ptb_5px bd-r"><?= $this->Session->read('Auth.User.display_first_name') ?></span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-right frame-arrow-pic" aria-labelledby="download">
                        <li class="text-align_c"><?= __d('gl', "準備中") ?></li>

                        <!--
-->
                    </ul>
                </div>
                <a href="<?= $this->Html->url('/') ?>" class="header-home header-link"><?= __d('gl', "ホーム") ?></a>

                <div class="dropdown navbar-nav-fix header-circle">
                    <a href="#" data-toggle="dropdown" id="download">
                        <i class="fa fa-plus-circle header-link header-icon"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-right frame-arrow-icon" aria-labelledby="download">
                        <? if ($this->Session->read('current_team_id')): ?>
                            <li><a href="<?= $this->Html->url(['controller' => 'goals', 'action' => 'add']) ?>">
                                    <i class="fa fa-flag header-drop-icons"></i>
                                    <span class="font_verydark"><?= __d('gl', 'ゴールを作成') ?></span>
                                </a>
                            </li>
                            <li>
                                <a href="#" data-toggle="modal" data-target="#modal_add_circle">
                                    <i class="fa fa-circle-o header-drop-icons"></i>
                                    <span class="font_verydark"><?= __d('gl', "サークルを作成") ?></span>
                                </a>
                            </li>
                        <? endif; ?>
                        <li>
                            <a href="<?= $this->Html->url(['controller' => 'teams', 'action' => 'add']) ?>">
                                <i class="fa fa-users header-drop-icons"></i>
                                <span class="font_verydark"><?= __d('gl', 'チームを作成') ?></span>
                            </a>
                        </li>
                    </ul>
                </div>
                <a class="develop--forbiddenLink" href="#"><i class="fa fa-envelope-o header-link header-icon"></i></a>
                <div class="dropdown dropdown-menu-right navbar-nav-fix header-circle">
                    <div class="btn btn-danger btn-xs bell-notify-box"
                         id="bellNum"
                         style="position: absolute;
                                margin: 5px 0 0 20px;
                                color: #fff;
                                font-size: 10px;
                                opacity: 0;
                                background-color:red!important;
                                display:block">0</div>
                    <a id="click-header-bell" class="header-link" data-toggle="dropdown" href="#">
                        <i class="fa fa-flag fa-bell-o header-link header-icon header-drop-icons"></i>
                    </a>
                    <div class="frame-arrow-notify dropdown-menu dropdown-menu-right notify-dropdown-area">
                        <div class="notify-head"><?=__d('gl',"お知らせ")?></div>
                        <ul class="notify-dropdown-lists" id="bell-dropdown" role="menu">
                            <li class="notify-card-empty" id="notifyCardEmpty">
                                <i class="fa fa-smile-o font_33px mr_8px"></i><span class="notify-empty-text"><?=__d('gl',"未読の通知はありません。")?></span>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="pull-right header-function dropdown">
                    <a href="#"
                       class="font_lightGray-gray font_14px plr_4px pt_1px pb_2px bd-radius_4px header-function-link"
                       data-toggle="dropdown"
                       id="download">
                        <i class="fa fa-cog header-function-icon"></i>
                        <i class="fa fa-caret-down goals-column-fa-caret-down header-function-icon"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-right frame-arrow-icon" role="menu"
                        aria-labelledby="dropdownMenu1">
                        <li>
                            <?= $this->Html->link(__d('gl', "ユーザ設定"),
                                                  ['controller' => 'users', 'action' => 'settings']) ?>
                        </li>
                        <li><a href="#" data-toggle="modal" data-target="#modal_tutorial"><?=
                                __d('gl',
                                    "チュートリアル") ?></a></li>
                        <li><?=
                            $this->Html->link(__d('gl', "ログアウト"),
                                              ['controller' => 'users', 'action' => 'logout']) ?></li>
                        <li class="divider"></li>
                        <?
                        //TODO 一時的にチーム管理者はチーム招待リンクを表示
                        if (isset($my_member_status['TeamMember']) && $my_member_status['TeamMember']['admin_flg']):?>
                            <li>
                                <?=
                                $this->Html->link(__d('gl', 'チーム設定'),
                                                  ['controller' => 'teams', 'action' => 'settings']) ?>
                            </li>
                        <? endif; ?>
                        <li><?=
                            $this->Html->link(__d('home', 'Blog'), 'http://blog.goalous.com/',
                                              ['target' => '_blank']) ?></li>
                    </ul>
                </div>
            </div>
        </div>
        <!--/.nav-collapse -->
    </div>
</header>
<? if ($avail_sub_menu): ?>
    <div class="col col-xxs-12 hidden-md hidden-lg sp-feed-alt height_40px" id="SubHeaderMenu">
        <div class="col col-xxs-6 text-align_r">
            <a class="font_lightGray-veryDark no-line plr_18px sp-feed-link inline-block pt_12px height_40px sp-feed-active"
               id="SubHeaderMenuFeed">
                <?= __d('gl', "ニュースフィード") ?>
            </a>
        </div>
        <div class="col col-xxs-6">
            <a class="font_lightGray-veryDark no-line plr_18px sp-feed-link inline-block pt_12px height_40px"
               id="SubHeaderMenuGoal">
                <?= __d('gl', "関連ゴール") ?>
            </a>
        </div>
    </div>
<? endif; ?>
<!-- END app/View/Elements/header_logged_in.ctp -->
