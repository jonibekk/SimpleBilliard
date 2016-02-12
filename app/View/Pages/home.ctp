<?php
/**
 * PHP 5
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * @link          http://cakephp.org CakePHP(tm) Project
 * @since         CakePHP(tm) v 0.10.0.1076
 *
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 *
 * @var CodeCompletionView
 * @var
 * @var
 */
?>
<!-- START app/View/Pages/home.ctp -->
<?php $this->append('meta') ?>
<link rel="alternate" hreflang="ja" href="<?= $this->Html->url('/ja/') ?>"/>
<link rel="alternate" hreflang="en" href="<?= $this->Html->url('/en/') ?>"/>
<link rel="alternate" hreflang="x-default" href="<?= $this->Html->url('/') ?>"/>
<?php $this->end() ?>

<!-- ******PROMO****** -->
<section id="promo" class="promo section">
    <div class="container intro">
        <h2 class="title"><span class="text"><?= __d('lp', 'オープンがゴールを切り拓く') ?></span></h2>
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
                <p class="summary"><?= __d('lp', '2016年8月31日まで完全無料！今すぐお試しください。') ?></p>
            </div>
            <div class="col-md-5 col-sm-12 col-xs-12">
                <a href="<?= $this->Html->url(['controller' => 'users', 'action' => 'register']) ?>">
                    <button type="submit" class="btn btn-cta btn-cta-primary btn-block"><?= __d('lp',
                                                                                                '新規会員登録') ?></button>
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
                    __d('lp', '2016年2月5日');
            ?></p>
            <p class="col-md-6 col-sm-9">
                <?=
                    $this->Html->link(
                        __d('lp', '月刊人事マネジメント2016年2月号に掲載されました'),
                        'http://blog.isao.co.jp/press_jinjimanage_20160205/',
                        ['target' => '_blank']
                    );
                ?>
            </p>
        </div>
        <div class="row text-left">
            <p class="col-md-2 col-md-offset-2 col-sm-3"><?=
                    __d('lp', '2016年2月4日');
            ?></p>
            <p class="col-md-6 col-sm-9">
                <?=
                    $this->Html->link(
                        __d('lp', 'アイデム 人と仕事の研究所 制度探訪に掲載されました'),
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
        <h2 class="title text-center"><?= __d('lp', 'Goalousで、組織は激変する') ?></h2>
        <p class="intro text-center"><?= __d('lp', '「ゴール達成」って？「最強にオープン」って？Goalousは、世界のシゴトをたのしくしたい。それだけのこと。') ?></p>
        <div class="item row flex from-left">
            <div
                class="content col-md-5 col-sm-5 col-xs-12 pull-right col-md-offset-1 col-sm-offset-1 col-xs-offset-0 col-right">
                <h3 class="title"><?= __d('lp', '経営ビジョンにずんずん近づく') ?></h3>
                <div class="details">
                    <p><?= __d('lp',
                               '経営ビジョンが反映され、従業員たちが到達を目指す具体的な指標。それがゴールです。Goalousを使えば使うほど、ゴールへの活動が増えて、組織が前へ前へずんずん進みます。') ?></p>
                </div>
            </div><!--//content-->
            <div class="figure col-md-6 col-sm-6 col-xs-12 col-left">
                <?= $this->Html->image('homepage/top/top-1.jpg', array('alt' => '', 'class' => 'img-responsive')); ?>
            </div><!--//figure-->
        </div><!--//item-->

        <hr/>

        <div class="item row flex from-right">
            <div class="content col-md-5 col-sm-5 col-xs-12 col-left">
                <h3 class="title"><?= __d('lp', '「それ知らない」が激減する') ?></h3>
                <div class="details">
                    <p><?= __d('lp',
                               '商談した・ドキュメントを作成した・プレゼンした…など、ゴール( 目標 )に対する日々のアクションや、特定の仲間との会話によって情報量が増えます。情報の不足がなければ、より的確な判断ができます。') ?></p>
                </div>
            </div><!--//content-->
            <div class="figure col-md-6 col-sm-6 col-xs-12 col-md-offset-1 col-sm-offset-1 col-xs-offset-0 col-right">
                <?= $this->Html->image('homepage/top/top-2.jpg', array('alt' => '', 'class' => 'img-responsive')); ?>
            </div><!--//figure-->
        </div><!--//item-->

        <hr/>

        <div class="item row flex from-left">
            <div
                class="content col-md-5 col-sm-5 col-xs-12 pull-right col-md-offset-1 col-sm-offset-1 col-xs-offset-0 col-right">
                <h3 class="title"><?= __d('lp', '協力による成果が出る') ?></h3>
                <div class="details">
                    <p><?= __d('lp',
                               'チームでミッションを達成するのに、最も大切な要素は「お互いにわかり合う」こと。Goalousを通して、お互いの活動を認め合い、助け合うことで効率よく成果が出るようになります。') ?></p>
                </div>
            </div><!--//content-->
            <div class="figure col-md-6 col-sm-6 col-xs-12 col-left">
                <?= $this->Html->image('homepage/top/top-3.jpg', array('alt' => '', 'class' => 'img-responsive')); ?>
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
            <p><?= __d('lp', 'Watch Video') ?></p>

            <!-- Video Modal -->
            <div class="modal modal-video" id="tour-video" tabindex="-1" role="dialog" aria-labelledby="videoModalLabel"
                 aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 id="videoModalLabel" class="modal-title"><?= __d('lp', 'Goalousについて') ?></h4>
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
                <h3><?= __d('lp', 'スマホアプリで、いつでもどこからでも') ?></h3>
                <p class="lead-text"><?= __d('lp', 'iOS・Androidアプリでもご利用いただけます') ?></p>
                <?= $this->Html->link(
                    $this->Html->image('http://linkmaker.itunes.apple.com/images/badges/en-us/badge_appstore-lrg.svg'),
                    'https://itunes.apple.com/us/app/goalous-chimu-li-xiang-shangsns/id1060474459?ls=1&mt=8',
                    array(
                        'escape' => false,
                        'alt' => 'iOS・Androidアプリでもご利用いただけます',
                        'class' => 'app-dl-btn'
                    ))
                ?>
                <?= $this->Html->link(
                    $this->Html->image(
                        'https://play.google.com/intl/en_us/badges/images/apps/en-play-badge.png',
                        [
                            'alt' => 'Get it on Google Play',
                            'height' => '40'
                        ]),
                        'https://play.google.com/store/apps/details?id=jp.co.isao.android.goalous/',
                        [
                            'escape' => false,
                            'class' => 'app-dl-btn'
                        ])
                ?>
            </div>
            <div class="col-md-6 col-sm-6 col-xs-12 from-right col-right">
                <?= $this->Html->image( 'homepage/top/devices.png', array('alt' => '', 'class' => 'img-responsive')); ?>
            </div>
        </div>
    </div>
</div>

<?= $this->element('Homepage/faq') ?>

<section class="document">
    <div class="container text-center">
        <dl class="media col-md-6 from-bottom">
            <div class="media-left media-middle">
                <i class="fa fa fa-file-pdf-o document-fa"></i>
            </div>
            <div class="media-body">
                <dt class="bold-text">
                </dt>
                <dd>
                    <?= __d('lp', '社内稟議用のサンプル資料です。是非ご活用ください。') ?>
                    <br>
                    <a href="#"><i class="fa fa-caret-right"></i>
                        <?= __d('lp', '資料ダウンロード') ?>
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
                    <?= __d('lp', 'フライヤー（PDFファイル / 2.8MB）') ?>
                </dt>
                <dd>
                    <?= __d('lp', 'フライヤー資料です。是非ご活用ください。') ?>
                    <br>
                    <a href="#"><i class="fa fa-caret-right"></i>
                        <?= __d('lp', '資料ダウンロード') ?>
                    </a>
                </dd>
            </div>
        </dl>
    </div>
</section>

<?= $this->element('Homepage/signup') ?>
<!-- END app/View/Pages/home.ctp -->
