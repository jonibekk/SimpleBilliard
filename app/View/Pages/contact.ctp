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
<link rel="alternate" hreflang="ja" href="<?= $this->Html->url('/ja/contact') ?>"/>
<link rel="alternate" hreflang="en" href="<?= $this->Html->url('/en/contact') ?>"/>
<link rel="alternate" hreflang="x-default" href="<?= $this->Html->url('/contact') ?>"/>
<?php $this->end() ?>
<!-- START app/View/Pages/contact.ctp -->

<section class="container">
    <?=
    $this->Form->create('Email', [
        'inputDefaults' => ['div' => null, 'wrapInput' => false, 'class' => null,],
        'novalidate'    => true
    ]); ?>
    <?= $this->Form->input('contact_menu', ['label' => 'contact_menu']) ?>
    <?= $this->Form->input('company_name', ['label' => 'company_name']) ?>
    <?= $this->Form->input('user_name', ['label' => 'user_name']) ?>
    <?= $this->Form->input('email', ['label' => 'email']) ?>
    <?= $this->Form->input('body', ['label' => 'body']) ?>
    <?= $this->Form->input('representatives.0', ['label' => 'representatives']) ?>
    <?= $this->Form->input('accept_privacy_policy', ['label' => 'accept_privacy_policy']) ?>
    <?= $this->Form->submit('送信') ?>
    <?= $this->Form->end() ?>

</section>
<section id="contact-main" class="contact-main section">
    <div class="container text-center">
        <h2 class="title">Goalousに関するお問い合わせ</h2>
        <p class="intro">
            Goalousは、”世界のシゴトをたのしくするビジョナリーカンパニー”である<a class="more" href="http://www.isao.co.jp/"
                                                     target="_blank">株式会社ISAO</a>が運営しております。<br>
            なんでも、お気軽にご相談ください。
        </p>

        <!--h3>お問い合わせフォーム</h3>
        <p class="intro">
          1〜2営業日を目処に担当者よりご回答いたします。<br>
          3営業日以内に回答がない場合には、大変お手数ですがcontact@goalous.comまでご連絡下さい。
        </p-->
    </div>
</section>

<section class="container contact-form-section">
    <div class="row text-center">
        <?=
        $this->Form->create('Contact', [
            'inputDefaults' => array(
                'div'       => null,
                'wrapInput' => false,
                'class'     => null,
            ),
            'class'         => null,
            'novalidate'    => true
        ]); ?>
        <form class="form" id="contact-form" method="post" action="#">
            <div class="contact-form col-md-8 col-sm-12 col-xs-12 col-md-offset-2">
                <div class="form-group want">
                    <label class="checkbox-inline"><input type="checkbox" name="want" value="1">詳しく知りたい</label>
                    <label class="checkbox-inline"><input type="checkbox" name="want" value="2">資料がほしい</label>
                    <label class="checkbox-inline"><input type="checkbox" name="want" value="3">協業したい</label>
                    <label class="checkbox-inline"><input type="checkbox" name="want" value="4">取材したい</label>
                    <label class="checkbox-inline"><input type="checkbox" name="want" value="5">その他</label>
                </div><!--//form-group-->
                <div class="form-group company">
                    <label class="sr-only" for="company">company</label>
                    <input id="company" name="company" type="text" class="form-control" placeholder="会社名">
                </div><!--//form-group-->
                <div class="form-group name">
                    <label class="sr-only" for="name">name</label>
                    <input id="name" name="name" type="text" class="form-control" placeholder="お名前*">
                </div><!--//form-group-->
                <div class="form-group email">
                    <label class="sr-only" for="email">email</label>
                    <input id="email" name="email" type="email" class="form-control" placeholder="メールアドレス*">
                </div><!--//form-group-->
                <div class="form-group message">
                    <label class="sr-only" for="message">message</label>
                    <textarea id="message" name="message" class="form-control" rows="8"
                              placeholder="お問い合わせ内容*"></textarea>
                </div><!--//form-group-->
            </div>

            <div class="contact-form col-md-10 col-md-offset-1 col-sm-12 col-xs-12 text-center">
                <p class="intro">ご希望の営業担当者がいれば、リクエストください。（複数選択可）</p>

                <div class="form-group sales text-left">


                    <label class="col-md-4 col-sm-6 col-xs-12 salesperson">
                        <div class="input-group">
                                  <span class="input-group-addon">
                                    <input type="checkbox" value="">
                                  </span>
                            <div class="media">
                                <div class="media-left media-middle">
                                    <img class="img-circle" src="assets/images/people/sales1.jpg" alt="photo"
                                         width="60px" height="60px">
                                </div>
                                <div class="media-body media-middle">
                                    湯川啓太
                                    <br>唐沢寿明に似てます
                                </div>
                            </div>
                        </div><!-- /input-group -->
                    </label><!-- /.col-md-4 col-sm-6 col-xs-12 -->
                    <label class="col-md-4 col-sm-6 col-xs-12 salesperson">
                        <div class="input-group">
                                  <span class="input-group-addon">
                                    <input type="checkbox" value="">
                                  </span>
                            <div class="media">
                                <div class="media-left media-middle">
                                    <img class="img-circle" src="assets/images/people/sales2.jpeg" alt="photo"
                                         width="60px" height="60px">
                                </div>
                                <div class="media-body media-middle">
                                    菊池厚平
                                    <br>Goalousのオーナーです
                                </div>
                            </div>
                        </div><!-- /input-group -->
                    </label><!-- /.col-md-4 col-sm-6 col-xs-12 -->
                    <label class="col-md-4 col-sm-6 col-xs-12 salesperson">
                        <div class="input-group">
                                  <span class="input-group-addon">
                                    <input type="checkbox" value="">
                                  </span>
                            <div class="media">
                                <div class="media-left media-middle">
                                    <img class="img-circle" src="assets/images/people/sales3.jpg" alt="photo"
                                         width="60px" height="60px">
                                </div>
                                <div class="media-body media-middle">
                                    吉岡真人
                                    <br>愛をこめた営業をします
                                </div>
                            </div>
                        </div><!-- /input-group -->
                    </label><!-- /.col-md-4 col-sm-6 col-xs-12 -->
                    <label class="col-md-4 col-sm-6 col-xs-12 salesperson">
                        <div class="input-group">
                                  <span class="input-group-addon">
                                    <input type="checkbox" value="">
                                  </span>
                            <div class="media">
                                <div class="media-left media-middle">
                                    <img class="img-circle" src="assets/images/people/sales4.jpeg" alt="photo"
                                         width="60px" height="60px">
                                </div>
                                <div class="media-body media-middle">
                                    石原裕介
                                    <br>DJやってます
                                </div>
                            </div>
                        </div><!-- /input-group -->
                    </label><!-- /.col-md-4 col-sm-6 col-xs-12 -->


                </div><!--//form-group-->
            </div>

            <div class="contact-form col-md-8 col-sm-12 col-xs-12 col-md-offset-2">
                <div class="checkbox">
                    <label><input type="checkbox" name="need" value="true">個人情報の取り扱いについてに同意の上、問い合わせする</label>
                </div>
                <p>
                    <button type="submit" class="btn btn-block btn-cta-primary">確認画面へ</button>
                </p>
            </div><!--//contact-form-->
        </form><!--//form-->
    </div><!--//row-->
</section>

<section id="contact-main" class="contact-main section">
    <div class="container text-center">
        <div class="row">
            <div class="item col-md-4 col-sm-12 col-xs-12">
                <div class="item-inner">
                    <a href="mailto:contact@goalous.com?subject=Goalousに関するお問い合わせ">
                        <div class="icon">
                            <!--<i class="fa fa-envelope"></i>-->
                            <span class="pe-icon pe-7s-mail-open-file"></span>
                        </div>
                    </a>
                    <div class="details">
                        <h4>Email</h4>
                        <p><a href="mailto:contact@goalous.com?subject=Goalousに関するお問い合わせ">contact@goalous.com</a></p>
                    </div><!--details-->
                </div><!--//item-inner-->
            </div><!--//item-->
            <div class="item col-md-4 col-sm-12 col-xs-12">
                <div class="item-inner">
                    <a href="https://twitter.com/goalous" target="_blank">
                        <div class="icon">
                            <!--<i class="fa fa-phone"></i>-->
                            <span class="fa fa-twitter"></span>
                        </div>
                    </a>
                    <div class="details">
                        <h4>twitter</h4>
                        <p><a href="https://twitter.com/goalous" target="_blank">@goalous</a></p>
                    </div><!--details-->
                </div><!--//item-inner-->
            </div><!--//item-->
            <div class="item col-md-4 col-sm-12 col-xs-12 last">
                <div class="item-inner">
                    <a href="https://www.facebook.com/goalous" target="_blank">
                        <div class="icon">
                            <!--<i class="fa fa-map-marker"></i>-->
                            <span class="pe-icon pe-7s-map-2"></span>
                        </div>
                    </a>
                    <div class="details">
                        <h4>Facebookページ</h4>
                        <p><a href="https://www.facebook.com/goalous" target="_blank">Goalous</a></p>
                    </div><!--details-->
                </div><!--//item-inner-->
            </div><!--//item-->
        </div><!--//row-->
    </div><!--//container-->
</section><!--//contact-->
<!-- END app/View/Pages/contact.ctp -->
