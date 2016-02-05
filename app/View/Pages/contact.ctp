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
 * @var CodeCompletionView $this
 * @var
 * @var
 */
?>
<?php $this->append('meta') ?>
<link rel="alternate" hreflang="ja" href="<?= $this->Html->url('/ja/contact') ?>"/>
<link rel="alternate" hreflang="en" href="<?= $this->Html->url('/en/contact') ?>"/>
<link rel="alternate" hreflang="x-default" href="<?= $this->Html->url('/contact') ?>"/>
<?php $this->end() ?>
<!-- START app/View/Pages/contact.ctp -->
<!-- ******CONTACT MAIN****** -->
<section id="contact-main" class="contact-main section">
    <div class="container text-center">
        <h2 class="title"><?= __d('lp', 'Goalousに関するお問い合わせ') ?></h2>
        <p class="intro">
            <?=
            __d('lp', 'Goalousは、”世界のシゴトをたのしくするビジョナリーハンサム”である') .
            $this->Html->link(__d('lp', '株式会社ISAO'), 'http://www.isao.co.jp/',
                              array('target' => '_blank', 'class' => 'more')) .
            __d('lp', 'が運営しております。') .
            '<br>' .
            __d('lp', 'なんでも、お気軽にご相談ください。');
            ?>
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
        $this->Form->create('Email', [
            'id'            => 'contact-form',
            'class'         => 'form',
            'inputDefaults' => ['div' => null, 'wrapInput' => false, 'class' => 'form-control', 'label' => false, 'error' => false],
            'novalidate'    => true
        ]); ?>
        <div class="contact-form col-md-8 col-sm-12 col-xs-12 col-md-offset-2">
            <div class="form-group want">
                <?= $this->Form->select('want',
                                        array(
                                            null                 => __d('lp', '選択してください'),
                                            __d('lp', '詳しく知りたい') => __d('lp', '詳しく知りたい'),
                                            __d('lp', '資料がほしい')  => __d('lp', '資料がほしい'),
                                            __d('lp', '協業したい')   => __d('lp', '協業したい'),
                                            __d('lp', '取材したい')   => __d('lp', '取材したい'),
                                            __d('lp', 'その他')     => __d('lp', 'その他'),
                                        ),
                                        array(
                                            'class' => 'form-control',
                                            'empty' => false,
                                            'value' => 0,
                                        )
                ); ?>
                <?= $this->Form->error('want', null, ['class' => 'help-block text-danger pull-left']) ?>
            </div><!-- //form-group -->
            <div class="form-group company">
                <label class="sr-only" for="company">
                    <?= __d('lp', 'company') ?>
                </label>
                <?= $this->Form->input('company', ['placeholder' => __d('lp', '会社名')]) ?>
                <?= $this->Form->error('company', null, ['class' => 'help-block text-danger pull-left']) ?>
            </div><!--//form-group-->
            <div class="form-group name">
                <label class="sr-only" for="name">
                    <?= __d('lp', 'name') ?>
                </label>
                <?= $this->Form->input('name', ['placeholder' => __d('lp', 'お名前') . ' *']) ?>
                <?= $this->Form->error('name', null, ['class' => 'help-block text-danger pull-left']) ?>
            </div><!--//form-group-->
            <div class="form-group email">
                <label class="sr-only" for="email">
                    <?= __d('lp', 'email') ?>
                </label>
                <?= $this->Form->input('email', ['type' => 'email', 'placeholder' => __d('lp',
                                                                                         'メールアドレス') . ' *']) ?>
                <?= $this->Form->error('email', null, ['class' => 'help-block text-danger pull-left']) ?>
            </div><!--//form-group-->
            <div class="form-group message">
                <label class="sr-only" for="message">
                    <?= __d('lp', 'message') ?>
                </label>
                <?= $this->Form->input('message', ['type' => 'text', 'rows' => 8, 'placeholder' => __d('lp',
                                                                                                       'お問い合わせ内容') . ' *']) ?>
                <?= $this->Form->error('message', null, ['class' => 'help-block text-danger pull-left']) ?>
            </div><!--//form-group-->
        </div>

        <div class="contact-form col-md-10 col-md-offset-1 col-sm-12 col-xs-12 text-center">
            <p class="intro">
                <?= __d('lp', 'ご希望の営業担当者がいれば、リクエストください。（複数選択可）') ?>
            </p>

            <div class="form-group sales text-left">

                <label class="col-md-4 col-sm-6 col-xs-12 salesperson">
                    <div class="input-group">
                            <span class="input-group-addon">
                                <input type="checkbox" value="">
                            </span>
                        <div class="media">
                            <div class="media-left media-middle">
                                <?= $this->Html->image('homepage/people/sales1.jpg',
                                                       array('alt' => 'photo', 'width' => '60', 'height' => '60', 'class' => 'img-circle')); ?>
                            </div>
                            <div class="media-body media-middle">
                                <?=
                                __d('lp', '湯川啓太') .
                                '<br>' .
                                __d('lp', '唐沢寿明に似てます')
                                ?>
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
                                <?= $this->Html->image('homepage/people/sales2.jpeg',
                                                       array('alt' => 'photo', 'width' => '60', 'height' => '60', 'class' => 'img-circle')); ?>
                            </div>
                            <div class="media-body media-middle">
                                <?=
                                __d('lp', '菊池厚平') .
                                '<br>' .
                                __d('lp', 'Goalousのオーナーです')
                                ?>
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
                                <?= $this->Html->image('homepage/people/sales3.jpg',
                                                       array('alt' => 'photo', 'width' => '60', 'height' => '60', 'class' => 'img-circle')); ?>
                            </div>
                            <div class="media-body media-middle">
                                <?=
                                __d('lp', '吉岡真人') .
                                '<br>' .
                                __d('lp', '唐沢寿明に似てます')
                                ?>
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
                                <?= $this->Html->image('homepage/people/sales4.jpeg',
                                                       array('alt' => 'photo', 'width' => '60', 'height' => '60', 'class' => 'img-circle')); ?>
                            </div>
                            <div class="media-body media-middle">
                                <?=
                                __d('lp', '石原裕介') .
                                '<br>' .
                                __d('lp', 'DJやってます')
                                ?>
                            </div>
                        </div>
                    </div><!-- /input-group -->
                </label><!-- /.col-md-4 col-sm-6 col-xs-12 -->
            </div><!--//form-group-->
        </div>
        <div class="contact-form col-md-8 col-sm-12 col-xs-12 col-md-offset-2">
            <div class="checkbox">
                <label>
                    <?= $this->Form->checkbox('need') ?>
                    <?= __d('lp', '個人情報の取り扱いについてに同意の上、問い合わせする') ?>
                </label>
                <?= $this->Form->error('need', __d('lp', '個人情報保護方針の同意が必要です。'), ['class' => 'help-block text-danger']) ?>
            </div>
            <p>
                <?= $this->Form->submit(__d('lp', '確認画面へ'), ['class' => 'btn btn-block btn-cta-primary']) ?>
            </p>
        </div><!--//contact-form-->

        <?= $this->Form->end() ?>
        <!--//form-->
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
                        <h4><?= __d('lp', 'Email') ?></h4>
                        <p>
                            <?= $this->html->link(__d('lp', 'Email'),
                                                  'mailto:contact@goalous.com?subject=' .
                                                  __d('lp', 'Goalousに関するお問い合わせ')
                            );
                            ?>
                        </p>
                    </div><!--details-->
                </div><!--//item-inner-->
            </div><!--//item-->
            <div class="item col-md-4 col-sm-12 col-xs-12">
                <div class="item-inner">
                    <a href="https://twitter.com/goalous" target="_blank">
                        <div class="icon">
                            <span class="fa fa-twitter"></span>
                        </div>
                    </a>
                    <div class="details">
                        <!-- なんで小文字はじまり？ -->
                        <h4><?= __d('lp', 'twitter') ?></h4>
                        <p>
                            <?= $this->Html->link('@goalous', 'https://twitter.com/goalous',
                                                  array('target' => '_blank')) ?>
                        </p>
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
                        <h4><?= __d('lp', 'Facebookページ') ?></h4>
                        <p>
                            <?= $this->Html->link('Goalous', 'https://www.facebook.com/goalous',
                                                  array('target' => '_blank')) ?>
                        </p>
                    </div><!--details-->
                </div><!--//item-inner-->
            </div><!--//item-->
        </div><!--//row-->
    </div><!--//container-->
</section><!--//contact-->
<!-- END app/View/Pages/contact.ctp -->
