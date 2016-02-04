<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 5/28/14
 * Time: 5:04 PM
 *
 * @var CodeCompletionView $this
 * @var                    $title_for_layout string
 * @var                    $this             View
 * @var                    $nav_disable
 */
?>
<!-- START app/View/Elements/header_not_logged_in.ctp -->
<header id="header" class="header">
    <div class="container">
        <h1 class="logo pull-left">
            <?php
                // ロゴの表示保留中
                // echo($this->Html->link($this->Html->image('logo_on.png')));
            ?>
            <div><?= __d('lp', '最強にオープンな社内SNS') ?></div>
            <div class="logo-title">
                <?php
                    // echo($this->Html->link($this->Html->image('')));
                 ?>
            </div>
        </h1>
        <nav id="main-nav" class="main-nav navbar-right" role="navigation">
            <div class="navbar-header">
                <!-- モバイルサイズのハンバーガーメニュー -->
                <button class="navbar-toggle" type="button" data-toggle="collapse" data-target="#navbar-collapse">
                    <span class="sr-only"><?= __d('lp', '') ?>Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button><!-- //navbar-collapse -->
            </div><!-- //navbar-header -->
            <div id="navbar-collapse" class="navbar-collapse collapse">
                <ul class="nav navbar-nav">
                    <li class="nav-item">for文で回すのがよい？</li>
                </ul> <!-- //nav -->
            </div> <!-- //navbar-collapse -->
        </nav> <!-- //main-nav -->
    </div> <!-- //container -->
</header> <!-- //header -->

<div class="navbar navbar-default navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <a href="/" class="navbar-brand"><?= $title_for_layout ?></a>
        </div>
    </div>
</div>
<!-- END app/View/Elements/header_not_logged_in.ctp -->
