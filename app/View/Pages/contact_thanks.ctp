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
$meta_contact_thanks = [
    [
        "name"    => "description",
        "content"  => __('Goalous is one of the best team communication tools. Let your team open. Your action will be share with your collegues. %s',__("Thank you for your inquiry.")),
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
        "content"  => __('お問い合わせ完了 | Goalous(ゴーラス)'),
    ],
    [
        "property" => "og:description",
        "content"  => __('Goalous is one of the best team communication tools. Let your team open. Your action will be share with your collegues. %s',__("Thank you for your inquiry.")),
    ],
    [
        "property" => "og:url",
        "content"  => "https://www.goalous.com/contact_thanks/",
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
$num_ogp = count($meta_contact_thanks);
for ($i = 0; $i < $num_ogp; $i++) {
    echo $this->Html->meta($meta_contact_thanks[$i]);
}
?>
<title><?= __('お問い合わせ完了 | Goalous (ゴーラス)') ?></title>
<link rel="alternate" hreflang="ja" href="<?= $this->Html->url('/ja/contact_thanks') ?>"/>
<link rel="alternate" hreflang="en" href="<?= $this->Html->url('/en/contact_thanks') ?>"/>
<link rel="alternate" hreflang="x-default" href="<?= $this->Html->url('/contact_thanks') ?>"/>
<?php $this->end() ?>
<?= $this->App->viewStartComment()?>
<!-- ******CONTACT MAIN****** -->
<section id="contact-main" class="contact-main section">
    <div class="container text-center">
        <h2 class="title"><?= __('Thank you for contacting us.') ?></h2>
        <p class="intro"><?= __('Support member will reply to you.') ?></p>

        <div class="row">
            <div class="item col-md-4 col-sm-12 col-xs-12">
                <div class="item-inner">
                    <?php
                        $blogUrl = ($this->Lang->getLangCode() == LangHelper::LANG_CODE_JP)
                                                    ? "https://www.goalous.com/blog/ja/"
                                                    : "https://www.goalous.com/blog/"
                    ?>
                    <a href="<?=$blogUrl ?>" target="_blank">
                        <div class="icon">
                            <i class="fa fa-rss"></i>
                        </div>
                    </a>
                    <div class="details">
                        <h4><?= __('Goalous Blog') ?></h4>
                        <p><?= __('The story of the team') ?></p>
                        <p><?= $this->Html->link(
                                __('Blog'),
                                $blogUrl,
                                ['target' => '_blank']); ?>
                        </p>
                    </div><!--details-->
                </div><!--//item-inner-->
            </div><!--//item-->
            <div class="item col-md-4 col-sm-12 col-xs-12">
                <div class="item-inner">
                    <a href="https://www.facebook.com/goalous" target="_blank">
                        <div class="icon">
                            <i class="fa fa-facebook"></i>
                        </div>
                    </a>
                    <div class="details">
                        <h4><?= __('Facebook page') ?></h4>
                        <p><?= __('Like us!') ?></p>
                        <p><a href="https://www.facebook.com/goalous" target="_blank">goalous</a></p>
                    </div><!--details-->
                </div><!--//item-inner-->
            </div><!--//item-->
            <div class="item col-md-4 col-sm-12 col-xs-12">
                <div class="item-inner">
                    <a href="https://twitter.com/goalous" target="_blank">
                        <div class="icon">
                            <i class="fa fa-twitter"></i>
                        </div>
                    </a>
                    <div class="details">
                        <h4><?= __('Twitter') ?></h4>
                        <p><?= __('Follow now!') ?></p>
                        <p><a href="https://twitter.com/goalous" target="_blank">@goalous</a></p>
                    </div><!--details-->
                </div><!--//item-inner-->
            </div><!--//item-->
        </div><!--//row-->
    </div><!--//container-->
</section><!--//contact-->
<?= $this->element('Homepage/signup') ?>
<!-- Goalous-Mar2018-spot-GoogleSearch -->
<script type="text/javascript">
/* <![CDATA[ */
var google_conversion_id = 956754448;
var google_conversion_label = "Z1J_CJ7HhH4QkNSbyAM";
var google_remarketing_only = false;
/* ]]> */
</script>
<script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js">
</script>
<noscript>
<div style="display:inline;">
<img height="1" width="1" style="border-style:none;" alt="" src="//www.googleadservices.com/pagead/conversion/956754448/?label=Z1J_CJ7HhH4QkNSbyAM&amp;guid=ON&amp;script=0"/>
</div>
</noscript>
<?= $this->App->viewEndComment()?>
