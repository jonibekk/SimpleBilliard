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
<section id="contact-promo" class="contact-promo section">
    <div class="bg-mask"></div>
    <div class="container">
        <div class="row">
            <div class="contact-intro col-xs-12 text-center">
                <h2 class="title"><?= __d('lp', 'Goalousに関するお問い合わせ') ?></h2>
                <p class="contact-list">
                    <?=
                    __d('lp', 'Goalousは、”世界のシゴトをたのしくするビジョナリーカンパニー”である株式会社ISAOが運営しております。') .
                    '<br>' .
                    __d('lp', 'なんでも、お気軽にご相談ください。');
                    ?>
                </p>
            </div>
        </div>
    </div>
</section>

<section class="container contact-form-section">
    <div class="row">
        <?=
        $this->Form->create('Email', [
            'id'            => 'contact-form',
            'class'         => 'form',
            'inputDefaults' => ['div' => null, 'wrapInput' => false, 'class' => null, 'error' => false],
            'novalidate'    => true
        ]); ?>
        <div class="contact-form col-md-8 col-xs-12 col-md-offset-2">
            <div class="form-group name">
                <span class="label label-danger">必須</span>
                <?= $this->Form->input('name', [
                    'placeholder' => __d('lp', '例）鈴木 いさお'),
                    'id' => 'EmailName',
                    'class' => 'form-control lp-contact-form-control',
                    'label' => [
                        'text' => __d( 'lp', 'お名前'),
                        'class' => 'control-label lp-contact-control-label',
                    ],
                    ]) ?>
                    <?= $this->Form->error('name', null, ['class' => 'contact-error-msg-block']) ?>
            </div>
            <div class="form-group email">
                <span class="label label-danger">必須</span>
                <?= $this->Form->input('email', [
                        'placeholder' => __d('lp', '例）example@goalous.com（半角英数字）'),
                        'id' => 'email',
                        'class' => 'form-control lp-contact-form-control',
                        'label' => [
                            'text' => __d('lp', 'メールアドレス'),
                            'class' => 'control-label lp-contact-control-label',
                        ],
                    ]) ?>
                    <?= $this->Form->error('email', null, ['class' => 'contact-error-msg-block']) ?>
            </div>
            <div class="form-group company">
                <?= $this->Form->input('company', [
                    'placeholder' => __d('lp', '例）株式会社ISAO'),
                    'id' => 'company',
                    'class' => 'form-control lp-contact-form-control',
                    'label' => [
                        'text' => __d('lp', '会社名・団体名など'),
                        'class' => 'control-label lp-contact-control-label',
                    ],
                ])
                ?>
                <?= $this->Form->error('company', null, ['class' => 'contact-error-msg-block']) ?>
            </div>
            <div class="form-group want">
                <span class="label label-danger">必須</span>
                <?=
                    $this->Form->input('want', [
                    'options' => $type_options, // PagesController - ln.184,
                    'value' => $selected_type,
                    'label' => [
                        'text' => __d('lp', 'お問い合わせ項目'),
                        'class' => 'control-label lp-contact-control-label',
                        'empty' => false,
                    ],
                    'class' => 'form-control lp-contact-form-control',
                ]); ?>
                <?= $this->Form->error('want', null, ['class' => 'contact-error-msg-block']) ?>
            </div>
            <div class="form-group messsage">
                <span class="label label-danger">必須</span>
                <?=
                    $this->Form->input('message', [
                        'class' => 'form-control lp-contact-form-control',
                        'type' => 'text',
                        'rows' => 8,
                        'placeholder' => __d('lp', '例）導入を希望しています。詳しく説明に来て欲しいです。'),
                        'label' => [
                            'text' => __d('lp', 'お問い合わせ内容（最大3,000文字）'),
                            'class' => 'control-label lp-contact-control-label',
                        ],
                    ]);
                ?>
                <?= $this->Form->error('message', null, ['class' => 'contact-error-msg-block']) ?>
            </div>
        </div>

        <div class="contact-form col-md-8 col-xs-12 col-md-offset-2 text-center">
            <label class="control-label"><?= __d('lp', 'ご希望の営業担当者がいれば、リクエストください。（複数選択可）') ?></label>
            <? $this->Form->unlockField('sales_people') ?>
            <div class="form-group sales text-left">

                <label class="col-sm-6 col-xs-12 salesperson">
                    <div class="input-group">
                            <span class="input-group-addon">
                                <?= $this->Form->checkbox('sales_people.',
                                                          ['value'   => __d('lp', '湯川啓太'), 'hiddenField' => false,
                                                           'checked' => isset($this->request->data['Email']['sales_people']) &&
                                                           in_array(__d('lp', '湯川啓太'),
                                                                    $this->request->data['Email']['sales_people']) ? 'checked' : null
                                                          ]) ?>
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
                </label><!-- /.col-sm-6 col-xs-12 -->
                <label class="col-sm-6 col-xs-12 salesperson">
                    <div class="input-group">
                            <span class="input-group-addon">
                                <?= $this->Form->checkbox('sales_people.',
                                                          ['value'   => __d('lp', '菊池厚平'), 'hiddenField' => false,
                                                           'checked' => isset($this->request->data['Email']['sales_people']) &&
                                                           in_array(__d('lp', '菊池厚平'),
                                                                    $this->request->data['Email']['sales_people']) ? 'checked' : null
                                                          ]) ?>
                            </span>
                        <div class="media">
                            <div class="media-left media-middle">
                                <?= $this->Html->image('homepage/people/sales2.jpg',
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
                </label><!-- /.col-sm-6 col-xs-12 -->
                <label class="col-sm-6 col-xs-12 salesperson">
                    <div class="input-group">
                            <span class="input-group-addon">
                                <?= $this->Form->checkbox('sales_people.',
                                                          ['value'   => __d('lp', '吉岡真人'), 'hiddenField' => false,
                                                           'checked' => isset($this->request->data['Email']['sales_people']) &&
                                                               in_array(__d('lp', '吉岡真人'),
                                                                                 $this->request->data['Email']['sales_people']) ? 'checked' : null
                                                          ]) ?>
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
                                __d('lp', '愛をこめた営業をします')
                                ?>
                            </div>
                        </div>
                    </div><!-- /input-group -->
                </label><!-- /.col-sm-6 col-xs-12 -->
                <label class="col-sm-6 col-xs-12 salesperson">
                    <div class="input-group">
                            <span class="input-group-addon">
                                <?= $this->Form->checkbox('sales_people.',
                                                          ['value'   => __d('lp', '石原裕介'), 'hiddenField' => false,
                                                           'checked' => isset($this->request->data['Email']['sales_people']) &&
                                                               in_array(__d('lp', '石原裕介'),
                                                                                 $this->request->data['Email']['sales_people']) ? 'checked' : null
                                                          ]) ?>
                            </span>
                        <div class="media">
                            <div class="media-left media-middle">
                                <?= $this->Html->image('homepage/people/sales4.jpg',
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
                </label><!-- /.col-sm-6 col-xs-12 -->
            </div><!--//form-group-->
        </div>
        <div class="contact-form col-md-8 col-sm-12 col-xs-12 col-md-offset-2">
            <div class="checkbox">
                <label>
                    <?= $this->Form->checkbox('need') ?>
                    <?= $this->Html->link(__d('lp', '利用規約'),[
                        'controller' => 'pages',
                        'action' => 'display',
                        'pagename'   => 'terms',
                        'lang' => $top_lang,
                    ],
                    [
                        'target' => '_blank',
                    ]
                    ) ?>
                    <?= __d('lp', '・') ?>
                    <?= $this->Html->link(__d('lp', '個人情報の取り扱い'),[
                        'controller' => 'pages',
                        'action' => 'display',
                        'pagename'   => 'privacy_policy',
                        'lang' => $top_lang,
                    ],
                    [
                        'target' => '_blank',
                    ]
                    ) ?>
                    <?= __d('lp', 'について同意の上、問い合わせする') ?>
                </label>
                <?= $this->Form->error('need', __d('lp', '個人情報保護方針の同意が必要です。'),
                                       ['class' => 'contact-error-msg-block']) ?>
            </div>
            <p>
                <?= $this->Form->submit(__d('lp', '確認画面へ'), ['class' => 'btn btn-block btn-cta-primary']) ?>
            </p>
        </div><!--//contact-form-->

        <?= $this->Form->end() ?>
        <!--//form-->
    </div><!--//row-->
</section>

<!-- END app/View/Pages/contact.ctp -->
