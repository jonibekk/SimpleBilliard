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
                <a href="https://www.goalous.com/users/register"><button type="submit" class="btn btn-cta btn-cta-primary btn-block"><?= __d('lp', '新規会員登録') ?></button></a>
            </div>
        </div><!--//contianer-->
    </div><!--//signup-->
</div>

<!-- ここからつづき -->
<!-- ******PRESS****** -->
<div class="press">
    <div class="container text-center">
        <div class="row text-left">
            <p class="col-md-2 col-md-offset-2 col-sm-3"><?= __d('lp', '2016年2月5日') ?></p>
            <p class="col-md-6 col-sm-9"><a href="/Goalous_lp_mock/company-content.html"><?= __d('lp', '月刊人事マネジメント2016年2月号に掲載されました') ?></a></p>
        </div>
        <div class="row text-left">
                <p class="col-md-2 col-md-offset-2 col-sm-3"><?= __d('lp', '2016年2月5日') ?></p>
                <p class="col-md-6 col-sm-9">
                    <?= $this->Html->link( __d('lp', 'お問い合わせ'), array('controller' => 'company-content'), array('class' => 'btn btn-cta btn')) ?>
                    <a href="/Goalous_lp_mock/company-content.html">
                    <?= __d('lp', '月刊人事マネジメント2016年2月号に掲載されました') ?></a></p>
        </div>
    </div>
</div><!--//press-->

<!-- ******WHY****** -->
<section id="why" class="why section">
    <div class="container">
        <h2 class="title text-center"><?= __d('lp', 'Goalousで、組織は激変する') ?></h2>
        <p class="intro text-center"><?= __d('lp', '「ゴール達成」って？「最強にオープン」って？Goalousは、世界のシゴトをたのしくしたい。それだけのこと。') ?></p>
        <div class="item row">
            <div class="content col-md-5 col-sm-5 col-xs-12 pull-right col-md-offset-1 col-sm-offset-1 col-xs-offset-0 from-right">
                <h3 class="title"><?= __d('lp', '経営ビジョンにずんずん近づく') ?></h3>
                <div class="details">
                    <p><?= __d('lp', '経営ビジョンが反映され、従業員たちが到達を目指す具体的な指標。それがゴールです。Goalousを使えば使うほど、ゴールへの活動が増えて、組織が前へ前へずんずん進みます。') ?></p>
                </div>
            </div><!--//content-->
            <div class="figure col-md-6 col-sm-6 col-xs-12 from-left">
                <?= $this->Html->image('homepage/top/top-1.jpg', array('alt' => '', 'class' => 'img-responsive')); ?>
            </div><!--//figure-->
        </div><!--//item-->

        <hr />

        <div class="item row">
            <div class="content col-md-5 col-sm-5 col-xs-12 from-left">
                <h3 class="title"><?= __d('lp', '「それ知らない」が激減する') ?></h3>
                <div class="details">
                    <p><?= __d('lp', '商談した・ドキュメントを作成した・プレゼンした…など、ゴール( 目標 )に対する日々のアクションや、特定の仲間との会話によって情報量が増えます。情報の不足がなければ、より的確な判断ができます。') ?></p>
                </div>
            </div><!--//content-->
            <div class="figure col-md-6 col-sm-6 col-xs-12 col-md-offset-1 col-sm-offset-1 col-xs-offset-0 from-right">
                <?= $this->Html->image('homepage/top/top-2.jpg', array('alt' => '', 'class' => 'img-responsive')); ?>
            </div><!--//figure-->
        </div><!--//item-->

        <hr />

        <div class="item row">
            <div class="content col-md-5 col-sm-5 col-xs-12 pull-right col-md-offset-1 col-sm-offset-1 col-xs-offset-0 from-right">
                <h3 class="title"><?= __d('lp', '協力による成果が出る') ?></h3>
                <div class="details">
                    <p><?= __d('lp', 'チームでミッションを達成するのに、最も大切な要素は「お互いにわかり合う」こと。Goalousを通して、お互いの活動を認め合い、助け合うことで効率よく成果が出るようになります。') ?></p>
                </div>
            </div><!--//content-->
            <div class="figure col-md-6 col-sm-6 col-xs-12 from-left">
                <?= $this->Html->image('homepage/top/top-3.jpg', array('alt' => '', 'class' => 'img-responsive')); ?>
            </div><!--//figure-->
        </div><!--//item-->
    </div><!--//container-->
</section><!--//why-->

<!-- ******VIDEO****** -->
<section id="video" class="video section">
    <div class="container">
        <div class="control text-center">
            <button type="button" id="play-trigger" class="play-trigger" data-toggle="modal" data-target="#tour-video"><i class="fa fa-play"></i></button>
            <p><?= __d('lp', 'Watch Video') ?></p>

            <!-- Video Modal -->
            <div class="modal modal-video" id="tour-video" tabindex="-1" role="dialog" aria-labelledby="videoModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 id="videoModalLabel" class="modal-title"><?= __d('lp', 'Video Tour') ?></h4>
                        </div>
                        <div class="modal-body">
                            <div class="video-container">
                                <iframe id="vimeo-video" src="https://www.youtube.com/embed/jwG1Lsq3Wyw?rel=0&autoplay=0" width="720" height="405" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
                            </div><!--//video-container-->
                        </div><!--//modal-body-->
                    </div><!--//modal-content-->
                </div><!--//modal-dialog-->
            </div><!--//modal-->
        </div><!--//control-->
    </div>
</section><!--//video-->

<!-- ******FAQ****** -->
<section id="faq" class="faq section has-bg-color">
    <div class="container">
        <h2 class="title text-center"><?= __d('lp', 'よくあるご質問') ?></h2>
        <div class="row">
            <div class="col-md-6 col-sm-6 col-xs-12">
                <div class="panel">
                    <div class="panel-heading">
                        <h4 class="panel-title"><a data-parent="#accordion"
                                                   data-toggle="collapse" class="panel-toggle" href="#faq1"><i class="fa fa-plus-square"></i><?= __d('lp', '他の社内向けSNSと何が違いますか？') ?></a></h4>
                    </div>

                    <div class="panel-collapse collapse" id="faq1">
                        <div class="panel-body">
                            <?= __d('lp', '回答を入れる。回答を入れる。回答を入れる。回答を入れる。') ?>
                            <?= __d('lp', '回答を入れる。回答を入れる。回答を入れる。回答を入れる。') ?>
                        </div>
                    </div>
                </div><!--//panel-->

                <div class="panel">
                    <div class="panel-heading">
                        <h4 class="panel-title"><a data-parent="#accordion"
                                                   data-toggle="collapse" class="panel-toggle" href="#faq2"><i class="fa fa-plus-square"></i><?= __d('lp', '個人の目標評価はどのようにおこなうのですか？') ?></a></h4>
                    </div>

                    <div class="panel-collapse collapse" id="faq2">
                        <div class="panel-body">
                            <?= __d('lp', '回答を入れる。回答を入れる。回答を入れる。回答を入れる。') ?>
                            <?= __d('lp', '回答を入れる。回答を入れる。回答を入れる。回答を入れる。') ?>
                        </div>
                    </div>
                </div><!--//panel-->

                <div class="panel">
                    <div class="panel-heading">
                        <h4 class="panel-title"><a data-parent="#accordion"
                                                   data-toggle="collapse" class="panel-toggle" href="#faq3"><i class="fa fa-plus-square"></i><?= __d('lp', '社員が期中で退職したらどうなりますか？') ?></a></h4>
                    </div>

                    <div class="panel-collapse collapse" id="faq3">
                        <div class="panel-body">
                            <?= __d('lp', '回答を入れる。回答を入れる。回答を入れる。回答を入れる。') ?>
                            <?= __d('lp', '回答を入れる。回答を入れる。回答を入れる。回答を入れる。') ?>
                        </div>
                    </div>
                </div><!--//panel-->

                <div class="panel">
                    <div class="panel-heading">
                        <h4 class="panel-title"><a data-parent="#accordion"
                                                   data-toggle="collapse" class="panel-toggle" href="#faq4"><i class="fa fa-plus-square"></i><?= __d('lp', 'セキュリティやバックアップはどうなっていますか？') ?></a></h4>
                    </div>

                    <div class="panel-collapse collapse" id="faq4">
                        <div class="panel-body">
                            <?= __d('lp', '回答を入れる。回答を入れる。回答を入れる。回答を入れる。') ?>
                            <?= __d('lp', '回答を入れる。回答を入れる。回答を入れる。回答を入れる。') ?>
                        </div>
                    </div>
                </div><!--//panel-->

                <div class="panel">
                    <div class="panel-heading">
                        <h4 class="panel-title"><a data-parent="#accordion"
                                                   data-toggle="collapse" class="panel-toggle" href="#faq5"><i class="fa fa-plus-square"></i><?= __d('lp', 'スマートフォン・タブレットのアプリはありますか？') ?></a></h4>
                    </div>

                    <div class="panel-collapse collapse" id="faq5">
                        <div class="panel-body">
                            <?= __d('lp', '回答を入れる。回答を入れる。回答を入れる。回答を入れる。') ?>
                            <?= __d('lp', '回答を入れる。回答を入れる。回答を入れる。回答を入れる。') ?>
                        </div>
                    </div>
                </div><!--//panel-->

                <div class="panel">
                    <div class="panel-heading">
                        <h4 class="panel-title"><a data-parent="#accordion"
                                                   data-toggle="collapse" class="panel-toggle" href="#faq6"><i class="fa fa-plus-square"></i><?= __d('lp', '企業担当者向けに詳しい説明をしてもらえますか？') ?></a></h4>
                    </div>

                    <div class="panel-collapse collapse" id="faq6">
                        <div class="panel-body">
                            <?= __d('lp', '回答を入れる。回答を入れる。回答を入れる。回答を入れる。') ?>
                            <?= __d('lp', '回答を入れる。回答を入れる。回答を入れる。回答を入れる。') ?>
                        </div>
                    </div>
                </div><!--//panel-->

            </div>
            <div class="col-md-6 col-sm-6 col-xs-12">
                <div class="panel">
                    <div class="panel-heading">
                        <h4 class="panel-title"><a data-parent="#accordion"
                                                   data-toggle="collapse" class="panel-toggle" href="#faq7"><i class="fa fa-plus-square"></i><?= __d('lp', '2016年8月31日の無料期間の後はどうなりますか？') ?></a></h4>
                    </div>

                    <div class="panel-collapse collapse" id="faq7">
                        <div class="panel-body">
                            <?= __d('lp', '自動継続になる？9月1日以降ログインできなくなる？やめたらデータが消える？放置するとどうなる？') ?>
                        </div>
                    </div>
                </div><!--//panel-->

                <div class="panel">
                    <div class="panel-heading">
                        <h4 class="panel-title"><a data-parent="#accordion"
                                                   data-toggle="collapse" class="panel-toggle" href="#faq8"><i class="fa fa-plus-square"></i><?= __d('lp', 'プラン変更時にデータは引き継がれますか？') ?></a></h4>
                    </div>

                    <div class="panel-collapse collapse" id="faq8">
                        <div class="panel-body">
                            <?= __d('lp', '回答を入れる。回答を入れる。回答を入れる。回答を入れる。') ?>
                            <?= __d('lp', '回答を入れる。回答を入れる。回答を入れる。回答を入れる。') ?>
                        </div>
                    </div>
                </div><!--//panel-->

                <div class="panel">
                    <div class="panel-heading">
                        <h4 class="panel-title"><a data-parent="#accordion"
                                                   data-toggle="collapse" class="panel-toggle" href="#faq9"><i class="fa fa-plus-square"></i><?= __d('lp', '機能のカスタマイズはできますか？') ?></a></h4>
                    </div>

                    <div class="panel-collapse collapse" id="faq9">
                        <div class="panel-body">
                            <?= __d('lp', '回答を入れる。回答を入れる。回答を入れる。回答を入れる。') ?>
                            <?= __d('lp', '回答を入れる。回答を入れる。回答を入れる。回答を入れる。') ?>
                        </div>
                    </div>
                </div><!--//panel-->

                <div class="panel">
                    <div class="panel-heading">
                        <h4 class="panel-title"><a data-parent="#accordion"
                                                   data-toggle="collapse" class="panel-toggle" href="#faq10"><i class="fa fa-plus-square"></i><?= __d('lp', '退会後に個人情報や機密情報はどうなりますか？') ?></a></h4>
                    </div>

                    <div class="panel-collapse collapse" id="faq10">
                        <div class="panel-body">
                            <?= __d('lp', '回答を入れる。回答を入れる。回答を入れる。回答を入れる。') ?>
                            <?= __d('lp', '回答を入れる。回答を入れる。回答を入れる。回答を入れる。') ?>
                        </div>
                    </div>
                </div><!--//panel-->

                <div class="panel">
                    <div class="panel-heading">
                        <h4 class="panel-title"><a data-parent="#accordion"
                                                   data-toggle="collapse" class="panel-toggle" href="#faq11"><i class="fa fa-plus-square"></i><?= __d('lp', '使い方がわからない場合、サポートはありますか？') ?></a></h4>
                    </div>

                    <div class="panel-collapse collapse" id="faq11">
                        <div class="panel-body">
                            <?= __d('lp', '回答を入れる。回答を入れる。回答を入れる。回答を入れる。') ?>
                            <?= __d('lp', '回答を入れる。回答を入れる。回答を入れる。回答を入れる。') ?>
                        </div>
                    </div>
                </div><!--//panel-->

                <div class="panel">
                    <div class="panel-heading">
                        <h4 class="panel-title"><a data-parent="#accordion"
                                                   data-toggle="collapse" class="panel-toggle" href="#faq12"><i class="fa fa-plus-square"></i><?= __d('lp', 'Goalousの運営会社はどこですか？？') ?></a></h4>
                    </div>

                    <div class="panel-collapse collapse" id="faq12">
                        <div class="panel-body">
                            <a class="more" href="http://www.isao.co.jp/" target="_blank"><?= __d('lp', '株式会社ISAO') ?></a><?= __d('lp', '（いさお）が企画・運営・開発全ておこなっています。') ?>
                        </div>
                    </div>
                </div><!--//panel-->


            </div>
        </div><!--//row-->
        <div class="more text-center">
            <h4 class="title"><?= __d('lp', 'その他のご質問はありますか？') ?></h4>
            <?= $this->Html->link( __d('lp', 'お問い合わせ'), array('controller' => 'contact'), array('class' => 'btn btn-cta btn-cta-secondary'));?>
        </div>
    </div><!--//container-->
</section><!--//faq-->

<div class="document">
    <div class="container text-center">
      <dl class="media col-md-6">
        <div class="media-left media-middle">
          <i class="fa fa-file-powerpoint-o document-fa"></i>
        </div>
        <div class="media-body">
          <dt class="bold-text">
            <?= __d('lp', '稟議書サンプル（PDFファイル / 2.8MB）') ?>
          </dt>
          <dd>
            <?= __d('lp', '社内稟議用のサンプル資料です。是非ご活用ください。') ?><br>
            <a href="#"><i class="fa fa-caret-right"></i><?= __d('lp', '資料ダウンロード') ?></a>
          </dd>
        </div>
      </dl>
    </div>
</div><!--//document-->

<?= $this->element('Homepage/signup') ?>
<!-- END app/View/Pages/home.ctp -->
