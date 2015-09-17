<?php /**
 * PHP 5
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Layouts
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @var                    $title_for_layout
 * @var                    $this View
 * @var                    $top_lang
 * @var CodeCompletionView $this
 */
?>
<!-- START app/View/Layouts/homepage.ctp -->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<!--[if IE 8]>
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" class="ie8"> <![endif]-->
<!--[if IE 9]>
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!-->
<html xmlns="http://www.w3.org/1999/xhtml" lang="ja"> <!--<![endif]-->
<head>
    <?= $this->Html->charset(); ?>
    <title><?=
        $title_for_layout ?></title>
    <?php echo $this->Html->meta('icon');
    echo $this->Html
        ->meta(
            array(
                'name'    => 'viewport',
                'content' => "width=device-width, initial-scale=1, maximum-scale=1"
            ));
    echo $this->fetch('meta');
    echo $this->fetch('ogp');
    ?>

    <!-- START CSS -->
    <link href='http://fonts.googleapis.com/css?family=Lato:300,400,300italic,400italic' rel='stylesheet'
          type='text/css'>
    <link href='http://fonts.googleapis.com/css?family=Montserrat:400,700' rel='stylesheet' type='text/css'>
    <?php
    echo $this->Html->css('font-awesome.min');
    echo $this->Html->css('homepage/bootstrap.min');
    echo $this->Html->css('pnotify.custom.min');
    echo $this->Html->css('homepage/pe-icon-7-stroke');
    echo $this->Html->css('homepage/animate.min');
    echo $this->Html->css('homepage/flexslider');
    echo $this->Html->css('homepage/styles');
    echo $this->fetch('css');
    ?>
    <!-- END CSS -->
    <link rel="apple-touch-icon"
          href="<?= $this->Html->url('/img/apple-touch-icon.png') ?>"/>
    <link rel="apple-touch-icon-precomposed"
          href="<?= $this->Html->url('/img/apple-touch-icon.png') ?>"/>

    <?php //公開環境のみタグを有効化
    if (PUBLIC_ENV) {
        /** @noinspection PhpDeprecationInspection */
        echo $this->element('external_service_tags');
    }
    ?>
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body class="home-page">
<?= $this->element('google_tag_manager') ?>

<!-- * Facebook Like button script starts -->
<div id="fb-root"></div>
<script>(function (d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s);
        js.id = id;
        js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.0";
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));</script>
<!-- * Facebook Like button script ends -->

<div class="wrapper">
    <!-- ******HEADER****** -->
    <header id="header" class="header">
        <div class="container">
            <h1 class="logo pull-left">
                <a href="<?= $this->Html->url('/') ?>">
                    <span class="logo-title">
                        <?= $this->Html->image('homepage/logo.png', ['alt' => 'Goalous']) ?>
                        <?= $this->Html->image('homepage/goalous.png', ['alt' => 'Goalous']) ?>
                        </span>
                </a>
            </h1><!--//logo-->
            <nav id="main-nav" class="main-nav navbar-right" role="navigation">
                <div class="navbar-header">
                    <button class="navbar-toggle" type="button" data-toggle="collapse" data-target="#navbar-collapse">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <!--//nav-toggle-->
                </div>
                <!--//navbar-header-->
                <div class="navbar-collapse collapse" id="navbar-collapse">
                    <ul class="nav navbar-nav">
                        <li class="nav-item">
                            <a href="<?= $this->Html->url(['controller' => 'users', 'action' => 'login']) ?>"
                               class=""><?= __d('gl', 'ログイン') ?></a>
                        </li>
                        <li class="nav-item dropdown last">
                            <a class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-delay="0"
                               data-close-others="false" href="#"><?= __d('gl', 'Language') ?> <i
                                    class="fa fa-angle-down"></i></a>
                            <ul class="dropdown-menu">
                                <?php /** @var $lang_list */
                                foreach ($lang_list as $key => $val) : ?>
                                    <li><?= $this->Html->link($val, '/' . $key . '/'); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </li>
                    </ul>
                    <!--//nav-->
                </div>
                <!--//navabr-collapse-->
            </nav>
            <!--//main-nav-->
        </div>
        <!--//container-->
    </header>
    <!--//header-->
    <?= $this->element('alert_maint') ?>
    <?= $this->Session->flash(); ?>
    <?= $this->fetch('content') ?>
</div>
<!-- ******FOOTER****** -->
<footer class="footer">
    <div class="footer-content">
        <div class="container">
            <div class="row">
                <div class="footer-col col-md-5 col-sm-7 col-sm-12 about">
                    <div class="footer-col-inner">
                        <h3 class="title"><?= __d('lp', 'About Us') ?></h3>

                        <p><?= __d('lp', 'Goalous Team') ?></p>
                    </div>
                    <!--//footer-col-inner-->
                </div>
                <!--//foooter-col-->
                <div class="footer-col col-md-3 col-sm-4 col-md-offset-1 links">
                    <div class="footer-col-inner">
                        <h3 class="title"><?= __d('lp', 'Other Links') ?></h3>
                        <ul class="list-unstyled">
                            <li><a href='#modal-tos' data-toggle='modal'><i class="fa fa-caret-right"></i><?= __d('gl',
                                                                                                                  '利用規約') ?>
                                </a></li>
                            <li><a href='#modal-pp' data-toggle='modal'><i class="fa fa-caret-right"></i><?= __d('gl',
                                                                                                                 'プライバシーポリシー') ?>
                                </a></li>
                        </ul>
                    </div>
                    <!--//footer-col-inner-->
                </div>
                <!--//foooter-col-->
                <div class="footer-col col-md-3 col-sm-12 contact">
                    <div class="footer-col-inner">
                        <h3 class="title"><?= __d('lp', 'Get in touch') ?></h3>

                        <div class="row">
                            <p class="email col-md-12 col-sm-4"><i class="fa fa-envelope"></i><a
                                    href="mailto:co-jp-form@isao.net?subject=<?= __d('lp', 'Goalousへのお問い合わせ') ?>">co-jp-form@isao.net</a>
                            </p>
                        </div>
                    </div>
                    <!--//footer-col-inner-->
                </div>
                <!--//foooter-col-->
            </div>
            <!--//row-->
        </div>
        <!--//container-->
    </div>
    <!--//footer-content-->
    <div class="bottom-bar">
        <div class="container">
            <div class="row">
                <small class="copyright col-md-6 col-sm-6 col-xs-12">Copyright© ISAO CORPORATION. All rights
                    reserved.
                </small>
                <ul class="social col-md-6 col-sm-6 col-xs-12 list-inline">
                    <li><a href="https://plus.google.com/+isaocojp/posts"><i class="fa fa-google"></i></a></li>
                    <li>
                        <a href="https://www.linkedin.com/company/1806929?trk=vsrp_companies_res_name&trkInfo=VSRPsearchId%3A3688113161424846250341%2CVSRPtargetId%3A1806929%2CVSRPcmpt%3Aprimary"><i
                                class="fa fa-linkedin"></i></a></li>
                    <li class="last"><a href="https://www.youtube.com/user/isaocorp"><i
                                class="fa fa-youtube"></i></a></li>
                    <li><a href="https://www.facebook.com/isao.jp"><i class="fa fa-facebook"></i></a></li>
                </ul>
                <!--//social-->
            </div>
            <!--//row-->
        </div>
        <!--//container-->
    </div>
    <!--//bottom-bar-->
</footer>
<!--//footer-->
<?= $this->element('common_homepage_js') ?>
<?= $this->fetch('script') ?>
<?php
$this->append('modal');
echo $this->element('modal_tos');
echo $this->element('modal_privacy_policy');
$this->end();
?>
<?= $this->fetch('modal') ?>
<?= $this->Session->flash('pnotify') ?>

<!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
<!--[if lt IE 9]>
<script src="https://html5shim.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
<!--NewRelic Footer-->
<?php if (extension_loaded('newrelic')) {
    /** @noinspection PhpUndefinedFunctionInspection */
    echo newrelic_get_browser_timing_footer();
} ?>
<!--/NewRelic Footer-->
</body>
</html>
<!-- END app/View/Layouts/homepage.ctp -->
