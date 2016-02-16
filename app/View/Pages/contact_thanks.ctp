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
        "name" => "description",
        "content" => __d('lp','Goalous (ゴーラス) は、ゴール達成への最強にオープンな社内SNS。すべてのメンバーのゴールをオープンにし、ゴールへのアクションを写真でたのしく共有できます。お問い合わせありがとうございました。'),
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
        "content" => __d('app', 'お問い合わせ完了 | Goalous(ゴーラス)'),
    ],
    [
        "property" => "og:description",
        "content" =>__d('app', 'Goalous (ゴーラス) は、ゴール達成への最強にオープンな社内SNS。すべてのメンバーのゴールをオープンにし、ゴールへのアクションを写真でたのしく共有できます。お問い合わせありがとうございました。'),
    ],
    [
        "property" => "og:url",
        "content" => "https://www.goalous.com/contact_thanks/",
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
$num_ogp = count($meta_contact_thanks);
for($i = 0; $i < $num_ogp; $i++){
    echo $this->Html->meta($meta_contact_thanks[$i]);
}
?>
<title><?= __d('lp', 'お問い合わせ完了 | Goalous (ゴーラス)') ?></title>
<link rel="alternate" hreflang="ja" href="<?= $this->Html->url('/ja/contact_thanks') ?>"/>
<link rel="alternate" hreflang="en" href="<?= $this->Html->url('/en/contact_thanks') ?>"/>
<link rel="alternate" hreflang="x-default" href="<?= $this->Html->url('/contact_thanks') ?>"/>
<?php $this->end() ?>
<!-- START app/View/Pages/contact_thanks.ctp -->
<!-- ******CONTACT MAIN****** -->
<section id="contact-main" class="contact-main section">
    <div class="container text-center">
        <h2 class="title"><?= __d('lp', 'お問い合わせありがとうございました') ?></h2>
        <p class="intro"><?= __d('lp', '確認後、弊社担当者よりご連絡いたします。') ?></p>
        <p class="intro"><?= __d('lp', 'お時間がございましたら、Goalous運営会社・株式会社ISAOのブログやSNSをご覧ください。') ?></p>

        <div class="row">
            <div class="item col-md-4 col-sm-12 col-xs-12">
                <div class="item-inner">
                    <div class="icon">
                        <i class="fa fa-rss"></i>
                    </div>
                    <div class="details">
                        <h4><?= __d('lp', 'IsaB') ?></h4>
                        <p><?= __d('lp', '億人の”シゴト”を熱くする！株式会社ISAOのブログ') ?></p>
                        <p><a href="http://blog.isao.co.jp/" target="_blank"><?= __d('lp', 'http://blog.isao.co.jp/') ?></a></p>
                    </div><!--details-->
                </div><!--//item-inner-->
            </div><!--//item-->
            <div class="item col-md-4 col-sm-12 col-xs-12">
                <div class="item-inner">
                    <div class="icon">
                        <i class="fa fa-facebook"></i>
                    </div>
                    <div class="details">
                        <h4><?= __d('lp', 'facebookページ') ?></h4>
                        <p><?= __d('lp', 'Like us！') ?></p>
                        <p><a href="https://www.facebook.com/isao.jp" target="_blank"><?= __d('lp', 'isao.jp') ?></a></p>
                    </div><!--details-->
                </div><!--//item-inner-->
            </div><!--//item-->
            <div class="item col-md-4 col-sm-12 col-xs-12">
                <div class="item-inner">
                    <div class="icon">
                        <i class="fa fa-twitter"></i>
                    </div>
                    <div class="details">
                        <h4><?= __d('lp', 'Twitter') ?></h4>
                        <p><?= __d('lp', 'Follow now！') ?></p>
                        <p><a href="https://twitter.com/ISAOcorp" target="_blank"><?= __d('lp', '@ISAOcorp') ?></a></p>
                    </div><!--details-->
                </div><!--//item-inner-->
            </div><!--//item-->
        </div><!--//row-->
    </div><!--//container-->
</section><!--//contact-->

<?= $this->element('Homepage/signup') ?>
<!-- END app/View/Pages/contact_thanks.ctp -->
