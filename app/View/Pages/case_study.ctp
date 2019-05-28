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
        "content"  => __('Goalous is one of the best team communication tools. Let your team open. Your action will be share with your collegues. %s',__("Description of the function is here.")),
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
        "content"  => __('Features | Goalous'),
    ],
    [
        "property" => "og:description",
        "content"  => __('Goalous is one of the best team communication tools. Let your team open. Your action will be share with your collegues. %s',__("Description of the function is here.")),
    ],
    [
        "property" => "og:url",
        "content"  => "https://www.goalous.com/features/",
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
$num_ogp = count($meta_features);
for ($i = 0; $i < $num_ogp; $i++) {
    echo $this->Html->meta($meta_features[$i]);
}
?>
<title><?= __('Casestudy | Goalous') ?></title>
<link rel="alternate" hreflang="ja" href="<?= $this->Html->url('/ja/casestudy') ?>"/>
<link rel="alternate" hreflang="en" href="<?= $this->Html->url('/en/casestudy') ?>"/>
<link rel="alternate" hreflang="x-default" href="<?= $this->Html->url('/casestudy') ?>"/>
<?php $this->end() ?>
<?= $this->App->viewStartComment()?>

<section id="casestudy" class="casestudy-promo section">
  <div class="bg-mask-casestudy"></div>
    <div class="container">
      <h1 class="small">CASE STUDIES</h1>
        <div class="row flex center-block">
            <div class="casestudy-intro col-md-8 col-sm-12 col-xs-12">
                <h2 class="title"><span class="small"> Goalousを使うことで、</span><br>経営層と従業員との<br>双方向コミュニケーションが<br>活発になりました。</h2>
            <ul class="list-unstyled casestudy-list">
                    <li><i class="fa fa-check"></i>社員の声が聞きたい</li>
                    <li><i class="fa fa-check"></i>新聞の進化版はこれだ！</li>
                    <li><i class="fa fa-check"></i>目標設定でモチベーションアップ</li>
                    <li><i class="fa fa-check"></i>サークル機能でコミュニケーションが活発に</li>
                    <li><i class="fa fa-check"></i>オープンでフラットな組織に</li>
                </ul>
            </div><!--//intro-->

            <div class="enterprise col-md-4 col-sm-12 col-xs-12 center-block">
              <?= $this->Html->image('homepage/casestudy/yabusaki/yabusaki_logo.png', array(
                  'alt'   => __('You can understand your colleages easily, have you ever known another way to know this?'),
                  'class' => 'img-responsive'
              )) ?>
                <h2>ヤブサキ産業株式会社</h2>
                <ul class="list-unstyled enterprise-list" >
                <li>会社概要</li>
                <p class="small">
                １９６８年創業、自動車への関わりを通じて、豊かな未来を実現する「トータルカーライフサポート」企業。<br>
                高度経済成長時代に車社会の到来を見据え、千葉県市川市にヤブサキ産業初のガソリンスタンドを建設。<br>
                同拠点を契機にガソリンスタンドの拠点を拡大し、現在は千葉県トップクラスの売上を誇る一大石油販売店となる。<br>
                出光サービスステーション・車検・鈑金工場の各拠点においては、各種カーライフ事業を専門的な視野にたって展開している。
                </p>
                <hr/>
                <li>導入人数</li>
                <p class="small">100-200人</p>
                <hr/>
                <li>インタビュイー</li>
                <p class="small">課長 宮脇洋志様</p>
                </ul>
              </div><!--//enterprise-->
        </div><!--//row-->
    </div><!--//container-->
</section><!--//features-promo-->





<!-- ******CASESTUDY****** -->
<section id="casestudy" class="casestudy section">
    <div class="container casestudy-text col-md-12">
        <div class="item row flex">
            <div class="content col-md-7 col-sm-8 col-xs-12 center-block col-xs-offset-0 from-right col-right">
                <h3 class="title">現場と経営者の間にある<br>コミュニケーションの壁を壊したかった</h3>
                <h4>Goalous 導入前の課題</h4>
                <div class="details">
                    <p>今まで会社が目指しているもの・経営者の思いなど、会社の情報を社員に直接伝える手段がなかったため、情報を社内で共有できて社員の声を聞くようなコミュニケーションが必要であると感じていました。
                      その想いから7年前に社内向けの新聞を作成していましたが、全員が読んでいるのかわからなかったり、反応をきくまでにタイムラグがあったりと一方的なコミュニケーションになっていることに気づきました。
                    </p>
                    <?= $this->Html->image('homepage/casestudy/yabusaki/yabusaki_01.jpg', array(
                        'alt'   => __('Let\'s start conversation about any activities and topics!'),
                        'class' => 'center-block img-responsive img-casestudy'
                    )) ?>
                </div>
            </div><!--//content-->
        </div><!--//item-->


        <div class="item row flex">
            <div class="content col-md-7 col-sm-8 col-xs-12 center-block from-left col-left">
                    <h3 class="title center-block">導入の決め手は「社内新聞」を<br>リアルタイムで実現できること</h3>
                    <h4>Goalous 導入の背景</h4>
                <div class="details">
                  <p>Goalousを見たときに「新聞の進化版はこれだ！」と思いました。情報を共有したいとき、みんなが同じタイミングで知れるということが重要なポイントだと思っています。
                    会社や社員が目指す目標を見える化することによって、一方的なコミュニケーションではなく、会社全体で双方向のコミュニケーションができると思ったので、導入を決めました。
                    また、いくつか事業所があるのですが、離れていてもゴールの他に写真を共有できるので、一体感を感じられると思いました。
                </div>
                <?= $this->Html->image('homepage/casestudy/yabusaki/yabusaki_02.jpg',
                    array('alt' => __('Pictures let us describe better.'), 'class' => 'center-block img-responsive img-casestudy')) ?>
            </div><!--//content-->
        </div><!--//item-->


        <div class="item row flex">
          <div class="content col-md-7 col-sm-8 col-xs-12 center-block col-xs-offset-0 from-right col-right">
                <h3 class="title">共通のゴールを目指すことで<br>今までにない一体感が生まれた</h3>
                <h4>ゴール機能について</h4>
                <div class="details">
                  <p>
                    一番盛り上がったのは、各ガソリンスタンドの店舗でキャンペーン期間中に数字を競い合うゴールを立てたことです。
                    車検キャンペーンやコーティングキャンペーンなど、店舗毎にお客様の獲得数字目標がありますので、その数字を達成するために競って日々アクション投稿をしていました。
                    社員とアルバイト関係なくアクション投稿ができるので、店舗でも一体感を持って数字を追うことができました。
                    また、数字の進捗を追ったり現状他の店舗がどれくらい達成しているのかも共有できていたので、モチベーションアップにつながりました.
                  </p>
                </div>
                <?= $this->Html->image('homepage/casestudy/yabusaki/yabusaki_03.jpg', array(
                    'alt'   => __('Let\'s start conversation about any activities and topics!'),
                    'class' => 'center-block img-responsive img-casestudy img-ui'
                )) ?>
            </div><!--//content-->
        </div><!--//item-->


        <div class="item row flex">
            <div class="content col-md-7 col-sm-8 col-xs-12 center-block from-left col-left">
                <h3 class="title">コミュニケーションの目的に応じて<br>自由に作れる「サークル機能」</h3>
                <h4>サークル機能について</h4>
                <div class="details">
                    <p>各店舗の月別売り上げ情報をまとめたサークル・店舗ごとの連絡事項を共有するサークルなど、情報によってサークルを分けて使っています。
                    「サークル」は参加したメンバー間のみで投稿やいいね・コメントが可能なコミュニケーション機能です。
                    ○○事業部情報共有の部屋や○○部連絡板のように目的別に作成可能です。
                    </p>
                </div>
                      <?= $this->Html->image('homepage/casestudy/yabusaki/yabusaki_04.jpg',
                          array('alt' => __('Evaluation? Do it in Goalous!'), 'class' => 'center-block img-responsive img-casestudy img-ui' )) ?>
            </div><!--//content-->
        </div><!--//item-->



        <div class="item row flex">
            <div class="content col-md-7 col-sm-8 col-xs-12  center-block col-sm-offset-1 col-xs-offset-0 from-right ">
                <h3 class="title">仕事だけでなくプライベートまでもオープンに！<br>家族的な双方向のコミュニケーションを実現</h3>
                <h4>Goalous 導入後の成果</h4>
                <div class="details">
                  <p>
                    経営者からの思いの他にも、毎朝、各店舗で今日1日のやる気を投稿して情報をタイムリーに共有できるようになりました。
                      社員・アルバイト関係なく投稿ができるので、実際に顔が見えなくても社員の声や状況がいつでもわかります。
                      また、今では仕事の情報の他にも、「結婚しました」「子供が生まれました 」とプライベートな報告もオープンになりました。
                      会ったことのない社員同士であってもGoalousを通じたコミュニケーションがとりやすく、社員の帰属意識も高まったと思います。
                  </p>
                </div>
                <?= $this->Html->image('homepage/casestudy/yabusaki/yabusaki_05.jpg', array(
                    'alt'   => __('Let\'s start conversation about any activities and topics!'),
                    'class' => 'center-block img-responsive img-casestudy'
                )) ?>
            </div><!--//content-->
        </div><!--//item-->
      </div>
</div>
</section>
<!--END-casestudy-->

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
