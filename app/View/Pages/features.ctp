<?php /**
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
 * @var CodeCompletionView $this
 * @var                    $user_count
 * @var                    $top_lang
 */
?>
<?php $this->append('meta') ?>
<?php
/*
Page毎に要素が変わるもの
- meta description
- og:description
- og:url
- title
*/
$meta_features = [
    [
        "name" => "description",
        "content" => __('Goalous (ゴーラス) は、ゴール達成への最強にオープンな社内SNS。すべてのメンバーのゴールをオープンにし、ゴールへのアクションを写真でたのしく共有できます。機能の説明はこちら。'),
    ],
    [
        "name" => "keywords",
        "content" => "目標管理,目標達成,社内SNS,評価,MBO",
    ],
    [
        "property" => "og:type",
        "content" => "website",
    ],
    [
        "property" => "og:title",
        "content" => __('機能 | Goalous (ゴーラス))'),
    ],
    [
        "property" => "og:description",
        "content" =>__('Goalous (ゴーラス) は、ゴール達成への最強にオープンな社内SNS。すべてのメンバーのゴールをオープンにし、ゴールへのアクションを写真でたのしく共有できます。機能の説明はこちら。'),
    ],
    [
        "property" => "og:url",
        "content" => "https://www.goalous.com/features/",
    ],
    [
        "property" => "og:image",
        "content" => "https://www.goalous.com/img/homepage/background/promo-bg.jpg",
    ],
    [
        "property" => "og:site_name",
        "content" => __('Goalous │ Enterprise SNS the most ever open for Goal'),
    ],
    [
        "property" => "fb:app_id",
        "content" => "966242223397117",
    ],
    [
        "name" => "twitter_card",
        "content" => "summary",
    ],
    [
        "name" => "twitter:site",
        "content" => "@goalous",
    ]
];
$num_ogp = count($meta_features);
for($i = 0; $i < $num_ogp; $i++){
    echo $this->Html->meta($meta_features[$i]);
}
?>
<title><?= __('機能 | Goalous (ゴーラス)') ?></title>
<link rel="alternate" hreflang="ja" href="<?= $this->Html->url('/ja/features') ?>"/>
<link rel="alternate" hreflang="en" href="<?= $this->Html->url('/en/features') ?>"/>
<link rel="alternate" hreflang="x-default" href="<?= $this->Html->url('/features') ?>"/>
<?php $this->end() ?>
<!-- START app/View/Pages/features.ctp -->
<!-- ******FEATURES PROMO****** -->
<section id="features-promo" class="features-promo section">
    <div class="bg-mask"></div>
    <div class="container">
        <div class="row">
            <div class="features-intro col-md-5 col-sm-6 col-xs-12">
                <h2 class="title"><?= __('Goalousで変えられる、あなたの組織・シゴト') ?></h2>
                <ul class="list-unstyled features-list">
                    <li><i class="fa fa-check"></i><?= __('意思決定のスピードが爆速に') ?></li>
                    <li><i class="fa fa-check"></i><?= __('社員全員へ企業のビジョンを浸透') ?></li>
                    <li><i class="fa fa-check"></i><?= __('透明性のある評価ができる') ?></li>
                    <li><i class="fa fa-check"></i><?= __('組織の壁なんて感じない') ?></li>
                    <li><i class="fa fa-check"></i><?= __('なんだか、仕事が楽しくなる') ?></li>
                </ul>
            </div><!--//intro-->
            <div class="features-video col-md-7 col-sm-6 col-xs-12 col-xs-offset-0">
                <div class="video-container">
                    <iframe width="720" height="405" src="https://www.youtube.com/embed/jwG1Lsq3Wyw?rel=0&autoplay=1" frameborder="0" allowfullscreen></iframe>
                </div><!--//video-container-->
            </div><!--//video-->
        </div><!--//row-->
    </div><!--//container-->
</section><!--//features-promo-->

<!-- ******FEATURES****** -->
<section id="features" class="features section">
    <div class="container">
        <div class="item row flex">
            <div class="content col-md-5 col-sm-5 col-xs-12 pull-right col-md-offset-1 col-sm-offset-1 col-xs-offset-0 from-right col-right">
                <h3 class="title"><i class="fa fa-flag-o"></i><?= __('一目瞭然、全社員のゴール') ?></h3>
                <div class="details">
                    <p><?= __('チームの1人ひとりのゴール。つまり、何のために何を目指し、どんなアクションをしているのか、全てオープンです。チームフィードにアクセスすると、見えないものが見える。そんな体験が待ってます。') ?></p>
                </div>
            </div><!--//content-->
            <div class="figure col-md-6 col-sm-6 col-xs-12 from-left col-left">
                <?= $this->Html->image('homepage/features/screenshot-1.jpg', array('alt' => __('全員のゴールを簡単に知る事ができます。今までこの情報を知りうる手段はありましたか？'), 'class' => 'img-responsive')) ?>
            </div><!--//figure-->
        </div><!--//item-->

        <hr />

        <div class="item row flex">
            <div class="content col-md-5 col-sm-5 col-xs-12 from-left col-left">
                <h3 class="title"><i class="fa fa-image"></i><?= __('視覚に刺さる、フォトアクション') ?></h3>
                <div class="details">
                    <p>
                        <?=
                            __('報告も共有も自己アピールも、フォトアクション＝フォトアクで完了。仕事もセルフプロデュースの時代です。').
                            '<br>'.
                            __('打ち合わせの最後に「フォトアク撮ろう！」の一言で、ほら、なんだか楽しくなってきた。')
                        ?>
                    </p>
                </div>
            </div><!--//content-->
            <div class="figure col-md-6 col-sm-6 col-xs-12 col-md-offset-1 col-sm-offset-1 col-xs-offset-0 from-right col-right">
                <?= $this->Html->image('homepage/features/screenshot-2.jpg', array('alt' => __('写真によって活動の表現力が格段にあがります。'), 'class' => 'img-responsive')) ?>
            </div><!--//figure-->
        </div><!--//item-->

         <hr />

        <div class="item row flex">
            <div class="content col-md-5 col-sm-5 col-xs-12 pull-right col-md-offset-1 col-sm-offset-1 col-xs-offset-0 from-right col-right">
                <h3 class="title"><i class="fa fa-circle-o"></i><?= __('共有はサークルで') ?></h3>
                <div class="details">
                    <p><?= __('部署別、プロジェクト別、同期・・・社内のあらゆるコミュニティ毎に、情報共有の場を作成できます。慣れたSNSのインターフェースで、投稿や写真・ファイルをシェア。') ?></p>
                </div>
            </div><!--//content-->
            <div class="figure col-md-6 col-sm-6 col-xs-12 from-left col-left">
                <?= $this->Html->image('homepage/features/screenshot-3.jpg', array('alt' => __('様々な活動や話題をサークルで共有しよう！'), 'class' => 'img-responsive')) ?>
            </div><!--//figure-->
        </div><!--//item-->

         <hr />

        <div class="item row flex">
            <div class="content col-md-5 col-sm-5 col-xs-12 from-left col-left">
                <h3 class="title"><i class="fa fa-send-o"></i><?= __('メッセージ送受信') ?></h3>
                <div class="details">
                    <p><?= __('アプリでもWebでも、メッセージでコミュニケーション。社内限定だから、安心してやり取りできます。ファイルの添付やグループチャットできるから、もうEメールはいりません。') ?></p>
                </div>
            </div><!--//content-->
            <div class="figure col-md-6 col-sm-6 col-xs-12 col-md-offset-1 col-sm-offset-1 col-xs-offset-0 from-right col-right">
                <?= $this->Html->image('homepage/features/screenshot-4.jpg', array('alt' => __('Goalousはメッセンジャー機能も備えています。'), 'class' => 'img-responsive')) ?>
            </div><!--//figure-->
        </div><!--//item-->

         <hr />

        <div class="item row flex">
            <div class="content col-md-5 col-sm-5 col-xs-12 pull-right col-md-offset-1 col-sm-offset-1 col-xs-offset-0 from-right col-right">
                <h3 class="title"><i class="fa fa-shield"></i><?= __('根拠のある評価ができる') ?></h3>
                <div class="details">
                    <p>
                        <?=
                            __('評価面談で「今期は何をやり遂げましたか？」なんて質問は不要です。').
                            '<br>'.
                            __('ゴールの達成度、やってきたアクションをすでに知っているから。')
                        ?>
                    </p>
                </div>
            </div><!--//content-->
            <div class="figure col-md-6 col-sm-6 col-xs-12 from-left col-left">
                <?= $this->Html->image('homepage/features/screenshot-5.jpg', array('alt' => __('評価？もちろんGoalousでできます。'), 'class' => 'img-responsive')) ?>
            </div><!--//figure-->
        </div><!--//item-->

        <hr />

        <div class="item row flex">
            <div class="content col-md-5 col-sm-5 col-xs-12 from-left col-left">
                <h3 class="title"><i class="fa fa-line-chart"></i><?= __('インサイト') ?></h3>
                <div class="details">
                    <p><?= __('社内SNSを導入している企業でよく言われるのが「導入して効果があったのかわからない」という声。インサイトやランキングで、エンゲージメントの増加が目に見えます。') ?></p>
                </div>
            </div><!--//content-->
            <div class="figure col-md-6 col-sm-6 col-xs-12 col-md-offset-1 col-sm-offset-1 col-xs-offset-0 from-right col-right">
                <?= $this->Html->image('homepage/features/screenshot-6.jpg', array('alt' => __('チーム力が向上しているか？これを見れば分かります。'), 'class' => 'img-responsive')) ?>
            </div><!--//figure-->
        </div><!--//item-->
        <div class="container text-center">
            <h2 class="title"><?= __('もっとあります、シゴトをたのしくするGoalousの特徴。') ?></h2></div>
           <div class="row">
            <div class="benefits col-md-12">

                <div class="item clearfix">
                    <div class="icon col-md-3 col-xs-12 text-center">
                        <i class="fa fa-child"></i>
                    </div><!--//icon-->
                    <div class="content col-md-9 col-xs-12">
                        <h3 class="title"><?= __('ゴールコラボ') ?></h3>
                        <p class="desc">
                            <?=
                                __('隣の部署、別のフロアの人に、以外な共通点があるかもしれません。').
                                '<br>'.
                                __('見えないものを見る。その連鎖が、あなたのゴールに助け合いをうみ、それがチームのゴール、成長への道になります。')
                            ?>
                        </p>
                    </div><!--//content-->
                </div><!--//item-->
                <div class="item clearfix">
                    <div class="icon col-md-3  col-xs-12 text-center">
                        <i class="fa fa-venus-double"></i>
                    </div><!--//icon-->
                    <div class="content col-md-9 col-xs-12">
                        <h3 class="title"><?= __('コーチ') ?></h3>
                        <p class="desc"><?= __('上司、部長、課長・・・評価してるだけでない、日々アクションを見て、アゲてくれる人。1メンバーにつき1人設定します。') ?></p>
                    </div><!--//content-->
                </div><!--//item-->
                <div class="item clearfix">
                    <div class="icon col-md-3  col-xs-12 text-center">
                        <i class="fa fa-user"></i>
                    </div><!--//icon-->
                    <div class="content col-md-9 col-xs-12">
                        <h3 class="title"><?= __('Profile') ?></h3>
                        <p class="desc"><?= __('顔と名前の一致に加え、何を目指して毎日どんなアクションをしているか検索できる。これは全社のデータベースです。') ?></p>
                    </div><!--//content-->
                </div><!--//item-->
                <div class="item clearfix">
                    <div class="icon col-md-3  col-xs-12 text-center">
                        <i class="fa fa-rocket"></i>
                    </div><!--//icon-->
                    <div class="content col-md-9 col-xs-12">
                        <h3 class="title"><?= __('Vision') ?></h3>
                        <p class="desc"><?= __('会社のビジョン、どのくらいの社員が知っていますか？いつでも意識できるインターフェースを用意しています。') ?></p>
                    </div><!--//content-->
                </div><!--//item-->
            </div>
    </div><!--//container-->
</section>

<?= $this->element('Homepage/signup') ?>
<!-- END app/View/Pages/features.ctp -->
