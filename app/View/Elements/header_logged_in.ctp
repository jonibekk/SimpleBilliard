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
 */
?>
<!-- START app/View/Elements/header_logged_in.ctp -->
<div class="navbar navbar-fixed-top navbar-default gl-navbar" style="">
    <div class="gl-nav-container header-container">
        <div class="navbar-header navbar-right">
            <button type="button" class="navbar-toggle gl-hamburger" data-toggle="offcanvas"
                    data-target=".navbar-offcanvas"
                    data-canvas="body">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <div class="pull-right gl-navbar-icons">
                <a href="<?= $this->Html->url('/') ?>" class="header-home">ホーム</a>

                <div class="dropdown gl-navbar-nav-fix header-circle">
                    <a href="#" data-toggle="dropdown" id="download">
                        <i class="fa fa-plus-circle"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-right frame-arrow-icon" aria-labelledby="download">
                        <? if ($this->Session->read('current_team_id')): ?>
                            <li>
                                <a href="#" data-toggle="modal" data-target="#modal_add_circle"><?=
                                    __d('gl',
                                        "サークルを作成") ?></a>
                            </li>
                        <? endif; ?>
                        <li><?=
                            $this->Html->link(__d('gl', "チームを作成"),
                                              ['controller' => 'teams', 'action' => 'add']) ?></li>
                    </ul>
                </div>
                <a class="develop--forbiddenLink" href="#"><i class="fa fa-envelope-o"></i></a>
                <a class="develop--forbiddenLink" href="#"><i class="fa fa-bell-o"></i></a>

                <div class="dropdown gl-navbar-nav-fix">
                    <a href="#" class="dropdown-toggle me-menu-image no-line header-user-profile" data-toggle="dropdown"
                       id="download">
                        <?=
                        $this->Upload->uploadImage($this->Session->read('Auth'), 'User.photo', ['style' => 'small'],
                                                   ['width' => '26px', 'height' => '26px', 'class' => 'img-circle']) ?>
                        <i class="fa fa-caret-down"></i></a>
                    <ul class="dropdown-menu dropdown-menu-right frame-arrow-pic" aria-labelledby="download">
                        <li>
                            <?= $this->Html->link(__d('gl', "設定"), ['controller' => 'users', 'action' => 'settings']) ?>
                        </li>
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
        <div class="navbar-offcanvas offcanvas navmenu-fixed-left">
            <a class="navmenu-brand" href="#"><?= $title_for_layout ?></a>
            <ul class="nav navbar-nav">
                <li><a class="header-logo" href="<?= $this->Html->url('/') ?>"><!--suppress HtmlUnknownTarget -->
                        <img src="../img/goalous.png"
                             class="header-logo"
                             alt="Goalous2.0">&nbsp;
                        <span class="visible-xs-inline"><?= __d('gl', "ホーム") ?></span>
                    </a>
                </li>
                <li><a href="#" class="header-goal"><i class="fa fa-bullseye develop--forbiddenLink"></i>&nbsp;
                        <span class="visible-xs-inline"><?= __d('gl', "ゴール") ?></span>
                    </a></li>
                <li><a href="#" class="header-team"><i class="fa fa-users develop--forbiddenLink"></i>&nbsp;
                        <span class="visible-xs-inline"><?= __d('gl', "チーム") ?></span>
                    </a>
                </li>
                <li>
                    <form class="gl-nav-form-group" role="search">
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
                                                    'class'     => 'form-control gl-nav-team-select',
                                                    'wrapInput' => false,
                                                ))
                        ?>
                    </form>
                </li>
                <li class="header-search-group">
                    <form class="gl-nav-form-group" role="search">
                        <i class="fa fa-search gl-nav-form-icon"></i>
                        <input type="text" class="form-control gl-nav-search develop--search" placeholder="Search">
                    </form>
                </li>
                <li class="circle-list-in-hamburger visible-xxs hidden-xs">
                    <?= $this->element('circle_list_in_hamburger') ?>
                </li>
            </ul>
        </div>
        <!--/.nav-collapse -->
    </div>
</div>

<!-- END app/View/Elements/header_logged_in.ctp -->
