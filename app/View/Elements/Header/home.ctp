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
<?= $this->App->viewStartComment() ?>
<?php
if (!isset($top_lang)) {
    $top_lang = null;
}
$backBtn = isset($_GET['backBtn']);

?>
<header id="header" class="header">
    <div class="container">
        <?php if ($backBtn && $is_mb_app): ?>
            <?= $this->element('Header/back_btn'); ?><a class="back-btn-text" href="#" onclick="window.history.back();">Back</a>
        <?php else: ?>
        <a class="logo-title" href="<?= $this->Html->url($top_lang ? '/' . $top_lang . "/" : '/'); ?>">
                <h1 class="logo pull-left">
                    <?= $this->Html->image('homepage/Goalous_logo.png', array('alt' => 'Goalous', 'height' => '40')); ?>
                </h1>
            </a>
            <nav id="main-nav" class="main-nav navbar-right" role="navigation">
                <div class="navbar-header">
                    <!-- モバイルサイズのハンバーガーメニュー -->
                    <button class="navbar-toggle mobile-hamburger-button" type="button" data-toggle="collapse" data-target="#navbar-collapse">
                        <span class="sr-only"><?= __('Toggle navigation') ?></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button><!-- //navbar-collapse -->
                </div><!-- //navbar-header -->
                <div id="navbar-collapse" class="navbar-collapse collapse">

                  <div class="header_top">
                  <ul id="navigationWrapperHome" class="nav navbar-nav">
                    <li class="nav-item" id="h-nav-case_study">
                      <a href="/#app"><?=__('アプリダウンロード')?></a>
                    </li>

                    <li class="nav-item hide-on-mobile" id="h-nav-case_study">
                      <a href="/#faq"><?=__('よくある質問')?></a>
                    </li>

                    <?php if($isLoggedIn): ?>
                      <li class="nav-item" id="h-nav-contact">
                          <?= $this->Html->link(__('Contact us'),
                              ['controller' => 'pages', 'action' => 'contact', 'lang' => $top_lang]); ?>
                      </li>

                        <li class="nav-item nav-item-cta last" id="h-nav-signup">
                            <?= $this->Html->link(__('Go Your Team'),
                                array('controller' => 'pages', 'action' => 'home'),
                                array(
                                    'class' => 'header-signup btn btn-cta btn-cta-primary',
                                    'id'    => 'RegisterLinkHeader'
                                )); ?>
                        </li>
                    <?php else: ?>
                        <li class="nav-item hide-on-mobile" id="h-nav-login">
                            <?= $this->Html->link(__('Login'), array('controller' => 'users', 'action' => 'login')); ?>
                        </li>
                      </ul>
                    </div>


                    <div class="header_bottom">
                    <ul id="navigationWrapperHome" class="nav navbar-nav txt-bold">

                      <li class="nav-item" id="h-nav-case_study">
                          <?= $this->Html->link(__('導入事例'), [
                              'controller' => 'pages',
                              'action'     => 'lp',
                              'pagename'   => 'case_study',
                              'lang'       => $top_lang,
                          ]); ?>
                      </li>

                        <li class="nav-item" id="h-nav-features">
                            <?= $this->Html->link(__('Features'), [
                                'controller' => 'pages',
                                'action'     => 'lp',
                                'pagename'   => 'features',
                                'lang'       => $top_lang,
                            ]); ?>
                        </li>

                        <li class="nav-item" id="h-nav-pricing">
                            <?= $this->Html->link(__('Pricing'), [
                                'controller' => 'pages',
                                'action'     => 'lp',
                                'pagename'   => 'pricing',
                                'lang'       => $top_lang,
                            ]); ?>
                        </li>

                        <li class="nav-item" id="h-nav-blog">
                            <?= $this->Html->link(
                                __('Blog'),
                                ($this->Lang->getLangCode() == LangHelper::LANG_CODE_JP)
                                    ? "https://www.goalous.com/blog/ja/"
                                    : "https://www.goalous.com/blog/",
                                ['target' => '_blank']); ?>
                        </li>
                            <!--
                            <li class="nav-item nav-item-cta last" id="h-nav-signup">
                                <?= $this->Html->link(__('Create New Team'),
                                    ['controller' => 'pages', 'action' => 'contact', 'lang' => $top_lang]); ?>-->
                            </li>

                                <li class="nav-item" id="h-nav-contact">
                                    <?= $this->Html->link('<img src="/img/homepage/top/mail_icon_w.svg" class="header_icon_mail">'.__('Contact us'),
                                    array('controller' => 'pages',
                                    'action' => 'contact',
                                    '?' => ['type' => 'header']),
                                    array(
                                        'escape' => false,
                                        'class' => 'header-signup btn btn-cta btn-cta-primary header-contact-button',
                                        'id'    => 'RegisterLinkHeader'
                                    )); ?>
                            </li>
                        <?php endif; ?>
                    </ul> <!-- //nav -->
                </div> <!-- //navbar-collapse -->
            </nav> <!-- //main-nav -->
        <?php endif; ?>
    </div> <!-- //container -->
</header> <!-- //header -->
<?= $this->App->viewEndComment() ?>
