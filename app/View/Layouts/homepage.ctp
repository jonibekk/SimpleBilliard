<?
/**
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
 * @var $title_for_layout
 * @var $this View
 * @var $top_lang
 */
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
    <?= $this->Html->charset(); ?>
    <title><?=
        $title_for_layout ?></title>
    <?
    echo $this->Html->meta('icon');
    echo $this->Html
        ->meta(
        array(
            'name'    => 'viewport',
            'content' => "width=device-width, initial-scale=1, maximum-scale=1"
        ));
    echo $this->fetch('meta');
    echo $this->Html->css('homepage/bootstrap');
    echo $this->Html->css('homepage/bootstrap-responsive');
    echo $this->Html->css('font-awesome.min');
    echo $this->Html->css('pnotify.custom.min');
    echo $this->Html->css('homepage/style');
    echo $this->Html->css('homepage/gl_home');
    echo $this->fetch('css');
    echo $this->fetch('ogp');
    ?>
    <link rel="apple-touch-icon"
          href="<?= $this->Html->url('/img/apple-touch-icon.png') ?>"/>
    <link rel="apple-touch-icon-precomposed"
          href="<?= $this->Html->url('/img/apple-touch-icon.png') ?>"/>
    <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if IE 9]>
    <style>.img-mask .flipper {
        display: none;
    }</style>
    <![endif]-->
    <!--[if IE 8]>
    <style>.boxy {
        position: relative;
        width: 150px;
        height: 150px;
    }

    .flipper {
        overflow: hidden;
    }

    .subscribe input[type="text"], .subscribe textarea {
        width: 35%;
    }

    .plan-background {
        background-position: center;
        background-repeat: repeat-x;
        border: none;
    }

    .sign-background {
        background-position: center;
        background-repeat: repeat-x;
        border: none;
    }

    .countdown-background {
        background-position: center;
        background-repeat: repeat-x;
        border: none;
    }

    .page-title {
        background-position: center;
        background-repeat: repeat-x;
        border: none;
    }

    .img-mask .flipper {
        display: none;
    }</style>
    <![endif]-->
    <?
    //公開環境のみタグを有効化
    if (PUBLIC_ENV) {
        /** @noinspection PhpDeprecationInspection */
        echo $this->element('external_service_tags');
    }
    ?>

</head>
<body>
<header id="header">

    <!--NewRelic Header-->
    <? if (extension_loaded('newrelic')) {
        /** @noinspection PhpUndefinedFunctionInspection */
        echo newrelic_get_browser_timing_header();
    } ?>
    <!--/NewRelic Header-->

    <!--start: navbar -->
    <div class="navbar navbar-fixed-top">
        <div class="navbar-inner">
            <div class="container">
                <a data-toggle="collapse" data-target=".nav-collapse"
                   class="btn btn-navbar"> <span><i class="icon-reorder"></i></span>
                </a> <a class="btn btn-navbar"
                        href="<?=
                        $this->Html->url(
                                   [
                                       'controller' => 'users',
                                       'action'     => 'login'
                                   ])
                        ?>"><i
                        class="icon-signin"></i></a> <a class="brand"
                                                        href="<?= ($top_lang) ? $this->Html->url('/' . $top_lang . "/") : $this->Html->url('/') ?>"><span><i
                            class="icon-stop"></i></span> <?= $title_for_layout ?></a>

                <div id="top-navigation"
                     class="nav-collapse subnav-collapse collapse ">
                    <ul class="nav pull-right">
                        <li class="active"><?=
                            $this->Html->link(__d('home', 'Home'),
                                              $top_lang ? '/' . $top_lang . "/" : '/') ?></li>
                        <li class=""><?=
                            $this->Html->link(__d('home', 'Features'),
                                              [
                                                  'controller' => 'pages',
                                                  'action'     => 'display',
                                                  'pagename'   => 'features',
                                                  'lang'       => $top_lang,
                                              ])
                            ?></li>
                        <li class=""><?= $this->Html->link(__d('home', 'Blog'), 'http://blog.goalous.com/') ?></li>
                        <li class="top-account visible-desktop">
                            <?=
                            $this->Html->link(__d('home', 'Sign UP'),
                                              [
                                                  'controller' => 'users',
                                                  'action' => 'register'
                                              ],
                                              [
                                                  'class' => 'top-sign-up'
                                              ])
                            ?>
                        </li>
                        <li class="top-account visible-desktop">
                            <?=
                            $this->Html
                                ->link(__d('home', 'Sign IN'),
                                       [
                                           'controller' => 'users',
                                           'action'     => 'login'
                                       ],
                                       [
                                           'class' => 'top-sign-in'
                                       ])
                            ?>
                        </li>
                        <li class="dropdown"><a data-toggle="dropdown"
                                                class="dropdown-toggle" href="#"><?= __d('gl', 'Language') ?>
                                <strong
                                    class="caret"></strong></a>
                            <ul class="dropdown-menu">
                                <? /** @var $lang_list */
                                foreach ($lang_list as $key => $val) : ?>
                                    <li><?= $this->Html->link($val, '/' . $key . '/'); ?></li>
                                <? endforeach; ?>
                            </ul>
                    </ul>
                </div>

            </div>
        </div>
    </div>
    <!--end: navbar --> </header>
<?= $this->element('mainte_alert') ?>
<?= $this->Session->flash(); ?>
<?= $this->fetch('content') ?>
<!-- start: footer -->
<footer>
    <div class="pre-footer">
        <div class="container">
            <div class="row-fluid"></div>
        </div>
    </div>
    <div class="post-footer">
        <div class="container">
            <div class="row">
                <div class="span6">
                    <div class="copyright">&copy; Goalous</div>
                </div>
                <div class="span6">
                    <ul class="footer-links">
                        <li class=""><?=
                            $this->Html->link(__d('home', 'Features'),
                                              [
                                                  'controller' => 'pages',
                                                  'action'     => 'display',
                                                  'pagename'   => 'features',
                                                  'lang'       => $top_lang,
                                              ])
                            ?></li>
                        <li class=""><?=
                            $this->Html->link(__d('home', 'Blog'),
                                              'http://blog.goalous.com/') ?></li>
                        <li class=""><?=
                            $this->Html->link(__d('gl', 'Privacy Policy'),
                                              [
                                                  'controller' => 'pages',
                                                  'action'     => 'display',
                                                  'pagename'   => 'pp',
                                                  'lang'       => $top_lang,
                                              ])
                            ?></li>
                        <li class=""><?=
                            $this->Html->link(__d('gl', 'Terms of Service'),
                                              [
                                                  'controller' => 'pages',
                                                  'action'     => 'display',
                                                  'pagename'   => 'tos',
                                                  'lang'       => $top_lang,
                                              ])
                            ?></li>
                        <li><?=
                            $this->Html->link(__d('home', 'Sign IN'),
                                              [
                                                  'controller' => 'users',
                                                  'action'     => 'login'
                                              ])
                            ?>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <!--NewRelic Footer-->
    <? if (extension_loaded('newrelic')) {
        /** @noinspection PhpUndefinedFunctionInspection */
        echo newrelic_get_browser_timing_footer();
    } ?>
    <!--/NewRelic Footer-->

</footer>
<!-- end: footer -->

<?= $this->element('common_homepage_js') ?>
<?= $this->fetch('script') ?>
<?= $this->Session->flash('pnotify') ?>

<!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
<!--[if lt IE 9]>
<script src="https://html5shim.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
</body>
</html>