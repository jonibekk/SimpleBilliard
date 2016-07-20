<?php
/**
 * PHP 5
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @var CodeCompletionView
 * @var
 * @var
 */
?>
<!-- START app/View/Pages/home.ctp -->
<?php $this->append('meta') ?>
<?php
/*
- meta description
- og:description
- og:url
- title
 */
$meta_lp = [
    [
        "name"    => "description",
        "content" => __('Goalous (ゴーラス) は、ゴール達成への最強にオープンな社内SNS。すべてのメンバーのゴールをオープンにし、ゴールへのアクションを写真でたのしく共有できます。スマホアプリ・ブラウザで利用可能です。'),
    ],
    [
        "name"    => "keywords",
        "content" => "目標管理,目標達成,社内SNS,評価,MBO",
    ],
    [
        "property" => "og:type",
        "content"  => "website",
    ],
    [
        "property" => "og:title",
        "content"  => __('Goalous(ゴーラス) | 最強にオープンな社内SNS'),
    ],
    [
        "property" => "og:description",
        "content"  => __('Goalous (ゴーラス) は、ゴール達成への最強にオープンな社内SNS。すべてのメンバーのゴールをオープンにし、ゴールへのアクションを写真でたのしく共有できます。スマホアプリ・ブラウザで利用可能です。'),
    ],
    [
        "property" => "og:url",
        "content"  => "https://www.goalous.com/",
    ],
    [
        "property" => "og:image",
        "content"  => "https://www.goalous.com/img/homepage/background/promo-bg.jpg",
    ],
    [
        "property" => "og:site_name",
        "content"  => __('Goalous │ Enterprise SNS the most ever open for Goal'),
    ],
    [
        "property" => "fb:app_id",
        "content"  => "966242223397117",
    ],
    [
        "name"    => "twitter_card",
        "content" => "summary",
    ],
    [
        "name"    => "twitter:site",
        "content" => "@goalous",
    ]
];
$num_ogp = count($meta_lp);
for ($i = 0; $i < $num_ogp; $i++) {
    echo $this->Html->meta($meta_lp[$i]);
}
?>
<title><?= __('Goalous (ゴーラス) │最強にオープンな社内SNS') ?></title>
<link rel="alternate" hreflang="ja" href="<?= $this->Html->url('/ja/') ?>"/>
<link rel="alternate" hreflang="en" href="<?= $this->Html->url('/en/') ?>"/>
<link rel="alternate" hreflang="x-default" href="<?= $this->Html->url('/') ?>"/>
<?php $this->end() ?>

<!-- ******PROMO****** -->
<section id="promo" class="promo section">
    <div class="container intro">
        <h2 class="title"><span class="text"><?= __('Be open, achieve Goal.') ?></span></h2>
    </div><!--//intro-->

    <div class="bg-slider-wrapper">
        <div id="bg-slider" class="flexslider bg-slider">
            <ul class="slides">
                <li class="slide slide-1"></li>
            </ul>
        </div>
    </div><!--//bg-slider-wrapper-->
</section><!--//promo-->

<div class="fixed-container">
    <div class="signup">
        <div class="container">
            <div class="col-md-7 col-sm-12 col-xs-12">
                <p class="summary"><?= __('It\'s free until 31 Aug 2016! Try it!') ?></p>
            </div>
            <div class="col-md-5 col-sm-12 col-xs-12">
                <a href="<?= $this->Html->url([
                    'controller' => 'signup',
                    'action'     => 'email',
                    '?'          => ['type' => 'middle']
                ]) ?>"
                   id="RegisterLinkTopMiddle">
                    <button type="submit" class="btn btn-cta btn-cta-primary btn-block"><?= __(
                            'Sign Up') ?></button>
                </a>
            </div>
        </div><!--//contianer-->
    </div><!--//signup-->
</div>

<!-- ここからつづき -->
<!-- ******PRESS****** -->
<div class="press">
    <div class="container text-center">
        <div class="row text-left">
            <p class="col-md-2 col-md-offset-2 col-sm-3"><?=
                __('5 Feb 2016');
                ?></p>
            <p class="col-md-6 col-sm-9">
                <?=
                $this->Html->link(
                    __('We appeared on a magazine. - Gekkan Jinji Management Feb. -'),
                    'http://blog.isao.co.jp/press_jinjimanage_20160205/',
                    ['target' => '_blank']
                );
                ?>
            </p>
        </div>
        <div class="row text-left">
            <p class="col-md-2 col-md-offset-2 col-sm-3"><?=
                __('4 Feb 2016');
                ?></p>
            <p class="col-md-6 col-sm-9">
                <?=
                $this->Html->link(
                    __('We appeared on a web media. - Hito to shigoto no kenkyujo by Idem -'),
                    'https://apj.aidem.co.jp/column/597//',
                    ['target' => '_blank']
                );
                ?>
            </p>
        </div>
    </div>
</div><!--//press-->

<!-- ******WHY****** -->
<section id="why" class="why section">
    <div class="container">
        <h2 class="title text-center"><?= __('You can definitely change your organization by Goalous.') ?></h2>
        <p class="intro text-center"><?= __('Achieve Goals, make your team open, let your job become joyful!') ?></p>
        <div class="item row flex from-left">
            <div
                class="content col-md-5 col-sm-5 col-xs-12 pull-right col-md-offset-1 col-sm-offset-1 col-xs-offset-0 col-right">
                <h3 class="title"><?= __('Get progress to Vision.') ?></h3>
                <div class="details">
                    <p><?= __(
                            'Every one knows Vision and make their Goals. Goalous let them improve themselves.') ?></p>
                </div>
            </div><!--//content-->
            <div class="figure col-md-6 col-sm-6 col-xs-12 col-left">
                <?= $this->Html->image('homepage/top/top-1.jpg',
                    array(
                        'alt'   => __(
                            'You get to know your team grow by your achievement of goals.'),
                        'class' => 'img-responsive'
                    )); ?>
            </div><!--//figure-->
        </div><!--//item-->

        <hr/>

        <div class="item row flex from-right">
            <div class="content col-md-5 col-sm-5 col-xs-12 col-left">
                <h3 class="title"><?= __('You can reduce the words, What is that?') ?></h3>
                <div class="details">
                    <p><?= __(
                            'What did your colleages did today? Share on Goalous and get to know more and more.') ?></p>
                </div>
            </div><!--//content-->
            <div class="figure col-md-6 col-sm-6 col-xs-12 col-md-offset-1 col-sm-offset-1 col-xs-offset-0 col-right">
                <?= $this->Html->image('homepage/top/top-2.jpg',
                    array(
                        'alt'   => __(
                            '仕事で大変な事も嬉しい事もオープンにしてお互いを理解しましょう！すべてはそこから始まります。'),
                        'class' => 'img-responsive'
                    )); ?>
            </div><!--//figure-->
        </div><!--//item-->

        <hr/>

        <div class="item row flex from-left">
            <div
                class="content col-md-5 col-sm-5 col-xs-12 pull-right col-md-offset-1 col-sm-offset-1 col-xs-offset-0 col-right">
                <h3 class="title"><?= __('Make results by coorporation.') ?></h3>
                <div class="details">
                    <p><?= __(
                            'What most important for teams is to know each other. Get to know by Goalous, you can get succeeded efficiently.') ?></p>
                </div>
            </div><!--//content-->
            <div class="figure col-md-6 col-sm-6 col-xs-12 col-left">
                <?= $this->Html->image('homepage/top/top-3.jpg',
                    array(
                        'alt'   => __(
                            'Help each other, enjoy with them. How wonderful?'),
                        'class' => 'img-responsive'
                    )); ?>
            </div><!--//figure-->
        </div><!--//item-->
    </div><!--//container-->
</section><!--//why-->

<!-- ******VIDEO****** -->
<section id="video" class="video section">
    <div class="container">
        <div class="control text-center">
            <button type="button" id="play-trigger" class="play-trigger" data-toggle="modal" data-target="#tour-video">
                <i class="fa fa-play"></i></button>
            <p><?= __('Watch Video') ?></p>

            <!-- Video Modal -->
            <div class="modal modal-video" id="tour-video" tabindex="-1" role="dialog" aria-labelledby="videoModalLabel"
                 aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 id="videoModalLabel" class="modal-title"><?= __('About Goalous') ?></h4>
                        </div>
                        <div class="modal-body">
                            <div class="video-container">
                                <iframe id="vimeo-video"
                                        src="https://www.youtube.com/embed/jwG1Lsq3Wyw?rel=0&autoplay=0" width="720"
                                        height="405" frameborder="0" webkitallowfullscreen mozallowfullscreen
                                        allowfullscreen></iframe>
                            </div><!--//video-container-->
                        </div><!--//modal-body-->
                    </div><!--//modal-content-->
                </div><!--//modal-dialog-->
            </div><!--//modal-->
        </div><!--//control-->
    </div>
</section><!--//video-->

<div class="store section">
    <div class="container">
        <div class="row flex">
            <div class="col-md-6 col-sm-6 col-xs-12 from-left col-left text-center">
                <h3><?= __('Wherever, Whenever, from your smartphone.') ?></h3>
                <p class="lead-text"><?= __('You can get iOS and Android apps.') ?></p>
                <?= $this->Html->link(
                    $this->Html->image('https://linkmaker.itunes.apple.com/images/badges/en-us/badge_appstore-lrg.svg'),
                    'https://itunes.apple.com/us/app/goalous-chimu-li-xiang-shangsns/id1060474459?ls=1&mt=8',
                    array(
                        'escape' => false,
                        'alt'    => 'iPhoneアプリもご利用いただけます',
                        'class'  => 'app-dl-btn'
                    ))
                ?>
                <?= $this->Html->link(
                    $this->Html->image(
                        'https://play.google.com/intl/en_us/badges/images/apps/en-play-badge.png',
                        [
                            'alt'    => 'Get it on Google Play',
                            'height' => '40'
                        ]),
                    'https://play.google.com/store/apps/details?id=jp.co.isao.android.goalous',
                    [
                        'escape' => false,
                        'class'  => 'app-dl-btn'
                    ])
                ?>
            </div>
            <div class="col-md-6 col-sm-6 col-xs-12 from-right col-right">
                <?= $this->Html->image('homepage/top/devices.png', array('alt' => '', 'class' => 'img-responsive')); ?>
            </div>
        </div>
    </div>
</div>

<?= $this->element('Homepage/faq') ?>
<?= $this->element('Homepage/signup') ?>

<section class="document">
    <div class="container text-center">
        <dl class="media col-md-6 from-bottom">
            <div class="media-left media-middle">
                <i class="fa fa fa-file-pdf-o document-fa"></i>
            </div>
            <div class="media-body">
                <dt class="bold-text">
                    <?= __('What is Goalous? (jp, pdf)') ?>
                </dt>
                <dd>
                    <?= __('This file is written in Japanese.') ?>
                    <br>
                    <a href="../composition/pdf/jp_GoalousIntroduction_100.pdf" target="_blank"><i
                            class="fa fa-arrow-down document-download-icon"></i>
                        <span class="document-download-text">
                            <?= __('Download the file') ?>
                        </span>
                    </a>
                </dd>
            </div>
        </dl>
        <dl class="media col-md-6 from-bottom">
            <div class="media-left media-middle">
                <i class="fa fa fa-file-pdf-o document-fa"></i>
            </div>
            <div class="media-body">
                <dt class="bold-text">
                    <?= __('Fryer (pdf)') ?>
                </dt>
                <dd>
                    <?= __('This is Goalous flyer.') ?>
                    <br>
                    <a href="../composition/pdf/jp_goalous_flier_campaign.pdf" target="_blank"><i
                            class="fa fa-arrow-down document-download-icon"></i>
                        <span class="document-download-text">
                            <?= __('Download the file') ?>
                        </span>
                    </a>
                </dd>
            </div>
        </dl>
    </div>
</section>
<!-- END app/View/Pages/home.ctp -->
