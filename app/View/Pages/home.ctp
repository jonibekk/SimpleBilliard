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
        <h2 class="title"><?= __d('lp', 'チーム力で、飛べ。') ?></h2>

        <p class="summary"><?= __d('lp', 'Goalous(ゴーラス)正式版がついに今秋登場！') ?><br/><?= __d('lp', 'チーム向上のためのSNS。はい、決定版。') ?>
        </p>
    </div>
    <!--//intro-->

    <div class="fixed-container">
        <div class="signup">
            <div class="container text-center">
                <h3 class="title"><?= __d('lp', '知らせたい。Goalousのこと。') ?></h3>

                <p class="summary"><?= __d('lp', 'メールアドレス登録で情報をゲット！') ?></p>
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
                <?= $this->Form->submit(__d('lp', "登録する"),
                                        ['class' => 'btn btn-cta btn-cta-primary', 'div' => false]) ?>
                <p class="under-mail">
                    <?php
                    $terms = "<a href='#modal-tos' data-toggle='modal'>" . __d('gl', "利用規約") . "</a>";
                    $pp = "<a href='#modal-pp' data-toggle='modal'>" . __d('gl', "プライバシーポリシー") . "</a>";

                    ?>
                    <?= __d('lp', '%1$s と %2$s をご確認のうえ、同意いただけましたら「新規登録」ボタンを押してください。', $terms, $pp) ?></p>
                <?= $this->Form->end(); ?>
            </div>
            <!--//contianer-->
        </div>
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
<!-- END app/View/Pages/home.ctp -->
