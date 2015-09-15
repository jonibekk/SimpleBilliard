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
