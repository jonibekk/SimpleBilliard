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
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="offcanvas" data-target=".navbar-offcanvas"
                    data-canvas="body">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <!--            <ul class="nav navbar-nav">-->
            <!--                <li class="dropdown">-->
            <!--                    <a href="#" class="dropdown-toggle gl-me-menu-image" data-toggle="dropdown" href="#" id="download">-->
            <!--                        --><? //=
            //                        $this->Upload->uploadImage($this->Session->read('Auth'), 'User.photo', ['style' => 'medium'],
            //                                                   ['width' => '40px', 'height' => '40px', 'class' => 'img-circle'])
            ?>
            <!--                        <span class="caret"></span></a>-->
            <!--                    <ul class="dropdown-menu" aria-labelledby="download">-->
            <!--                        <li><a href="#" data-toggle="modal" data-target="#modal_tutorial">-->
            <? //= __d('gl',
            //                                                                                                  "チュートリアル")
            ?><!--</a></li>-->
            <!--                        <li>--><? //= $this->Html->link(__d('gl', "ログアウト"),
            //                                                  ['controller' => 'users', 'action' => 'logout'])
            ?><!--</li>-->
            <!--                        <li class="divider"></li>-->
            <!--                        <li>--><? //=
            //                            $this->Html->link(__d('home', 'Blog'), 'http://blog.goalous.com/',
            //                                              ['target' => '_blank'])
            ?><!--</li>-->
            <!--                    </ul>-->
            <!--                </li>-->
            <!--            </ul>-->
            <a class="navbar-brand" href="#"><i class="fa fa-plus-circle"></i></a>
            <a class="navbar-brand" href="#"><i class="fa fa-envelope-o"></i></a>
            <a class="navbar-brand" href="#"><i class="fa fa-bell-o"></i></a>

        </div>
        <div class="navbar-offcanvas offcanvas " style="">
            <a class="navmenu-brand" href="#">Project name</a>
            <ul class="nav navbar-nav">
                <li><a href="#"><i class="fa fa-home"></i></a></li>
                <li><a href="#"><i class="fa fa-bullseye"></i> </a></li>
                <li><a href="#"><i class="fa fa-users"></i> </a></li>
            </ul>
        </div>
        <!--/.nav-collapse -->
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