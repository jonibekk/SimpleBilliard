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
$meta_terms = [
    [
        "name"    => "description",
        "content"  => __("Use GKA(Goal-Key Result-Action) invented based on OKR methodology to improve communication across your organization."),
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
        "content"  => __('Terms of service | Goalous'),
    ],
    [
        "property" => "og:description",
        "content"  => __("Use GKA(Goal-Key Result-Action) invented based on OKR methodology to improve communication across your organization."),
    ],
    [
        "property" => "og:url",
        "content"  => "https://www.goalous.com/terms/",
    ],
    [
        "property" => "og:image",
        "content"  => AppUtil::fullBaseUrl(ENV_NAME)."/img/homepage/promo.jpg",
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
$num_ogp = count($meta_terms);
for ($i = 0; $i < $num_ogp; $i++) {
    echo $this->Html->meta($meta_terms[$i]);
}
?>
<title><?= __('Terms of service | Goalous') ?></title>
<link rel="alternate" hreflang="ja" href="<?= $this->Html->url('/ja/terms') ?>"/>
<link rel="alternate" hreflang="en" href="<?= $this->Html->url('/en/terms') ?>"/>
<link rel="alternate" hreflang="x-default" href="<?= $this->Html->url('/terms') ?>"/>
<?php $this->end() ?>
<?= $this->App->viewStartComment()?>
<p class="termsOfService">
    <?= nl2br($terms)?>
</p>
<?= $this->App->viewEndComment()?>
