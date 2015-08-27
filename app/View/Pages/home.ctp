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
<!-- ******PROMO****** -->
<section id="promo" class="promo section">
    <div class="container intro">
        <h2 class="title">ニッポンのシゴトを変えるのは、<br/>チーム力向上のスパイラル</h2>

        <p class="summary">MBOってなんのため？目標と目的の違い、わかってる？<br/>はい、チームごっこ、おしまい。（テキストはダミーです）</p>
        <a class="btn btn-cta btn-cta-secondary" href="tour.html">詳しく見る</a>
    </div>
    <!--//intro-->

    <div class="fixed-container">
        <div class="signup">
            <div class="container text-center">
                <h3 class="title">Try Goalous Free</h3>

                <p class="summary">1チーム5人までは永年無料！今すぐお試しください。</p>
                <?= $this->Form->create('SubscribeEmail', [
                    'inputDefaults' => [
                        'div'       => 'form-group',
                        'wrapInput' => false,
                        'class'     => 'form-control',
                        'label'     => [
                            'class' => 'sr-only'
                        ],
                    ],
                    'class'         => 'signup-form',
                    'url'           => ['controller' => 'users', 'action' => 'add_subscribe_email'],
                ]); ?>
                <?= $this->Form->input('email', array(
                    'label'       => __d('lp', "Your email"),
                    'placeholder' => __d('lp', "メールアドレスを入力"),
                )); ?>
                <?= $this->Form->submit(__d('lp', "新規登録"),
                                        ['class' => 'btn btn-cta btn-cta-primary', 'div' => false]) ?>
                <p class="under-mail"><a href="">利用規約</a>をご確認のうえ、同意いただけましたら「新規登録」ボタンを押してください。</p>
                <?= $this->Form->end(); ?>
            </div>
            <!--//contianer-->
        </div>
        <!--//signup-->
        <div class="social text-center">
            <div class="container">
                <span class="line">Like Goalous? 世界を変える1シェアを！:</span>

                <!--//twitter tweet button code starts -->
                <div class="twitter-tweet">
                    <a href="https://twitter.com/goalous" class="twitter-share-button" data-via="3rdwave_media"
                       data-hashtags="bootstrap">Tweet</a>
                    <script>!function (d, s, id) {
                            var js, fjs = d.getElementsByTagName(s)[0], p = /^http:/.test(d.location) ? 'http' : 'https';
                            if (!d.getElementById(id)) {
                                js = d.createElement(s);
                                js.id = id;
                                js.src = p + '://platform.twitter.com/widgets.js';
                                fjs.parentNode.insertBefore(js, fjs);
                            }
                        }(document, 'script', 'twitter-wjs');</script>
                </div>
                <!--//twitter tweet button code ends -->

                <!--//facebook like button code starts-->
                <div class="fb-like" data-href="http://themes.3rdwavemedia.com/tempo/" data-layout="button_count"
                     data-action="like" data-show-faces="false" data-share="true"></div>
                <!--//facebook like button code ends-->

                <!--//hatena bookmark button code starts-->
                <div class="hatena-bookmark">
                    <a href="http://b.hatena.ne.jp/entry/http://www.goalous.com/" class="hatena-bookmark-button"
                       data-hatena-bookmark-title="Goalous ニッポンの仕事を変える、チーム力向上ツール"
                       data-hatena-bookmark-layout="simple-balloon" title="このエントリーをはてなブックマークに追加"><img
                            src="https://b.st-hatena.com/images/entry-button/button-only@2x.png"
                            alt="このエントリーをはてなブックマークに追加" width="20" height="20" style="border: none;"/></a>
                    <script type="text/javascript" src="https://b.st-hatena.com/js/bookmark_button.js" charset="utf-8"
                            async="async"></script>
                    <!--//hatena bookmark button code ends-->
                </div>
            </div>
        </div>
        <!--//social-->
    </div>
    <div class="bg-slider-wrapper">
        <div id="bg-slider" class="flexslider bg-slider">
            <ul class="slides">
                <li class="slide slide-1"></li>
                <li class="slide slide-2"></li>
                <li class="slide slide-3"></li>
            </ul>
        </div>
    </div>
    <!--//bg-slider-wrapper-->
</section><!--//promo-->

<!-- ******PRESS****** -->
<div class="press">
    <div class="container text-center">
        <div class="row">
            <ul class="list-unstyled">
                <li class="col-md-2 col-sm-4 col-xs-6"><a href="/Goalous_lp_mock/company-content.html"><img
                            class="img-responsive" src="/img/homepage/press/press-1.png" alt=""></a></li>
                <li class="col-md-2 col-sm-4 col-xs-6"><a href="/Goalous_lp_mock/company-content.html"><img
                            class="img-responsive" src="/img/homepage/press/press-2.png" alt=""></a></li>
                <li class="col-md-2 col-sm-4 col-xs-6 xs-break"><a href="/Goalous_lp_mock/company-content.html"><img
                            class="img-responsive" src="/img/homepage/press/press-3.png" alt=""></a></li>
                <li class="col-md-2 col-sm-4 col-xs-6 sm-break"><a href="/Goalous_lp_mock/company-content.html"><img
                            class="img-responsive" src="/img/homepage/press/press-4.png" alt=""></a></li>
                <li class="col-md-2 col-sm-4 col-xs-6 xs-break"><a href="/Goalous_lp_mock/company-content.html"><img
                            class="img-responsive" src="/img/homepage/press/press-5.png" alt=""></a></li>
                <li class="col-md-2 col-sm-4 col-xs-6"><a href="/Goalous_lp_mock/company-content.html"><img
                            class="img-responsive" src="/img/homepage/press/press-6.png" alt=""></a></li>
            </ul>
        </div>
    </div>
</div><!--//press-->

<!-- ******WHY****** -->
<section id="why" class="why section">
    <div class="container">
        <h2 class="title text-center">Goalousがシゴトを変える理由</h2>

        <p class="intro text-center">従業員数3人〜10,000人規模の会社まで、広く選ばれています。</p>

        <div class="row">
            <div class="benefits col-md-7 col-sm-6 col-xs-12">

                <div class="item clearfix">
                    <div class="icon col-md-3 col-xs-12 text-center">
                        <span class="pe-icon pe-7s-refresh-2"></span>
                    </div>
                    <!--//icon-->
                    <div class="content col-md-9 col-xs-12">
                        <h3 class="title">すべてのアクティビティをオープンに</h3>

                        <p class="desc">State a benefit of your product/services here. You can change the icon on the
                            left to any of the 500+ <a href="http://fortawesome.github.io/Font-Awesome/icons/"
                                                       target="_blank">FontAwesome icons</a> available. Maecenas
                            ultrices pellentesque nisi, eu volutpat nunc. </p>
                    </div>
                    <!--//content-->
                </div>
                <!--//item-->
                <div class="item clearfix">
                    <div class="icon col-md-3  col-xs-12 text-center">
                        <span class="pe-icon pe-7s-smile"></span>
                    </div>
                    <!--//icon-->
                    <div class="content col-md-9 col-xs-12">
                        <h3 class="title">いつでも共有、どこでもコミュニケーション</h3>

                        <p class="desc">State a benefit of your product/services here. You can change the icon on the
                            left to any of the 500+ <a href="http://fortawesome.github.io/Font-Awesome/icons/"
                                                       target="_blank">FontAwesome icons</a> available. Maecenas
                            ultrices pellentesque nisi, eu volutpat nunc. </p>
                    </div>
                    <!--//content-->
                </div>
                <!--//item-->
                <div class="item clearfix">
                    <div class="icon col-md-3  col-xs-12 text-center">
                        <span class="pe-icon pe-7s-users"></span>
                    </div>
                    <!--//icon-->
                    <div class="content col-md-9 col-xs-12">
                        <h3 class="title">オンラインで評価・コーチングを実現</h3>

                        <p class="desc">State a benefit of your product/services here. You can change the icon on the
                            left to any of the 500+ <a href="http://fortawesome.github.io/Font-Awesome/icons/"
                                                       target="_blank">FontAwesome icons</a> available. Maecenas
                            ultrices pellentesque nisi, eu volutpat nunc. </p>
                    </div>
                    <!--//content-->
                </div>
                <!--//item-->
                <div class="item clearfix">
                    <div class="icon col-md-3  col-xs-12 text-center">
                        <span class="pe-icon pe-7s-like2"></span>
                    </div>
                    <!--//icon-->
                    <div class="content col-md-9 col-xs-12">
                        <h3 class="title">褒め合い、励まし合い、熱くなる</h3>

                        <p class="desc">State a benefit of your product/services here. You can change the icon on the
                            left to any of the 500+ <a href="http://fortawesome.github.io/Font-Awesome/icons/"
                                                       target="_blank">FontAwesome icons</a> available. Maecenas
                            ultrices pellentesque nisi, eu volutpat nunc. Cras pharetra turpis pharetra iaculis euismod.
                        </p>
                    </div>
                    <!--//content-->
                </div>
                <!--//item-->
                <div class="item last clearfix">
                    <div class="icon col-md-3  col-xs-12 text-center">
                        <span class="pe-icon pe-7s-global"></span>
                    </div>
                    <!--//icon-->
                    <div class="content col-md-9 col-xs-12">
                        <h3 class="title">日英対応でグローバル企業をカバー</h3>

                        <p class="desc">State a benefit of your product/services here. You can change the icon on the
                            left to any of the 500+ <a href="http://fortawesome.github.io/Font-Awesome/icons/"
                                                       target="_blank">FontAwesome icons</a> available. Maecenas
                            ultrices pellentesque nisi, eu volutpat nunc. Pellentesque fermentum purus nec mi vulputate
                            interdum. Ut eu vulputate mi, nec imperdiet enim. Ut faucibus faucibus turpis et luctus.
                            Quisque bibendum tristique purus eu pulvinar. Cras pharetra turpis pharetra iaculis euismod.
                            Nullam ac ullamcorper turpis, quis tristique dui.</p>
                    </div>
                    <!--//content-->
                </div>
                <!--//item-->
                <div class="clearfix"></div>
                <div class="text-center">
                    <a class="btn btn-cta btn-cta-secondary" href="tour.html">詳しく見る</a>
                </div>
            </div>
            <div class="testimonials col-md-4 col-sm-5 col-md-offset-1 col-sm-offset-1 col-xs-12 col-xs-offset-0">
                <div class="item">
                    <div class="quote-box">
                        <blockquote class="quote">初めは、半信半疑だったんです。文化を変えることができるなんて。今では、Goalous無しのチーム運営は考えられません！
                        </blockquote>
                        <!--//quote-->
                        <p class="details">
                            <span class="name">苗字　名前</span>
                            <span class="title">株式会社ISAO, 東京</span>
                        </p>
                        <i class="fa fa-quote-right"></i>
                    </div>
                    <!--//quote-box-->
                    <div class="people text-center">
                        <img class="img-rounded user-pic" src="/img/homepage/people/people-1.png" alt="">
                    </div>
                    <!--//people-->
                </div>
                <!--//item-->
                <div class="item">
                    <div class="quote-box">
                        <blockquote class="quote">初めは、半信半疑だったんです。文化を変えることができるなんて。今では、Goalous無しのチーム運営は考えられません！
                        </blockquote>
                        <!--//quote-->
                        <p class="details">
                            <span class="name">苗字　名前</span>
                            <span class="title">株式会社ISAO, 東京</span>
                        </p>
                        <i class="fa fa-quote-right"></i>
                    </div>
                    <!--//quote-box-->
                    <div class="people text-center">
                        <img class="img-rounded user-pic" src="/img/homepage/people/people-2.png" alt="">
                    </div>
                    <!--//people-->
                </div>
                <!--//item-->
                <div class="item last">
                    <div class="quote-box">
                        <blockquote class="quote">初めは、半信半疑だったんです。文化を変えることができるなんて。今では、Goalous無しのチーム運営は考えられません！
                        </blockquote>
                        <!--//quote-->
                        <p class="details">
                            <span class="name">苗字　名前</span>
                            <span class="title">株式会社ISAO, 東京</span>
                        </p>
                        <i class="fa fa-quote-right"></i>
                    </div>
                    <!--//quote-box-->
                    <div class="people text-center">
                        <img class="img-rounded user-pic" src="/img/homepage/people/people-3.png" alt="">
                    </div>
                    <!--//people-->
                </div>
                <!--//item-->
            </div>
        </div>
        <!--//row-->
    </div>
    <!--//container-->
</section><!--//why-->

<!-- ******VIDEO****** -->
<section id="video" class="video section">
    <div class="container">
        <h2 class="title">簡単3ステップ！まずはチームを作りましょう。</h2>

        <p class="summary">If you have a promo video, you can put it in this block. Vitae dapibus elit viverra. Praesent
            ullamcorper dignissim arcu, at vulputate ligula suscipit eu.</p>

        <div class="control text-center">
            <button type="button" id="play-trigger" class="play-trigger" data-toggle="modal" data-target="#tour-video">
                <i class="fa fa-play"></i></button>
            <p>Watch Video</p>

            <!-- Video Modal -->
            <div class="modal modal-video" id="tour-video" tabindex="-1" role="dialog" aria-labelledby="videoModalLabel"
                 aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 id="videoModalLabel" class="modal-title">Video Tour</h4>
                        </div>
                        <div class="modal-body">
                            <div class="video-container">
                                <iframe id="vimeo-video"
                                        src="//player.vimeo.com/video/32424882?color=ffffff&amp;wmode=transparent"
                                        width="720" height="405" frameborder="0" webkitallowfullscreen
                                        mozallowfullscreen allowfullscreen></iframe>
                            </div>
                            <!--//video-container-->
                        </div>
                        <!--//modal-body-->
                    </div>
                    <!--//modal-content-->
                </div>
                <!--//modal-dialog-->
            </div>
            <!--//modal-->
        </div>
        <!--//control-->
    </div>
</section><!--//video-->

<!-- ******FAQ****** -->
<section id="faq" class="faq section has-bg-color">
    <div class="container">
        <h2 class="title text-center">よくあるご質問</h2>

        <div class="row">
            <div class="col-md-6 col-sm-6 col-xs-12">
                <div class="panel">
                    <div class="panel-heading">
                        <h4 class="panel-title"><a data-parent="#accordion"
                                                   data-toggle="collapse" class="panel-toggle" href="#faq1"><i
                                    class="fa fa-plus-square"></i>Can I viverra sit amet quam eget lacinia?</a></h4>
                    </div>

                    <div class="panel-collapse collapse" id="faq1">
                        <div class="panel-body">
                            Anim pariatur cliche reprehenderit, enim eiusmod high life
                            accusamus terry richardson ad squid. 3 wolf moon officia
                            aute, non cupidatat skateboard dolor brunch. Food truck
                            quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor,
                            sunt aliqua put a bird on it squid single-origin coffee
                            nulla assumenda shoreditch et. Nihil anim keffiyeh
                            helvetica, craft beer labore wes anderson cred nesciunt
                            sapiente ea proident. Ad vegan excepteur butcher vice lomo.
                            Leggings occaecat craft beer farm-to-table, raw denim
                            aesthetic synth nesciunt you probably haven't heard of them
                            accusamus labore sustainable VHS.
                        </div>
                    </div>
                </div>
                <!--//panel-->

                <div class="panel">
                    <div class="panel-heading">
                        <h4 class="panel-title"><a data-parent="#accordion"
                                                   data-toggle="collapse" class="panel-toggle" href="#faq2"><i
                                    class="fa fa-plus-square"></i>What is the ipsum dolor sit amet quam tortor?</a></h4>
                    </div>

                    <div class="panel-collapse collapse" id="faq2">
                        <div class="panel-body">
                            Anim pariatur cliche reprehenderit, enim eiusmod high life
                            accusamus terry richardson ad squid. 3 wolf moon officia
                            aute, non cupidatat skateboard dolor brunch. Food truck
                            quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor,
                            sunt aliqua put a bird on it squid single-origin coffee
                            nulla assumenda shoreditch et. Nihil anim keffiyeh
                            helvetica, craft beer labore wes anderson cred nesciunt
                            sapiente ea proident. Ad vegan excepteur butcher vice lomo.
                            Leggings occaecat craft beer farm-to-table, raw denim
                            aesthetic synth nesciunt you probably haven't heard of them
                            accusamus labore sustainable VHS.
                        </div>
                    </div>
                </div>
                <!--//panel-->

                <div class="panel">
                    <div class="panel-heading">
                        <h4 class="panel-title"><a data-parent="#accordion"
                                                   data-toggle="collapse" class="panel-toggle" href="#faq3"><i
                                    class="fa fa-plus-square"></i>How does the morbi quam tortor work?</a></h4>
                    </div>

                    <div class="panel-collapse collapse" id="faq3">
                        <div class="panel-body">
                            Anim pariatur cliche reprehenderit, enim eiusmod high life
                            accusamus terry richardson ad squid. 3 wolf moon officia
                            aute, non cupidatat skateboard dolor brunch. Food truck
                            quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor,
                            sunt aliqua put a bird on it squid single-origin coffee
                            nulla assumenda shoreditch et. Nihil anim keffiyeh
                            helvetica, craft beer labore wes anderson cred nesciunt
                            sapiente ea proident. Ad vegan excepteur butcher vice lomo.
                            Leggings occaecat craft beer farm-to-table, raw denim
                            aesthetic synth nesciunt you probably haven't heard of them
                            accusamus labore sustainable VHS.
                        </div>
                    </div>
                </div>
                <!--//panel-->

                <div class="panel">
                    <div class="panel-heading">
                        <h4 class="panel-title"><a data-parent="#accordion"
                                                   data-toggle="collapse" class="panel-toggle" href="#faq4"><i
                                    class="fa fa-plus-square"></i>Can I ipsum dolor sit amet nascetur ridiculus?</a>
                        </h4>
                    </div>

                    <div class="panel-collapse collapse" id="faq4">
                        <div class="panel-body">
                            Anim pariatur cliche reprehenderit, enim eiusmod high life
                            accusamus terry richardson ad squid. 3 wolf moon officia
                            aute, non cupidatat skateboard dolor brunch. Food truck
                            quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor,
                            sunt aliqua put a bird on it squid single-origin coffee
                            nulla assumenda shoreditch et. Nihil anim keffiyeh
                            helvetica, craft beer labore wes anderson cred nesciunt
                            sapiente ea proident. Ad vegan excepteur butcher vice lomo.
                            Leggings occaecat craft beer farm-to-table, raw denim
                            aesthetic synth nesciunt you probably haven't heard of them
                            accusamus labore sustainable VHS.
                        </div>
                    </div>
                </div>
                <!--//panel-->
            </div>
            <div class="col-md-6 col-sm-6 col-xs-12">
                <div class="panel">
                    <div class="panel-heading">
                        <h4 class="panel-title"><a data-parent="#accordion"
                                                   data-toggle="collapse" class="panel-toggle" href="#faq5"><i
                                    class="fa fa-plus-square"></i>Is it possible to tellus eget auctor condimentum?</a>
                        </h4>
                    </div>

                    <div class="panel-collapse collapse" id="faq5">
                        <div class="panel-body">
                            Anim pariatur cliche reprehenderit, enim eiusmod high life
                            accusamus terry richardson ad squid. 3 wolf moon officia
                            aute, non cupidatat skateboard dolor brunch. Food truck
                            quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor,
                            sunt aliqua put a bird on it squid single-origin coffee
                            nulla assumenda shoreditch et. Nihil anim keffiyeh
                            helvetica, craft beer labore wes anderson cred nesciunt
                            sapiente ea proident. Ad vegan excepteur butcher vice lomo.
                            Leggings occaecat craft beer farm-to-table, raw denim
                            aesthetic synth nesciunt you probably haven't heard of them
                            accusamus labore sustainable VHS.
                        </div>
                    </div>
                </div>
                <!--//panel-->

                <div class="panel">
                    <div class="panel-heading">
                        <h4 class="panel-title"><a data-parent="#accordion"
                                                   data-toggle="collapse" class="panel-toggle" href="#faq6"><i
                                    class="fa fa-plus-square"></i>Would it elementum turpis semper imperdiet?</a></h4>
                    </div>

                    <div class="panel-collapse collapse" id="faq6">
                        <div class="panel-body">
                            Anim pariatur cliche reprehenderit, enim eiusmod high life
                            accusamus terry richardson ad squid. 3 wolf moon officia
                            aute, non cupidatat skateboard dolor brunch. Food truck
                            quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor,
                            sunt aliqua put a bird on it squid single-origin coffee
                            nulla assumenda shoreditch et. Nihil anim keffiyeh
                            helvetica, craft beer labore wes anderson cred nesciunt
                            sapiente ea proident. Ad vegan excepteur butcher vice lomo.
                            Leggings occaecat craft beer farm-to-table, raw denim
                            aesthetic synth nesciunt you probably haven't heard of them
                            accusamus labore sustainable VHS.
                        </div>
                    </div>
                </div>
                <!--//panel-->

                <div class="panel">
                    <div class="panel-heading">
                        <h4 class="panel-title"><a data-parent="#accordion"
                                                   data-toggle="collapse" class="panel-toggle" href="#faq7"><i
                                    class="fa fa-plus-square"></i>How can I imperdiet lorem sem non nisl?</a></h4>
                    </div>

                    <div class="panel-collapse collapse" id="faq7">
                        <div class="panel-body">
                            Anim pariatur cliche reprehenderit, enim eiusmod high life
                            accusamus terry richardson ad squid. 3 wolf moon officia
                            aute, non cupidatat skateboard dolor brunch. Food truck
                            quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor,
                            sunt aliqua put a bird on it squid single-origin coffee
                            nulla assumenda shoreditch et. Nihil anim keffiyeh
                            helvetica, craft beer labore wes anderson cred nesciunt
                            sapiente ea proident. Ad vegan excepteur butcher vice lomo.
                            Leggings occaecat craft beer farm-to-table, raw denim
                            aesthetic synth nesciunt you probably haven't heard of them
                            accusamus labore sustainable VHS.
                        </div>
                    </div>
                </div>
                <!--//panel-->

                <div class="panel">
                    <div class="panel-heading">
                        <h4 class="panel-title"><a data-parent="#accordion"
                                                   data-toggle="collapse" class="panel-toggle" href="#faq8"><i
                                    class="fa fa-plus-square"></i>Can I imperdiet massa ut?</a></h4>
                    </div>

                    <div class="panel-collapse collapse" id="faq8">
                        <div class="panel-body">
                            Anim pariatur cliche reprehenderit, enim eiusmod high life
                            accusamus terry richardson ad squid. 3 wolf moon officia
                            aute, non cupidatat skateboard dolor brunch. Food truck
                            quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor,
                            sunt aliqua put a bird on it squid single-origin coffee
                            nulla assumenda shoreditch et. Nihil anim keffiyeh
                            helvetica, craft beer labore wes anderson cred nesciunt
                            sapiente ea proident. Ad vegan excepteur butcher vice lomo.
                            Leggings occaecat craft beer farm-to-table, raw denim
                            aesthetic synth nesciunt you probably haven't heard of them
                            accusamus labore sustainable VHS.
                        </div>
                    </div>
                </div>
                <!--//panel-->
            </div>
        </div>
        <!--//row-->
        <div class="more text-center">
            <h4 class="title">その他のご質問はありますか？</h4>
            <a class="btn btn-cta btn-cta-secondary" href="contact.html">お問い合わせ</a>
        </div>
    </div>
    <!--//container-->
</section><!--//faq-->

<!-- ******SIGNUP****** -->
<section id="signup" class="signup">
    <div class="container text-center">
        <h2 class="title">さぁ、Goalous Teamへ！</h2>

        <p class="summary">1チーム5人までは永年無料！今すぐお試しください。</p>

        <form class="signup-form" method="post" action="#">
            <div class="form-group">
                <label class="sr-only" for="semail2">Your email</label>
                <input type="email" id="semail2" name="semail2" class="form-control" placeholder="メールアドレスを入力する"
                       required>
            </div>
            <button type="submit" class="btn btn-cta btn-cta-primary">新規登録</button>
        </form>
        <!--//signup-form-->
    </div>
</section><!--//signup-->
<!-- END app/View/Pages/home.ctp -->
