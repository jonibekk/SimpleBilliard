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
<?= $this->App->viewStartComment()?>
<?
if (!isset($top_lang)) {
    $top_lang = null;
}
?>
<header id="header" class="header">
    <div class="container">
        <a class="logo-title" href="<?= $this->Html->url($top_lang ? '/' . $top_lang . "/" : '/'); ?>">
            <h1 class="logo pull-left">
                <?= $this->Html->image('homepage/Goalous_logo.png', array('alt' => 'Goalous', 'height' => '40')); ?>
            </h1>
        </a>
        <nav id="main-nav" class="main-nav navbar-right" role="navigation">
            <div class="navbar-header">
                <!-- モバイルサイズのハンバーガーメニュー -->
                <button class="navbar-toggle" type="button" data-toggle="collapse" data-target="#navbar-collapse">
                    <span class="sr-only"><?= __('Toggle navigation') ?></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button><!-- //navbar-collapse -->
            </div><!-- //navbar-header -->
            <div id="navbar-collapse" class="navbar-collapse collapse">
                <ul class="nav navbar-nav">
                    <li class="nav-item" id="h-nav-features">
                        <?= $this->Html->link(__('Features'), [
                            'controller' => 'pages',
                            'action'     => 'display',
                            'pagename'   => 'features',
                            'lang'       => $top_lang,
                        ]); ?>
                    </li>
                    <li class="nav-item" id="h-nav-pricing">
                        <?= $this->Html->link(__('Pricing'), [
                            'controller' => 'pages',
                            'action'     => 'display',
                            'pagename'   => 'pricing',
                            'lang'       => $top_lang,
                        ]); ?>
                    </li>
                    <li class="nav-item" id="h-nav-blog">
                        <?= $this->Html->link(__('Blog'), 'http://blog.isao.co.jp/',
                            array('target' => '_blank')); ?>
                    </li>
                    <li class="nav-item" id="h-nav-contact">
                        <?= $this->Html->link(__('Contact us'),
                            ['controller' => 'pages', 'action' => 'contact', 'lang' => $top_lang]); ?>
                    </li>
                    <li class="nav-item" id="h-nav-login">
                        <?= $this->Html->link(__('Login'), array('controller' => 'users', 'action' => 'login'),
                            [
                                "class" => "btn btn-link",
                            ]); ?>
                    </li>
                    <li class="nav-item nav-item-cta last" id="h-nav-signup">
                        <?= $this->Html->link(__('Create New Team'),
                            array('controller' => 'signup', 'action' => 'email', '?' => ['type' => 'header']),
                            array(
                                'class' => 'header-signup btn btn-cta btn-cta-primary',
                                'id'    => 'RegisterLinkHeader'
                            )); ?>
                    </li>
                </ul> <!-- //nav -->
            </div> <!-- //navbar-collapse -->
        </nav> <!-- //main-nav -->
    </div> <!-- //container -->
</header> <!-- //header -->
<?= $this->App->viewEndComment()?>
