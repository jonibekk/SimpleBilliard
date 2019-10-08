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
<?php
/*
Page毎に要素が変わるもの
- meta description
- og:description
- og:url
- title
*/
if (!isset($top_lang)) {
    $top_lang = null;
}
$meta_features = [
    [
        "name"    => "description",
        "content"  => __("Goalous changes the communication of many organizations and creates a team that runs for Key Results."),
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
        "content"  => __('Case Studies | Goalous'),
    ],
    [
        "property" => "og:description",
        "content"  => __("Goalous changes the communication of many organizations and creates a team that runs for Key Results."),
    ],
    [
        "property" => "og:url",
        "content"  => AppUtil::fullBaseUrl(ENV_NAME). $_SERVER['REQUEST_URI'],
    ],
    [
        "property" => "og:image",
        "content"  => AppUtil::fullBaseUrl(ENV_NAME)."/img/homepage/promo_".$company.".png",
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
$num_ogp = count($meta_features);
for ($i = 0; $i < $num_ogp; $i++) {
    echo $this->Html->meta($meta_features[$i]);
}
?>
<title><?= __('Case Studies | Goalous') ?></title>
<link rel="alternate" hreflang="ja" href="<?= $this->Html->url('/ja/casestudy') ?>"/>
<link rel="alternate" hreflang="en" href="<?= $this->Html->url('/en/casestudy') ?>"/>
<link rel="alternate" hreflang="x-default" href="<?= $this->Html->url('/casestudy') ?>"/>
<?php $this->end() ?>
<?= $this->App->viewStartComment()?>

<?= $this->element('CaseStudy/casestudy_nav') ?>
<?= $this->element('CaseStudy/'.$company) ?>


<!--START-contact-->
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
                'url'          => [
                    'controller' => 'pages',
                    'action'     => 'contact',
                    'lang'       => $top_lang
                ],
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
<!--END-contact-->
