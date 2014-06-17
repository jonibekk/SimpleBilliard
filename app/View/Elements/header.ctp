<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 5/28/14
 * Time: 5:04 PM
 *
 * @var $title_for_layout string
 * @var $this             View
 * @var $nav_disable
 */
?>
<? if (!$nav_disable): ?>
    <div class="navbar navbar-fixed-top navbar-default" style="">
        <div class="container">
            <div class="navbar-header navbar-right">
                <button type="button" class="navbar-toggle gl-hamburger" data-toggle="offcanvas"
                        data-target=".navbar-offcanvas"
                        data-canvas="body">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="gl-navbar-icon" href="#"><i class="fa fa-plus-circle"></i></a>
                <a class="gl-navbar-icon" href="#"><i class="fa fa-envelope-o"></i></a>
                <a class="gl-navbar-icon" href="#"><i class="fa fa-bell-o"></i></a>

                <div class="dropdown gl-navbar-nav-fix">
                    <a href="#" class="dropdown-toggle me-menu-image" data-toggle="dropdown" href="#" id="download">
                        <?=
                        $this->Upload->uploadImage($this->Session->read('Auth'), 'User.photo', ['style' => 'small'],
                                                   ['width' => '12px', 'height' => '12px', 'class' => 'img-circle']) ?>
                        <span class="caret"></span></a>
                    <ul class="dropdown-menu" aria-labelledby="download">
                        <li><a href="#" data-toggle="modal" data-target="#modal_tutorial"><?= __d('gl',
                                                                                                  "チュートリアル") ?></a></li>
                        <li><?= $this->Html->link(__d('gl', "ログアウト"),
                                                  ['controller' => 'users', 'action' => 'logout']) ?></li>
                        <li class="divider"></li>
                        <li><?=
                            $this->Html->link(__d('home', 'Blog'), 'http://blog.goalous.com/',
                                              ['target' => '_blank']) ?></li>
                    </ul>
                </div>

            </div>
            <div class="navbar-offcanvas offcanvas navmenu-fixed-left">
                <a class="navmenu-brand" href="#"><?= $title_for_layout ?></a>
                <ul class="nav navbar-nav">
                    <li>
                        <form class="navbar-form navbar-left" role="search">
                            <div class="form-group">
                                <!--suppress HtmlFormInputWithoutLabel -->
                                <select class="form-control">
                                    <option>TeamISAO</option>
                                    <option>TeamGoalous</option>
                                </select>
                            </div>
                        </form>
                    </li>
                    <li><a href="#"><i class="fa fa-home"></i>&nbsp;
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
                        <form class="navbar-form navbar-left" role="search">
                            <div class="form-group left-inner-addon">
                                <i class="fa fa-search"></i>
                                <input type="text" class="form-control" placeholder="Search">
                            </div>
                        </form>
                    </li>
                </ul>
            </div>
            <!--/.nav-collapse -->
        </div>
    </div>
<? else: ?>
    <div class="navbar navbar-default navbar-fixed-top">
        <div class="container">
            <div class="navbar-header">
                <? if ($nav_disable): ?>
                    <a href="/" class="navbar-brand"><?= $title_for_layout ?></a>
                <? endif; ?>
                <? if (!$nav_disable) : ?>
                    <button class="navbar-toggle collapsed" type="button" data-toggle="collapse"
                            data-target="#navbar-main">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                <? endif ?>
            </div>
            <?
            if (!$nav_disable) {
                echo $this->element('navbar');
            }
            ?>
        </div>
    </div>
<?endif; ?>