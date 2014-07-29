<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 5/28/14
 * Time: 5:04 PM
 *
 * @var       $title_for_layout string
 * @var       $this             View
 * @var       $nav_disable
 * @var array $my_teams
 */
?>
<div class="navbar navbar-fixed-top navbar-default gl-navbar" style="">
    <div class="container gl-nav-container">
        <div class="navbar-header navbar-right">
            <button type="button" class="navbar-toggle gl-hamburger" data-toggle="offcanvas"
                    data-target=".navbar-offcanvas"
                    data-canvas="body">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <div class="pull-right gl-navbar-icons">
                <div class="dropdown gl-navbar-nav-fix">
                    <a href="#" data-toggle="dropdown" id="download">
                        <i class="fa fa-plus-circle"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="download">
                        <? if ($this->Session->read('current_team_id')): ?>
                            <li><a href="#" data-toggle="modal" data-target="#modal_add_circle"><?=
                                    __d('gl',
                                        "サークルを作成") ?></a>
                            </li>
                        <? endif; ?>
                        <li><?=
                            $this->Html->link(__d('gl', "チームを作成"),
                                              ['controller' => 'teams', 'action' => 'add']) ?></li>
                    </ul>
                </div>
                <a href="#"><i class="fa fa-envelope-o"></i></a>
                <a href="#"><i class="fa fa-bell-o"></i></a>

                <div class="dropdown gl-navbar-nav-fix">
                    <a href="#" class="dropdown-toggle me-menu-image" data-toggle="dropdown" href="#" id="download">
                        <?=
                        $this->Upload->uploadImage($this->Session->read('Auth'), 'User.photo', ['style' => 'small'],
                                                   ['width' => '26px', 'height' => '26px', 'class' => 'img-circle']) ?>
                        <i class="fa fa-caret-down"></i></a>
                    <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="download">
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
                <li><a href="<?= $this->Html->url('/') ?>"><i class="fa fa-home"></i>&nbsp;
                        <span class="visible-xs-inline"><?= __d('gl', "ホーム") ?></span>
                    </a>
                </li>
                <li><a href="#"><i class="fa fa-bullseye"></i>&nbsp;
                        <span class="visible-xs-inline"><?= __d('gl', "ゴール") ?></span>
                    </a></li>
                <li><a href="#"><i class="fa fa-users"></i>&nbsp;
                        <span class="visible-xs-inline"><?= __d('gl', "チーム") ?></span>
                    </a>
                </li>
                <li>
                    <form class="gl-nav-form-group" role="search">
                        <i class="fa fa-search"></i>
                        <input type="text" class="form-control gl-nav-search" placeholder="Search">
                    </form>
                </li>
            </ul>
        </div>
        <!--/.nav-collapse -->
    </div>
</div>