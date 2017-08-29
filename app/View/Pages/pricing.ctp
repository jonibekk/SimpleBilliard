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
$meta_pricing = [
    [
        "name"    => "description",
        "content"  => __('Goalous is one of the best team communication tools. Let your team open. Your action will be share with your collegues. %s',__("Pricing is here.")),
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
        "content"  => __('Pricing | Goalous'),
    ],
    [
        "property" => "og:description",
        "content"  => __('Goalous is one of the best team communication tools. Let your team open. Your action will be share with your collegues. %s',__("Pricing is here.")),
    ],
    [
        "property" => "og:url",
        "content"  => "https://www.goalous.com/pricing/",
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
$num_ogp = count($meta_pricing);
for ($i = 0; $i < $num_ogp; $i++) {
    echo $this->Html->meta($meta_pricing[$i]);
}
?>
<title><?= __('Pricing | Goalous') ?></title>
<link rel="alternate" hreflang="ja" href="<?= $this->Html->url('/ja/pricing') ?>"/>
<link rel="alternate" hreflang="en" href="<?= $this->Html->url('/en/pricing') ?>"/>
<link rel="alternate" hreflang="x-default" href="<?= $this->Html->url('/pricing') ?>"/>
<?php $this->end() ?>
<?= $this->App->viewStartComment()?>

<section id="pricingDetail">
    <div class="container">
        <h2>Pricing Guide</h2>
        <p>You use all features of Goalous for a 15-day free trial. Give it a try!</p>
        <div class="pricing-card">
            <h3>Standard</h3>
            <p><span class="price-text">&yen;1,900</span>
            Per active member, per month</p>
            <button class="btn btn-cta btn-cta-primary">Start 15-day Free Trial</button>
            <div class="hr"></div>
            <p>Goalous is a lorem ipsum dolor sit amet, consectetur adipisicing elit. Eligendi omnis commodi aliquam, animi sit nesciunt in eos officiis illo. Officiis distinctio harum maiores voluptatem odio, quasi labore omnis, nemo at?</p>
        </div>
        <div class="feature-category">
            <strong class="icon icon-heart">Goal Features</strong>
            <ul>
                <li>Unlimited team, goal, photo, and video storage</li>
            </ul>
        </div>
        <div class="feature-category">
            <strong class="icon icon-message">Communication Features</strong>
            <ul>
                <li>Live chat messaging</li>
            </ul>
        </div>
        <div class="feature-category">
            <strong class="icon icon-lock">Security Features</strong>
            <ul>
                <li>Encrypted storage</li>
            </ul>
        </div>
    </div>
</section>

<section id="faqs">
    <div class="container">
        <h2>Frequently Asked Questions</h2>
        <div class="question">
            <p class="question-entry"><strong>What are my payment options?</strong><span class="fa fa-angle-down"></span></p>
            <div class="answer">
                <p>You can use your credit card to pay for our Standard Plan. If your organizaiton is located in Japan, we can invoice you monthly.</p>
                <p>You can use your credit card to pay for our Standard Plan. If your organizaiton is located in Japan, we can invoice you monthly.</p>
            </div>
        </div>
        <div class="question">
            <p class="question-entry"><strong>What are my payment options?</strong><span class="fa fa-angle-down"></span></p>
            <div class="answer">
                <p>You can use your credit card to pay for our Standard Plan. If your organizaiton is located in Japan, we can invoice you monthly.</p>
                <p>You can use your credit card to pay for our Standard Plan. If your organizaiton is located in Japan, we can invoice you monthly.</p>
            </div>
        </div>
    </div>
</section>
<?= $this->element('Homepage/signup') ?>
<?= $this->App->viewEndComment()?>
