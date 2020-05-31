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
        "content"  => __('Goalous is one of the best team communication tools. Let your team open. Your action will be share with your collegues. %s',__("Terms and Conditions is here.")),
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
        "content"  => __('Goalous is one of the best team communication tools. Let your team open. Your action will be share with your collegues. %s',__("Terms and Conditions is here.")),
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

$terms = "Goalousキャンペーン利用特約

株式会社Colorkrew（以下「Colorkrew」といいます）と目標達成支援ツール「Goalous」利用者（以下「利用者」といいます）は、Goalous利用規約（以下「原規約」といいます）にもとづき、両者間で成立した「Goalous」利用契約（以下「Goalous利用契約」といいます）について、以下条件にてGoalousを利用することができます。なお、本特約にて使用する用語・定義は特段の定めがある場合を除き、原規約と同一とする。

----

第1条 （キャンペーン価格の適用）
(1) 利用者は、本特約に同意することにより、Goalous利用料をキャンペーン価格にて利用することができます。購入時に提示される料金に所定の税金を加算した金額が、年間の契約期間中に毎月請求されます。
(2) Goalous利用料は、Goalous利用規約の定めに従い支払います。

----

第2条（協力義務）
利用者は、以下に例示するColorkrewによるGoalousの販売活動に最大限協力するものとします。
(1) Goalousの利用状況、その効果等について、Colorkrewからの取材に応じる
(2) Goalousサービスサイトや紹介サイト、その他販促ツールへの利用者の商号（ロゴ含む）、代表者及び担当者名、取材内容等の掲載
(3) Colorkrewが主催するGoalous紹介セミナー等での講演

----

第3条 （キャンペーン価格適用期間）
(1) キャンペーン価格は、本規約同意日を始期とし、キャンペーン価格適用日の翌年における同一日の前日までを適用期間（以下「適用期間」という）とします。契約は解約されるまで毎年キャンペーン価格適用日に自動的に更新されます。更新料金は変更されることがありますが、その場合はチーム管理者に通知いたします。
(2) キャンペーン価格適用期間中には、上位プラン(メンバー数の上限がより多い)に変更することができます。上位プランは購入時の料金で価格設定されます。上位プランに変更した場合、日割り計算により不足金額をお支払いいただきます。また、適用期間中に下位プラン(メンバー数の上限がより少ない)に変更することはできません。ただし、適用期間の １１ヶ月目にのみ、下位プランに変更することが可能です。この場合、その差額は返金されません。
(3) 適用期間満了日より前にGoalousを中途解約した場合、中途解約日から適用期間満了日の残存期間において発生するGoalous利用料の100%を一括してColorkrewに支払います。なお、解約後にサービスはすぐに終了されます。

----

附 則１．本規約は、2017年11月1日より施行します。
";

?>
<title><?= __('Campaign Contract | Goalous') ?></title>
<link rel="alternate" hreflang="ja" href="<?= $this->Html->url('/ja/terms') ?>"/>
<link rel="alternate" hreflang="en" href="<?= $this->Html->url('/en/terms') ?>"/>
<link rel="alternate" hreflang="x-default" href="<?= $this->Html->url('/terms') ?>"/>
<?php $this->end() ?>
<?= $this->App->viewStartComment()?>
<p class="termsOfService">
    <?= nl2br($terms)?>
</p>
<?= $this->App->viewEndComment()?>
