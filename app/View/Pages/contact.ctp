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
        "content" => __("Let's go on a journey to pursue the essence of the team. Get in touch with us!"),
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
        "content"  => __("Let's go on a journey to pursue the essence of the team. Get in touch with us!"),
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
<?= $this->App->viewStartComment() ?>


<section id="contact_section">
    <div class="container">
        <div class="container-half">
            <h1><?= __('Say <q>Hello</q> to your company&rsquo;s next communication tool');?></h1>
            <p><?= __('Through goal oriented communication, you can revolutionize your team&rsquo;s power! Contact us today, and we&rsquo;ll help you get started along with a <strong>free trial</strong> of Goalous!'); ?></p>
            <figure>
                <img src="<?= $this->Lang->getLangCode() == LangHelper::LANG_CODE_JP ? '/img/homepage/goalous-contact-jp.png' : '/img/homepage/goalous-contact-en.png'?>" alt="Screenshots of the Goalous Application">
            </figure>
        </div>
        <div class="container-half">
            <h2><?= __('Contact Us Today'); ?></h2>

            <?=
            $this->Form->create('Email', [
                'id'            => 'contact-form',
                'class'         => 'form',
                'inputDefaults' => ['div' => null, 'wrapInput' => false, 'class' => null, 'error' => false]
            ]); ?>
                <div class="half">
                    <label for="lastName"><?= __('Last Name ');?> <sup class="req">*</sup></label>
                    <?= $this->Form->input('name_last', [
                        'placeholder' => __('Last Name '),
                        'id'          => 'name_last',
                        'required'     => true,
                    ]) ?>
                </div>
                <div class="half">
                    <label for="firstName"><?= __('First Name ');?> <sup class="req">*</sup></label>
                    <?= $this->Form->input('name_first', [
                        'placeholder' => __('First Name '),
                        'id'          => 'name_first',
                        'required'     => true,
                    ]) ?>
                </div>
                <label for="email"><?= __('Your Work Email Address');?> <sup class="req">*</sup></label>
                <?= $this->Form->input('email', [
                    'placeholder' => __('Your Work Email Address'),
                    'id'          => 'email',
                    'required'    => true,
                    'type'        => 'email'
                ]) ?>
                <label for="phone"><?= __('Phone Number (Optional)');?></label>
            <?= $this->Form->input('phone', [
                'placeholder' => __('Phone Number (Optional)'),
                'id'          => 'phone',
                'type'        => 'tel',
                'required'    => false,
            ]) ?>
                <label for="company"><?= __('Company Name (Optional)'); ?></label>
            <?= $this->Form->input('company', [
                'placeholder' => __('Company Name (Optional)'),
                'id'          => 'company',
                'required'    => false
            ]) ?>
                <div class="container-submit">
                    <p><small><?= __("By clicking <q>I Agree. Contact us.</q> below, you are agreeing to the <a href='/terms' target='_blank'>Terms&nbsp;of&nbsp;Service</a> and the <a href='/privacy_policy' target='_blank'>Privacy&nbsp;Policy</a>.");?></small></p>
                    <button class="btn btn-cta-primary"><?=__('I Agree, Contact us');?></button>
                </div>
            <?= $this->Form->end() ?>

        </div>
    </div>
</section>

<?= $this->App->viewEndComment() ?>
