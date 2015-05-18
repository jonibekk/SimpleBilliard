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
 * @package       Cake.View.Pages
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @var $this View
 * @var $user_count
 * @var $top_lang
 */
?>
<!-- START app/View/Pages/home.ctp -->
<div class="section-wrapper">
    <div class="container">
        <?= $this->Session->flash('oauth_error'); ?>
        <div class="row">
            <div class="span6">
                <div class="separator">
                    <div class="left">
                        <div class="lines">
                            <div>
                                <h2 class="lines-title">
                                    <?= __d('home', "Let's start Goalous!") ?>
                                </h2>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="signup-email">
                    <?php $signup_link = $this->Html
                        ->link(__d('home', 'here'),
                               array(
                                   'controller' => 'users',
                                   'action'     => 'register'
                               ))
                    ?>
                    <?=
                    __d('home', 'You can also sign up with your email address. Click %s to start.',
                        $signup_link)
                    ?>

                </div>
            </div>
            <div class="span6">
                <div class="separator">
                    <div class="left">
                        <div class="lines">
                            <div>
                                <h2 class="lines-title"></h2>
                            </div>
                        </div>
                    </div>
                </div>
                <iframe width="100%" height="350"
                        src="https://www.youtube.com/embed/_-u73hdotYA" frameborder="0"
                        allowfullscreen></iframe>

            </div>
        </div>
    </div>
</div>
<!-- start: slider -->
<div class="slider-wrapper slider-wrapper">

    <div id="slider" class="sl-slider-wrapper">

        <div class="sl-slider">

            <div class="sl-slide bg-1" data-orientation="horizontal">
                <div class="sl-slide-inner">
                    <div class="deco">
                        <?= $this->Html->image('homepage/home-slider/mv1.png') ?>
                    </div>
                    <h2></h2>
                </div>
            </div>

            <div class="sl-slide bg-2" data-orientation="vertical">
                <div class="sl-slide-inner">
                    <div class="deco">
                        <?= $this->Html->image('homepage/home-slider/mv2.png') ?>
                    </div>
                    <h2></h2>
                </div>
            </div>

            <div class="sl-slide bg-3" data-orientation="horizontal">
                <div class="sl-slide-inner">
                    <div class="deco">
                        <?= $this->Html->image('homepage/home-slider/mv3.png') ?>
                    </div>
                    <h2></h2>
                </div>
            </div>

            <div class="sl-slide bg-4" data-orientation="vertical">
                <div class="sl-slide-inner">
                    <div class="deco">
                        <?= $this->Html->image('homepage/home-slider/mv4.png') ?>
                    </div>
                </div>
            </div>

        </div>
        <!-- /sl-slider -->

        <nav id="nav-arrows" class="nav-arrows">
            <span class="nav-arrow-prev">Previous</span> <span
                class="nav-arrow-next">Next</span>
        </nav>

        <nav id="nav-dots" class="nav-dots">
            <span class="nav-dot-current"></span> <span></span> <span></span> <span></span>
        </nav>

    </div>
    <!-- /slider-wrapper -->

</div>
<!-- end: slider -->

<!-- start: lastest-work -->
<div class="section-wrapper lastest-work">
    <div class="container">
        <div class="row">
            <div class="span12">
                <div class="separator">
                    <div class="left">
                        <div class="lines">
                            <div>
                                <h2 class="lines-title">
                                    <?= __d('home', 'Features') ?>
                                </h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="span3">
                <div class="white-content">
                    <div class="img-mask hover-mask">
                        <?= $this->Html->image('homepage/photos/feature-1.png') ?>
                        <div class="flip-container">
                            <div class="flipper">
                                <div class="back">
                                    <div class="flip-b">
                                        <a href="<?=
                                        $this->Html->url(
                                            [
                                                'controller' => 'pages',
                                                'action'     => 'display',
                                                'pagename'   => 'features',
                                                'lang'       => $top_lang,
                                            ])
                                        ?>
										"><span
                                                class="icon-back"><i class="icon-plus-sign"></i></span></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <span class="hover-fade"><i class="icon-plus-sign icon-3x"></i></span>
                    </div>
                    <a href="<?=
                    $this->Html->url(
                        [
                            'controller' => 'pages',
                            'action'     => 'display',
                            'pagename'   => 'features',
                            'lang'       => $top_lang,
                        ])
                    ?>
										"><h5>
                            <?= __d('home', "Set a goal") ?>
                            <span class="white-title"><i class="icon-plus"></i></span>
                        </h5></a>

                    <p>
                        <?=
                        __d('home',
                            "Create a goal by establishing its title, deadline, challenging point, benefits, alists, and overall image.")
                        ?>
                    </p>
                </div>
            </div>
            <div class="span3">
                <div class="white-content">
                    <div class="img-mask hover-mask">
                        <?= $this->Html->image('homepage/photos/feature-2.png') ?>
                        <div class="flip-container">
                            <div class="flipper">
                                <div class="back">
                                    <div class="flip-b">
                                        <a href="<?=
                                        $this->Html->url(
                                            [
                                                'controller' => 'pages',
                                                'action'     => 'display',
                                                'pagename'   => 'features',
                                                'lang'       => $top_lang,
                                            ])
                                        ?>
										"><span
                                                class="icon-back"><i class="icon-plus-sign"></i></span></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <span class="hover-fade"><i class="icon-plus-sign icon-3x"></i></span>
                    </div>
                    <a href="<?=
                    $this->Html->url(
                        [
                            'controller' => 'pages',
                            'action'     => 'display',
                            'pagename'   => 'features',
                            'lang'       => $top_lang,
                        ])
                    ?>
										"><h5>
                            <?= __d('home', "Make it open") ?>
                            <span class="white-title"><i class="icon-plus"></i></span>
                        </h5></a>

                    <p>
                        <?=
                        __d('home',
                            "Let team members and other co-workers know of your ambition, collaborators, and daily progress.")
                        ?>
                    </p>
                </div>
            </div>
            <div class="span3">
                <div class="white-content">
                    <div class="img-mask hover-mask">
                        <?= $this->Html->image('homepage/photos/feature-3.png') ?>
                        <div class="flip-container">
                            <div class="flipper">
                                <div class="back">
                                    <div class="flip-b">
                                        <a href="<?=
                                        $this->Html->url(
                                            [
                                                'controller' => 'pages',
                                                'action'     => 'display',
                                                'pagename'   => 'features',
                                                'lang'       => $top_lang,
                                            ])
                                        ?>
										"><span
                                                class="icon-back"><i class="icon-plus-sign"></i></span></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <span class="hover-fade"><i class="icon-plus-sign icon-3x"></i></span>
                    </div>
                    <a href="<?=
                    $this->Html->url(
                        [
                            'controller' => 'pages',
                            'action'     => 'display',
                            'pagename'   => 'features',
                            'lang'       => $top_lang,
                        ])
                    ?>
										"><h5>
                            <?= __d('home', "Help each other") ?>
                            <span class="white-title"><i class="icon-plus"></i></span>
                        </h5></a>

                    <p>
                        <?=
                        __d('home',
                            "Work towards your goal with your collaborators. You don't need to struggle alone anymore.")
                        ?>
                    </p>
                </div>
            </div>
            <div class="span3">
                <div class="white-content">
                    <div class="img-mask hover-mask">
                        <?= $this->Html->image('homepage/photos/feature-4.png') ?>
                        <div class="flip-container">
                            <div class="flipper">
                                <div class="back">
                                    <div class="flip-b">
                                        <a href="<?=
                                        $this->Html->url(
                                            [
                                                'controller' => 'pages',
                                                'action'     => 'display',
                                                'pagename'   => 'features',
                                                'lang'       => $top_lang,
                                            ])
                                        ?>
										"><span
                                                class="icon-back"><i class="icon-plus-sign"></i></span></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <span class="hover-fade"><i class="icon-plus-sign icon-3x"></i></span>
                    </div>
                    <a href="<?=
                    $this->Html->url(
                        [
                            'controller' => 'pages',
                            'action'     => 'display',
                            'pagename'   => 'features',
                            'lang'       => $top_lang,
                        ])
                    ?>
										"><h5>
                            <?= __d('home', "Send compliments") ?>
                            <span class="white-title"><i class="icon-plus"></i></span>
                        </h5></a>

                    <p>
                        <?=
                        __d('home',
                            "Getting a compliment is the best treat for your brain. Best of all, complimenting one another is free!")
                        ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- end: lastest-work -->
<!-- start: social icon -->
<div class="section-wrapper section-icon social-icon">
    <div class="container">
        <div class="row">

            <div class="social-icon-phone clear-phone">
                <div class="span1 offset3">
                    <div class="flip-container">
                        <div class="flipper">
                            <div class="front">
                                <div class="flip-a">
                                    <a href="http://www.facebook.com/goalous" target="_blank"><i
                                            class="icon-facebook icon-2x"></i></a>
                                </div>
                            </div>
                            <div class="back">
                                <div class="flip-b facebook">
                                    <a href="http://www.facebook.com/goalous" target="_blank"><span
                                            class="icon-back"><i class="icon-facebook icon-2x"></i></span></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="span1">
                    <div class="flip-container">
                        <div class="flipper">
                            <div class="front ">
                                <div class="flip-a">
                                    <a href="https://twitter.com/goalous" target="_blank"><i
                                            class="icon-twitter icon-2x"></i></a>
                                </div>
                            </div>
                            <div class="back">
                                <div class="flip-b twitter">
                                    <a href="https://twitter.com/goalous" target="_blank"><span
                                            class="icon-back"><i class="icon-twitter icon-2x"></i></span></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="span1">
                    <div class="flip-container">
                        <div class="flipper">
                            <div class="front ">
                                <div class="flip-a">
                                    <a
                                        href="https://plus.google.com/u/0/b/114558325021409202574/114558325021409202574/posts"
                                        target="_blank"><i class="icon-google-plus icon-2x"></i></a>
                                </div>
                            </div>
                            <div class="back">
                                <div class="flip-b google">
                                    <a
                                        href="https://plus.google.com/u/0/b/114558325021409202574/114558325021409202574/posts"
                                        target="_blank"><span class="icon-back"><i
                                                class="icon-google-plus icon-2x"></i></span></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="social-icon-phone clear-phone">
                <div class="span1">
                    <div class="flip-container">
                        <div class="flipper">
                            <div class="front ">
                                <div class="flip-a">
                                    <a href="https://pinterest.com/goalous/" target="_blank"><i
                                            class="icon-pinterest icon-2x"></i></a>
                                </div>
                            </div>
                            <div class="back">
                                <div class="flip-b pinterest">
                                    <a href="https://pinterest.com/goalous/" target="_blank"><span
                                            class="icon-back"><i class="icon-pinterest icon-2x"></i></span></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<!-- end: social icon -->
<?php $this->append('ogp') ?>
<meta property="og:site_name" content="Goalous"/>
<meta property="og:type" content="article"/>
<meta property="og:title" content="Goalous"/>
<meta property="og:description"
      content="<?= __d('home', "Collaborative achievement goal tool") ?>"/>
<meta property="og:image"
      content="https://s3-ap-northeast-1.amazonaws.com/goalous-www/external/img/gl_logo_180x180.png"/>
<meta property="og:url" content="https://www.goalous.com/"/>
<?php $this->end() ?>
<?
$this->Html
    ->meta('description',
           __d('home',
               "Goalous is a tool that all members of a team are able to action in high motivated toward the achievement of self goals.Because the members create public goals in a team,they collaborate each other and get praise to your actions."),
           array(
               'inline' => false
           ));
?>
<!-- END app/View/Pages/home.ctp -->
