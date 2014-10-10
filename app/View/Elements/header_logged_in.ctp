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
 */
?>
<!-- START app/View/Elements/header_logged_in.ctp -->
<header class="navbar navbar-fixed-top navbar-default gl-navbar" id="header">
    <div class="gl-nav-container header-container">
        <div class="navbar-offcanvas offcanvas navmenu-fixed-left">
            <button type="button" class="close font_33px close-design humbarger-close" data-toggle="offcanvas"
                    data-target=".navbar-offcanvas" aria-hidden="true">
                <span class="close-icon hidden-sm hidden-md hidden-lg">×</span>
            </button>
            <ul class="nav navbar-nav">
                <li><a class="header-logo header_l-icons <?= $current_global_menu == "home" ? "activeColumn" : null ?>"
                       href="<?= $this->Html->url('/') ?>"><!--suppress HtmlUnknownTarget -->
                        <img src="<?= $this->Html->url('/img/goalous_gray.png') ?>"
                             class="header-logo-img"
                             alt="Goalous2.0" width="20px" height="20px">

                        <p class="font_11px font_heavyGray header_icon-text hidden-xs header-link-home header-link">
                            <?= __d('gl', "ホーム") ?>
                        </p>
                        <span class="visible-xs-inline"><?= __d('gl', "ホーム") ?></span>
                    </a>
                </li>
                <li><a class="header-goal header_l-icons <?= $current_global_menu == "goal" ? "activeColumn" : null ?>"
                       href="<?= $this->Html->url(['controller' => 'goals', 'action' => 'index']) ?>">
                        <i class="fa fa-flag header-link header-icon">
                            <p class="font_11px header_icon-text hidden-xs"><?= __d('gl', "ゴール") ?></p>
                        </i>
                        <span class="visible-xs-inline"><?= __d('gl', "ゴール") ?></span>
                    </a></li>
                <li><a href="#" class="header-team header_l-icons develop--forbiddenLink">
                        <i class="fa fa-users header-link header-icon">
                            <p class="font_11px header_icon-text hidden-xs"><?= __d('gl', "チーム") ?></p>
                        </i>&nbsp;
                        <span class="visible-xs-inline"><?= __d('gl', "チーム") ?></span>
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
                        <i class="fa fa-search gl-nav-form-icon"></i>
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
            <button type="button" class="navbar-toggle gl-hamburger" data-toggle="offcanvas"
                    data-target=".navbar-offcanvas">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <div class="pull-right nav-icons">
                <a href="<?= $this->Html->url('/') ?>" class="header-home header-link">ホーム</a>

                <div class="dropdown gl-navbar-nav-fix header-circle">
                    <a href="#" data-toggle="dropdown" id="download">
                        <i class="fa fa-plus-circle header-link header-icon"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-right frame-arrow-icon" aria-labelledby="download">
                        <? if ($this->Session->read('current_team_id')): ?>
                            <li>
                                <a href="#" data-toggle="modal" data-target="#modal_add_circle">
                                    <i class="fa fa-circle-o header-drop-icons">
                                        <span class="font_verydark"><?= __d('gl', "サークルを作成") ?></span>
                                    </i>
                                </a>
                            </li>
                        <? endif; ?>
                        <li>
                            <a href="<?= $this->Html->url(['controller' => 'teams', 'action' => 'add']) ?>">
                                <i class="fa fa-users header-drop-icons">
                                    <span class="font_verydark"><?= __d('gl', 'チームを作成') ?></span>
                                </i>
                            </a>
                        </li>
                        <li><a href="<?= $this->Html->url(['controller' => 'goals', 'action' => 'add']) ?>">
                                <i class="fa fa-flag header-drop-icons">
                                    <span class="font_verydark"><?= __d('gl', 'ゴールを作成') ?></span>
                                </i>
                            </a>
                        </li>
                    </ul>
                </div>
                <a class="develop--forbiddenLink" href="#"><i class="fa fa-envelope-o header-link header-icon"></i></a>
                <a class="develop--forbiddenLink" href="#"><i class="fa fa-bell-o header-link header-icon"></i></a>

                <div class="dropdown gl-navbar-nav-fix">
                    <a href="#" class="dropdown-toggle me-menu-image no-line header-user-profile" data-toggle="dropdown"
                       id="download">
                        <?=
                        $this->Upload->uploadImage($this->Session->read('Auth'), 'User.photo', ['style' => 'small'],
                                                   ['width' => '26px', 'height' => '26px', 'class' => 'img-circle']) ?>
                        <i class="fa fa-caret-down header-profile-icon"></i></a>
                    <ul class="dropdown-menu dropdown-menu-right frame-arrow-pic" aria-labelledby="download">
                        <li>
                            <?= $this->Html->link(__d('gl', "設定"), ['controller' => 'users', 'action' => 'settings']) ?>
                        </li>
                        <?
                        //TODO 一時的にチーム管理者はチーム招待リンクを表示
                        if (isset($my_member_status['TeamMember']) && $my_member_status['TeamMember']['admin_flg']):?>
                            <li>
                                <?=
                                $this->Html->link(__d('gl', "メンバーを招待"),
                                                  ['controller' => 'teams', 'action' => 'invite']) ?>
                            </li>
                        <? endif; ?>
                        <li><a href="#" data-toggle="modal" data-target="#modal_tutorial"><?=
                                __d('gl',
                                    "チュートリアル") ?></a></li>
                        <li><?=
                            $this->Html->link(__d('gl', "ログアウト"),
                                              ['controller' => 'users', 'action' => 'logout']) ?></li>
                        <li class="divider"></li>
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

<!-- END app/View/Elements/header_logged_in.ctp -->
