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
                <h2 class="title"><?= __d('lp', 'Goalousで変えられる、あなたの組織・シゴト') ?></h2>
                <ul class="list-unstyled features-list">
                    <li><i class="fa fa-check"></i><?= __d('lp', '意思決定のスピードが爆速に') ?></li>
                    <li><i class="fa fa-check"></i><?= __d('lp', '社員全員へ企業のビジョンを浸透') ?></li>
                    <li><i class="fa fa-check"></i><?= __d('lp', '透明性のある評価ができる') ?></li>
                    <li><i class="fa fa-check"></i><?= __d('lp', '組織の壁なんて感じない') ?></li>
                    <li><i class="fa fa-check"></i><?= __d('lp', 'なんだか、仕事が楽しくなる') ?></li>
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
                <h3 class="title"><i class="fa fa-flag-o"></i><?= __d('lp', '一目瞭然、全社員のゴール') ?></h3>
                <div class="details">
                    <p><?= __d('lp', 'チームの1人ひとりのゴール。つまり、何のために何を目指し、どんなアクションをしているのか、全てオープンです。チームフィードにアクセスすると、見えないものが見える。そんな体験が待ってます。') ?></p>
                </div>
            </div><!--//content-->
            <div class="figure col-md-6 col-sm-6 col-xs-12 from-left col-left">
                <?= $this->Html->image('homepage/features/screenshot-1.png', array('alt' => '', 'class' => 'img-responsive')) ?>
            </div><!--//figure-->
        </div><!--//item-->

        <hr />

        <div class="item row flex">
            <div class="content col-md-5 col-sm-5 col-xs-12 from-left col-left">
                <h3 class="title"><i class="fa fa-image"></i><?= __d('lp', '視覚に刺さる、フォトアクション') ?></h3>
                <div class="details">
                    <p>
                        <?=
                            __d('lp', '報告も共有も自己アピールも、フォトアクション＝フォトアクで完了。仕事もセルフプロデュースの時代です。').
                            '<br>'.
                            __d('lp', '打ち合わせの最後に「フォトアク撮ろう！」の一言で、ほら、なんだか楽しくなってきた。')
                        ?>
                    </p>
                </div>
            </div><!--//content-->
            <div class="figure col-md-6 col-sm-6 col-xs-12 col-md-offset-1 col-sm-offset-1 col-xs-offset-0 from-right col-right">
                <?= $this->Html->image('homepage/features/screenshot-2.jpg', array('alt' => '', 'class' => 'img-responsive')) ?>
            </div><!--//figure-->
        </div><!--//item-->

         <hr />

        <div class="item row flex">
            <div class="content col-md-5 col-sm-5 col-xs-12 pull-right col-md-offset-1 col-sm-offset-1 col-xs-offset-0 from-right col-right">
                <h3 class="title"><i class="fa fa-circle-o"></i><?= __d('lp', '共有はサークルで') ?></h3>
                <div class="details">
                    <p><?= __d('lp', '部署別、プロジェクト別、同期・・・社内のあらゆるコミュニティ毎に、情報共有の場を作成できます。慣れたSNSのインターフェースで、投稿や写真・ファイルをシェア。') ?></p>
                </div>
            </div><!--//content-->
            <div class="figure col-md-6 col-sm-6 col-xs-12 from-left col-left">
                <?= $this->Html->image('homepage/features/screenshot-3.png', array('alt' => '', 'class' => 'img-responsive')) ?>
            </div><!--//figure-->
        </div><!--//item-->

         <hr />

        <div class="item row flex">
            <div class="content col-md-5 col-sm-5 col-xs-12 from-left col-left">
                <h3 class="title"><i class="fa fa-send-o"></i><?= __d('lp', 'メッセージ送受信') ?></h3>
                <div class="details">
                    <p><?= __d('lp', 'アプリでもWebでも、メッセージでコミュニケーション。社内限定だから、安心してやり取りできます。ファイルの添付やグループチャットできるから、もうEメールはいりません。') ?></p>
                </div>
            </div><!--//content-->
            <div class="figure col-md-6 col-sm-6 col-xs-12 col-md-offset-1 col-sm-offset-1 col-xs-offset-0 from-right col-right">
                <?= $this->Html->image('homepage/features/screenshot-4.png', array('alt' => '', 'class' => 'img-responsive')) ?>
            </div><!--//figure-->
        </div><!--//item-->

         <hr />

        <div class="item row flex">
            <div class="content col-md-5 col-sm-5 col-xs-12 pull-right col-md-offset-1 col-sm-offset-1 col-xs-offset-0 from-right col-right">
                <h3 class="title"><i class="fa fa-shield"></i><?= __d('lp', '根拠のある評価ができる') ?></h3>
                <div class="details">
                    <p>
                        <?=
                            __d('lp', '評価面談で「今期は何をやり遂げましたか？」なんて質問は不要です。').
                            '<br>'.
                            __d('lp', 'ゴールの達成度、やってきたアクションをすでに知っているから。')
                        ?>
                    </p>
                </div>
            </div><!--//content-->
            <div class="figure col-md-6 col-sm-6 col-xs-12 from-left col-left">
                <?= $this->Html->image('homepage/features/screenshot-5.jpg', array('alt' => '', 'class' => 'img-responsive')) ?>
            </div><!--//figure-->
        </div><!--//item-->

        <hr />

        <div class="item row flex">
            <div class="content col-md-5 col-sm-5 col-xs-12 from-left col-left">
                <h3 class="title"><i class="fa fa-line-chart"></i><?= __d('lp', 'インサイト') ?></h3>
                <div class="details">
                    <p><?= __d('lp', '社内SNSを導入している企業でよく言われるのが「導入して効果があったのかわからない」という声。インサイトやランキングで、エンゲージメントの増加が目に見えます。') ?></p>
                </div>
            </div><!--//content-->
            <div class="figure col-md-6 col-sm-6 col-xs-12 col-md-offset-1 col-sm-offset-1 col-xs-offset-0 from-right col-right">
                <?= $this->Html->image('homepage/features/screenshot-6.jpg', array('alt' => '', 'class' => 'img-responsive')) ?>
            </div><!--//figure-->
        </div><!--//item-->
        <div class="container text-center">
            <h2 class="title"><?= __d('lp', 'もっとあります、シゴトをたのしくするGoalousの特徴。') ?></h2></div>
           <div class="row">
            <div class="benefits col-md-12">

                <div class="item clearfix">
                    <div class="icon col-md-3 col-xs-12 text-center">
                        <i class="fa fa-child"></i>
                    </div><!--//icon-->
                    <div class="content col-md-9 col-xs-12">
                        <h3 class="title"><?= __d('lp', 'ゴールコラボ') ?></h3>
                        <p class="desc">
                            <?=
                                __d('lp', '隣の部署、別のフロアの人に、以外な共通点があるかもしれません。').
                                '<br>'.
                                __d('lp', '見えないものを見る。その連鎖が、あなたのゴールに助け合いをうみ、それがチームのゴール、成長への道になります。')
                            ?>
                        </p>
                    </div><!--//content-->
                </div><!--//item-->
                <div class="item clearfix">
                    <div class="icon col-md-3  col-xs-12 text-center">
                        <i class="fa fa-venus-double"></i>
                    </div><!--//icon-->
                    <div class="content col-md-9 col-xs-12">
                        <h3 class="title"><?= __d('lp', 'コーチ') ?></h3>
                        <p class="desc"><?= __d('lp', '上司、部長、課長・・・評価してるだけでない、日々アクションを見て、アゲてくれる人。1メンバーにつき1人設定します。') ?></p>
                    </div><!--//content-->
                </div><!--//item-->
                <div class="item clearfix">
                    <div class="icon col-md-3  col-xs-12 text-center">
                        <i class="fa fa-user"></i>
                    </div><!--//icon-->
                    <div class="content col-md-9 col-xs-12">
                        <h3 class="title"><?= __d('lp', 'プロフィール') ?></h3>
                        <p class="desc"><?= __d('lp', '顔と名前の一致に加え、何を目指して毎日どんなアクションをしているか検索できる。これは全社のデータベースです。') ?></p>
                    </div><!--//content-->
                </div><!--//item-->
                <div class="item clearfix">
                    <div class="icon col-md-3  col-xs-12 text-center">
                        <i class="fa fa-rocket"></i>
                    </div><!--//icon-->
                    <div class="content col-md-9 col-xs-12">
                        <h3 class="title"><?= __d('lp', 'ビジョン') ?></h3>
                        <p class="desc"><?= __d('lp', '会社のビジョン、どのくらいの社員が知っていますか？いつでも意識できるインターフェースを用意しています。') ?></p>
                    </div><!--//content-->
                </div><!--//item-->
            </div>
    </div><!--//container-->
</section>

<?= $this->element('Homepage/signup') ?>
<!-- END app/View/Pages/features.ctp -->
