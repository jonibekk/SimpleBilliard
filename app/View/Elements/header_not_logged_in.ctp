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
<?= $this->Html->css('homepage/styles'); ?>
<header id="header" class="header">
    <div class="container">
        <h1 class="logo pull-left">
            <a href="<?= $this->Html->url('/'); ?>">
                <div><?= __d('lp', '最強にオープンな社内SNS') ?></div>
                <div class="logo-title">
                    <?= $this->Html->image('homepage/Goalous_logo.png', array('alt'=>'Goalous', 'width'=>'115')); ?>
                </div>
            </a>
        </h1>
        <nav id="main-nav" class="main-nav navbar-right" role="navigation">
            <div class="navbar-header">
                <!-- モバイルサイズのハンバーガーメニュー -->
                <button class="navbar-toggle" type="button" data-toggle="collapse" data-target="#navbar-collapse">
                    <span class="sr-only"><?= __d('lp', 'Toggle navigation') ?></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button><!-- //navbar-collapse -->
            </div><!-- //navbar-header -->
            <div id="navbar-collapse" class="navbar-collapse collapse">
                <ul class="nav navbar-nav">
                    <li class="nav-item">
                        <?=$this->Html->link( __d('lp', '機能'), array('controller' => 'features',));?>
                    </li>
                    <li class="nav-item">
                        <?=$this->Html->link( __d('lp', '料金'), array('controller' => 'pricing',));?>
                    </li>
                    <li class="nav-item">
                        <?=$this->Html->link( __d('lp', 'ブログ'), 'http://blog.isao.co.jp/tag/goalous/', array('target' => '_blank'));?>
                    </li>
                    <li class="nav-item">
                        <?=$this->Html->link( __d('lp', 'お問い合わせ'), array('controller' => 'contact'));?>
                    </li>
                    <li class="nav-item">
                        <button type="button" class="login-trigger btn btn-link" data-toggle="modal" data-target="#login-modal">
                            <?=$this->Html->link( __d('lp', 'ログイン'), array('controller' => 'users', 'action' => 'login'));?>
                        </button>
                    </li>
                    <li class="nav-item nav-item-cta last">
                        <button type="button" class="btn btn-cta btn-cta-primary" data-toggle="modal" data-target="#signup-modal" >
                            <?=$this->Html->link( __d('lp', '新規登録'), array('controller' => 'users', 'action' => 'register'), array('class' => 'shine'));?>
                        </button>
                    </li>
                </ul> <!-- //nav -->
            </div> <!-- //navbar-collapse -->
        </nav> <!-- //main-nav -->
    </div> <!-- //container -->
</header> <!-- //header -->
<!-- END app/View/Elements/header_not_logged_in.ctp -->
