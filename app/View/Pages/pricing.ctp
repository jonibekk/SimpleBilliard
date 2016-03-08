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
        "name" => "description",
        "content" => __('Goalous (ゴーラス) は、ゴール達成への最強にオープンな社内SNS。すべてのメンバーのゴールをオープンにし、ゴールへのアクションを写真でたのしく共有できます。料金・価格はこちら。'),
    ],
    [
        "name" => "keywords",
        "content" => "目標管理,目標達成,社内SNS,評価,MBO",
    ],
    [
        "property" => "og:type",
        "content" => "website",
    ],
    [
        "property" => "og:title",
        "content" => __('料金・価格 | Goalous(ゴーラス)'),
    ],
    [
        "property" => "og:description",
        "content" =>__('Goalous (ゴーラス) は、ゴール達成への最強にオープンな社内SNS。すべてのメンバーのゴールをオープンにし、ゴールへのアクションを写真でたのしく共有できます。料金・価格はこちら。'),
    ],
    [
        "property" => "og:url",
        "content" => "https://www.goalous.com/pricing/",
    ],
    [
        "property" => "og:image",
        "content" => "https://www.goalous.com/img/homepage/background/promo-bg.jpg",
    ],
    [
        "property" => "og:site_name",
        "content" => __('Goalous │ Enterprise SNS the most ever open for Goal'),
    ],
    [
        "property" => "fb:app_id",
        "content" => "966242223397117",
    ],
    [
        "name" => "twitter_card",
        "content" => "summary",
    ],
    [
        "name" => "twitter:site",
        "content" => "@goalous",
    ]
];
$num_ogp = count($meta_pricing);
for($i = 0; $i < $num_ogp; $i++){
    echo $this->Html->meta($meta_pricing[$i]);
}
?>
<title><?= __('料金・価格 | Goalous (ゴーラス)') ?></title>
<link rel="alternate" hreflang="ja" href="<?= $this->Html->url('/ja/pricing') ?>"/>
<link rel="alternate" hreflang="en" href="<?= $this->Html->url('/en/pricing') ?>"/>
<link rel="alternate" hreflang="x-default" href="<?= $this->Html->url('/pricing') ?>"/>
<?php $this->end() ?>
<!-- START app/View/Pages/pricing.ctp -->
<!-- ******PRICE PLAN****** -->
<section id="price-plan" class="price-plan section">
    <div class="container text-center">
        <h2 class="title"><?= __('今だけ、有料プランも完全無料') ?></h2>
        <p class="intro"><?= __('リリース記念で2016年8月までPlusをご利用いただけます。フィードバックをお待ちしております。') ?></p>
        <div class="item col-xs-12 col-md-6 col-md-offset-3">
            <h3 class="heading"><?= __('Plus') ?><span class="label label-custom"><?= __(
                                                                                                 'キャンペーン') ?></span>
            </h3>
            <div class="content">
                <div class="price-figure">
                    <span class="currency"><?= __('¥') ?>
                        <div class="pricing-line-through"></div></span><span class="number">1,980</span><span
                        class="unit"><?= __('/月') ?></span>
                </div>
                <i class="fa fa-arrow-down pricing-figure-mid-icon"></i>
                <div class="price-figure">
                    <p><?= __('1ユーザーあたり') ?></p>
                    <span class="currency"><?= __('¥') ?></span>
                    <span class="number">0</span>
                    <span class="unit"><?= __('/月') ?></span>
                </div>
                <ul class="list-unstyled feature-list">
                    <li><?= __('1チームのアカウント無制限') ?></li>
                    <li><?= __('20MB/ファイルのアップロード') ?></li>
                    <li><?= __('ストレージ無制限のファイル共有') ?></li>
                    <li><?= __('チャットメッセージ') ?></li>
                    <li><?= __('インサイト分析') ?></li>
                    <li><?= __('チーム管理機能') ?></li>
                    <li><?= __('オンラインでのユーザーサポート') ?></li>
                </ul>
                <a class="pricing-signup btn btn-cta btn-cta-primary" id="RegisterLinkPricingPlus"
                   href="<?= $this->Html->url(['controller' => 'users', 'action' => 'register', '?' => ['type' => 'pricing_plus']]) ?>">
                    <?= __('今すぐ始める') ?>
                    <br/>
                        <span class="extra">
                            <?= __('無料相談受付中') ?>
                        </span>
                </a>
            </div><!--//content-->
        </div><!--//item-->
    </div><!--//row-->
    </div><!--//container-->
</section><!--//price-plan-->

<?= $this->element('Homepage/signup') ?>
<!-- END app/View/Pages/pricing.ctp -->
