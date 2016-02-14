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
- url
- title
*/
$ogp_privacy_policy = [
    [
        "property" => "og:type",
        "content" => "website",
    ],
    [
        "property" => "og:title",
        "content" => __d('gl', 'プライバシーポリシー | Goalous(ゴーラス)'),
    ],
    [
        "property" => "og:description",
        "content" =>__d('gl', 'Goalous(ゴーラス)は、チーム力向上のためのSNSです。Goalousを利用すれば、オープンでクリアな目標設定をしたり、ゴールへの活動内容を写真で共有したり、サークルやメッセンジャーで仲間たちとコミュニケーションをとったりできます。'),
    ],
    [
        "property" => "og:url",
        "content" => "https://www.goalous.com/privacy_policy/",
    ],
    [
        "property" => "og:image",
        "content" => "https://www.goalous.com/img/homepage/background/promo-bg.jpg",
    ],
    [
        "property" => "og:site_name",
        "content" => __d('lp', 'Goalous (ゴーラス) │ゴール達成への最強にオープンな社内SNS'),
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
$num_ogp = count($ogp_privacy_policy);
for($i = 0; $i < $num_ogp; $i++){
    echo $this->Html->meta($ogp_privacy_policy[$i]);
}
?>
<title><?= __d('lp', 'プライバシーポリシー | Goalous (ゴーラス)') ?></title>
<link rel="alternate" hreflang="ja" href="<?= $this->Html->url('/ja/privacy_policy') ?>"/>
<link rel="alternate" hreflang="en" href="<?= $this->Html->url('/en/privacy_policy') ?>"/>
<link rel="alternate" hreflang="x-default" href="<?= $this->Html->url('/privacy_policy') ?>"/>
<?php $this->end() ?>
<!-- START app/View/Pages/privacy_policy.ctp -->
<div id="markdown" class="markdown-wrap" src="../../composition/markdowns/<?=$short_lang?>_privacy_policy.md"></div>
<!-- END app/View/Pages/privacy_policy.ctp -->
