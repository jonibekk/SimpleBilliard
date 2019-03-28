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
<?= $this->App->viewStartComment()?>
<!DOCTYPE html>
<!--[if IE 8]>
<html xmlns="http://www.w3.org/1999/xhtml" lang="<?= $this->Lang->getLangCode() ?>" class="ie8"> <![endif]-->
<!--[if IE 9]>
<html xmlns="http://www.w3.org/1999/xhtml" lang="<?= $this->Lang->getLangCode() ?>" class="ie9"> <![endif]-->
<!--[if !IE]><!-->
<html lang="<?= $this->Lang->getLangCode() ?>">
<!--<![endif]-->
<head>
    <?= $this->Html->charset(); ?>
    <?php
    echo $this->Html->meta('icon');
    echo $this->Html->meta([
        'name'    => 'viewport',
        'content' => "width=device-width, initial-scale=1, maximum-scale=1"
    ]);
    echo $this->fetch('meta');
    ?>

    <!-- START CSS -->
    <link href='https://fonts.googleapis.com/css?family=Lato:300,400,300italic,400italic' rel='stylesheet'
          type='text/css'>
    <link href='https://fonts.googleapis.com/css?family=Montserrat:400,700' rel='stylesheet' type='text/css'>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <?php
    echo $this->Html->css('font-awesome.min');
    echo $this->Html->css('homepage/bootstrap.min');
    echo $this->Html->css('homepage/pe-icon-7-stroke');
    echo $this->Html->css('homepage/animate.min');
    echo $this->Html->css('homepage/flexslider');
    echo $this->Html->css('homepage/style.min');
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
        echo $this->element('intercom');
    }
    ?>
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body class="home-page">
<?= $this->element('google_tag_manager', ['page_type' => 'lp']) ?>

<!-- * Facebook Like button script starts -->
<div id="fb-root"></div>
<script>
    (function (d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s);
        js.id = id;
        js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.0";
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));
</script>
<!-- * Facebook Like button script ends -->


<div class="wrapper">

    <!-- ******HEADER****** -->
    <?php echo $this->element('Header/home'); ?>

    <?= $this->element('alert_maint') ?>
    <?= $this->Session->flash(); ?>


    <?= $this->fetch('content') ?>
</div>
<!-- ******FOOTER****** -->
<?php echo $this->element('footer_not_logged_in'); ?>
<!--//footer-->
<?= $this->element('common_homepage_js') ?>
<?= $this->fetch('script') ?>
<?= $this->fetch('modal') ?>
<?= $this->Session->flash('noty') ?>

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
<?= $this->App->viewEndComment()?>
