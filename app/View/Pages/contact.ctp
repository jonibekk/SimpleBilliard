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
<?php
/*
Page毎に要素が変わるもの
- meta description
- og:description
- og:url
- title
*/
$meta_contact = [
    [
        "name"    => "description",
        "content"  => __('Goalous is one of the best team communication tools. Let your team open. Your action will be share with your collegues. %s',__("We accept consultation of introduction.")),
    ],
    [
        "name"    => "keywords",
        "content" => __("goal management, achieve a goal, sns app, evaluation, mbo"),
    ],
    [
        "property" => "og:type",
        "content"  => "website",
    ],
    [
        "property" => "og:title",
        "content"  => __('Contact us | Goalous'),
    ],
    [
        "property" => "og:description",
        "content"  => __('Goalous is one of the best team communication tools. Let your team open. Your action will be share with your collegues. %s',__("We accept consultation of introduction.")),
    ],
    [
        "property" => "og:url",
        "content"  => "https://www.goalous.com/contact/",
    ],
    [
        "property" => "og:image",
        "content"  => "https://www.goalous.com/img/homepage/background/promo-bg.jpg",
    ],
    [
        "property" => "og:site_name",
        "content"  => __('Goalous │ Enterprise SNS the most ever open for Goal'),
    ],
    [
        "property" => "fb:app_id",
        "content"  => "966242223397117",
    ],
    [
        "name"    => "twitter_card",
        "content" => "summary",
    ],
    [
        "name"    => "twitter:site",
        "content" => "@goalous",
    ]
];
$num_ogp = count($meta_contact);
for ($i = 0; $i < $num_ogp; $i++) {
    echo $this->Html->meta($meta_contact[$i]);
}
?>
<title><?= __('Contact us | Goalous') ?></title>
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
                <h2 class="title"><?= __('Contact us about Goalous') ?></h2>
                <p class="contact-list">
                    <?=
                    __('Goalous is managed by ISAO, Visionary Company which make jobs joyful in the world') .
                    '<br>' .
                    __('Feel free to ask us.');
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
                <span class="label label-danger"><?= __('Required') ?></span>
                <?= $this->Form->input('name', [
                    'placeholder' => __('eg. Isao Suzuki'),
                    'id'          => 'EmailName',
                    'class'       => 'form-control lp-contact-form-control',
                    'label'       => [
                        'text'  => __('Your name'),
                        'class' => 'control-label lp-contact-control-label',
                    ],
                ]) ?>
                <?= $this->Form->error('name', null, ['class' => 'contact-error-msg-block']) ?>
            </div>
            <div class="form-group email">
                <span class="label label-danger"><?= __('Required') ?></span>
                <?= $this->Form->input('email', [
                    'placeholder' => __('eg. example@goalous.com'),
                    'id'          => 'email',
                    'class'       => 'form-control lp-contact-form-control',
                    'label'       => [
                        'text'  => __('Email address'),
                        'class' => 'control-label lp-contact-control-label',
                    ],
                ]) ?>
                <?= $this->Form->error('email', null, ['class' => 'contact-error-msg-block']) ?>
            </div>
            <div class="form-group company">
                <?= $this->Form->input('company', [
                    'placeholder' => __('eg. ISAO Cooporation'),
                    'id'          => 'company',
                    'class'       => 'form-control lp-contact-form-control',
                    'label'       => [
                        'text'  => __('Company name, Organization name or Something like that.'),
                        'class' => 'control-label lp-contact-control-label',
                    ],
                ])
                ?>
                <?= $this->Form->error('company', null, ['class' => 'contact-error-msg-block']) ?>
            </div>
            <div class="form-group want">
                <span class="label label-danger"><?= __('Required') ?></span>
                <?=
                $this->Form->input('want', [
                    'options' => $type_options, // PagesController - ln.184,
                    'value'   => $selected_type,
                    'label'   => [
                        'text'  => __('Which item you want to ask'),
                        'class' => 'control-label lp-contact-control-label',
                        'empty' => false,
                    ],
                    'class'   => 'form-control lp-contact-form-control',
                ]); ?>
                <?= $this->Form->error('want', null, ['class' => 'contact-error-msg-block']) ?>
            </div>
            <div class="form-group messsage">
                <span class="label label-danger"><?= __('Required') ?></span>
                <?=
                $this->Form->input('message', [
                    'class'       => 'form-control lp-contact-form-control',
                    'type'        => 'text',
                    'rows'        => 8,
                    'placeholder' => __('eg. We want to use it. So, we need more explanation in detail.'),
                    'label'       => [
                        'text'  => __('What you ask (3,000 characters are limit)'),
                        'class' => 'control-label lp-contact-control-label',
                    ],
                ]);
                ?>
                <?= $this->Form->error('message', null, ['class' => 'contact-error-msg-block']) ?>
            </div>
        </div>

        <div class="contact-form col-md-8 col-xs-12 col-md-offset-2 text-center">
            <label class="control-label"><?= __('You can request your sales person.') ?></label>
            <? $this->Form->unlockField('sales_people') ?>
            <div class="form-group sales text-left">
                <?php
                $sales_people = [
                    [
                        'name'        => __('Keita Yukawa'),
                        'description' => __('Cool like Japanese actor'),
                        'img'         => 'homepage/people/sales1.jpg',
                    ],
                    [
                        'name'        => __('Kohei Kikuchi'),
                        'description' => __('Owner of Goalous'),
                        'img'         => 'homepage/people/sales2.jpg',
                    ],
                    [
                        'name'        => __('Makoto Yoshioka'),
                        'description' => __('Sales person with full of Joyful'),
                        'img'         => 'homepage/people/sales3.jpg',
                    ],
                    [
                        'name'        => __('Yusuke Ishihara'),
                        'description' => __('I\'m a DJ from teens'),
                        'img'         => 'homepage/people/sales4.jpg',
                    ],
                ]
                ?>
                <? foreach ($sales_people as $k => $v): ?>
                    <label class="col-sm-6 col-xs-12 salesperson">
                        <div class="input-group">
                            <span class="input-group-addon">
                                <?= $this->Form->checkbox('sales_people.',
                                    [
                                        'value'       => $v['name'],
                                        'hiddenField' => false,
                                        'checked'     => isset($this->request->data['Email']['sales_people']) &&
                                        in_array($v['name'],
                                            $this->request->data['Email']['sales_people']) ? 'checked' : null
                                    ]) ?>
                            </span>
                            <div class="media">
                                <div class="media-left media-middle">
                                    <?= $this->Html->image($v['img'],
                                        array('alt'    => 'photo',
                                              'width'  => '60',
                                              'height' => '60',
                                              'class'  => 'img-circle'
                                        )); ?>
                                </div>
                                <div class="media-body media-middle">
                                    <?=
                                    $v['name'] .
                                    '<br>' .
                                    $v['description']
                                    ?>
                                </div>
                            </div>
                        </div><!-- /input-group -->
                    </label><!-- /.col-sm-6 col-xs-12 -->

                <? endforeach; ?>
            </div><!--//form-group-->
        </div>
        <div class="contact-form col-md-8 col-sm-12 col-xs-12 col-md-offset-2">
            <div class="checkbox contact-form-checkbox">
                <label>
                    <?= $this->Form->checkbox('need') ?>
                    <?= $this->Html->link(__('Terms of service'), [
                        'controller' => 'pages',
                        'action'     => 'display',
                        'pagename'   => 'terms',
                        'lang'       => $top_lang,
                    ],
                        [
                            'target' => '_blank',
                        ]
                    ) ?>
                    <?= __('・') ?>
                    <?= $this->Html->link(__('PrivacyPolicy'), [
                        'controller' => 'pages',
                        'action'     => 'display',
                        'pagename'   => 'privacy_policy',
                        'lang'       => $top_lang,
                    ],
                        [
                            'target' => '_blank',
                        ]
                    ) ?>
                    <?= __(' - Do you agree with them?') ?>
                </label>
                <?= $this->Form->error('need', __('Need to agree to terms and policy.'),
                    ['class' => 'contact-error-msg-block']) ?>
            </div>
            <p>
                <?= $this->Form->submit(__('Go to confirm'), ['class' => 'btn btn-block btn-cta-primary']) ?>
            </p>
        </div><!--//contact-form-->

        <?= $this->Form->end() ?>
        <!--//form-->
    </div><!--//row-->
</section>

<!-- END app/View/Pages/contact.ctp -->
