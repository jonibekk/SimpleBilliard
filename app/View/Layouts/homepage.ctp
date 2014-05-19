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
                        $this->Html
                            ->url(
                            array(
                                'controller' => 'users',
                                'action'     => 'login'
                            ))
                        ?>"><i
                        class="icon-signin"></i></a> <a class="brand"
                                                        href="<?= $this->Html->url('/') ?>"><span><i
                            class="icon-stop"></i></span> <?= $title_for_layout ?></a>

                <div id="top-navigation"
                     class="nav-collapse subnav-collapse collapse ">
                    <ul class="nav pull-right">
                        <li class="active"><?= $this->Html->link(__d('home', 'Home'), '/') ?></li>
                        <li class=""><?=
                            $this->Html
                                ->link(__d('home', 'Features'),
                                       array(
                                           'controller' => 'pages',
                                           'action'     => 'features'
                                       ))
                            ?></li>
                        <li class=""><?=
                            $this->Html->link(__d('home', 'Blog'),
                                              'http://blog.goalous.com/') ?></li>
                        <li class="top-account visible-desktop"><?=
                            $this->Html
                                ->link(__d('home', 'Sign UP'),
                                       array(
                                           'controller' => 'users',
                                           'action'     => 'add'
                                       ),
                                       array(
                                           'class' => 'top-sign-up'
                                       ))
                            ?>
                        </li>
                        <li class="top-account visible-desktop"><?=
                            $this->Html
                                ->link(__d('home', 'Sign IN'),
                                       array(
                                           'controller' => 'users',
                                           'action'     => 'login'
                                       ),
                                       array(
                                           'class' => 'top-sign-in'
                                       ))
                            ?>
                        </li>
                        <li class="dropdown"><a data-toggle="dropdown"
                                                class="dropdown-toggle" href="#"><?= __d('global', 'Language') ?>
                                <strong
                                    class="caret"></strong></a>
                            <ul class="dropdown-menu">
                                <? /** @var $lang_list */
                                foreach ($lang_list as $key => $val) : ?>
                                    <li><?
                                        echo $this->Html->link($val, "/?change_lang=$key");
                                        ?></li>
                                <? endforeach; ?>
                            </ul>
                    </ul>
                </div>

            </div>
        </div>
    </div>
    <!--end: navbar --> </header>
<?= $this->element('mainte_alert') ?>

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
                            $this->Html
                                ->link(__d('home', 'Features'),
                                       array(
                                           'controller' => 'pages',
                                           'action'     => 'features'
                                       ))
                            ?></li>
                        <li class=""><?=
                            $this->Html->link(__d('home', 'Blog'),
                                              'http://blog.goalous.com/') ?></li>
                        <li class=""><?=
                            $this->Html
                                ->link(__d('usr', 'Privacy Policy'),
                                       array(
                                           'controller' => 'pages',
                                           'action'     => 'pp'
                                       ))
                            ?></li>
                        <li class=""><?=
                            $this->Html
                                ->link(__d('usr', 'Terms of Service'),
                                       array(
                                           'controller' => 'pages',
                                           'action'     => 'tos'
                                       ))
                            ?></li>
                        <li><?=
                            $this->Html
                                ->link(__d('home', 'Sign IN'),
                                       array(
                                           'controller' => 'users',
                                           'action'     => 'login'
                                       ))
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

<!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
<!--[if lt IE 9]>
<script src="https://html5shim.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
<?
//環境を識別できるようにリボンを表示
switch (ENV_NAME) {
    case 'stg':
        echo $this->Html->script('http://quickribbon.com/ribbon/2014/04/c966588e9495aa7b205aeaaf849d674f.js');
        break;
    case 'local':
        echo $this->Html->script('http://quickribbon.com/ribbon/2014/04/b13dfc8e5d887b8725f256c31cc1dff4.js');
        break;
    default:
        break;
}
?>
</body>
</html>