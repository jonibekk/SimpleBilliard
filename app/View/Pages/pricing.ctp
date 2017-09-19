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
        <h2><?= __('Pricing Guide');?></h2>
        <p><?= __('You use all features of Goalous for a 15-day free trial. Give it a try!');?></p>
        <div class="pricing-card">
            <h3><?= __('Standard');?></h3>
            <p><span class="price-text">&yen;1,900</span>
            <?= __('Per active member, per month');?></p>
            <button class="btn btn-cta btn-cta-primary"><?= __('Start 15-day Free Trial');?></button>
            <div class="hr"></div>
            <!-- TODO: Replace placement text -->
            <p>Goalous is a lorem ipsum dolor sit amet, consectetur adipisicing elit. Eligendi omnis commodi aliquam, animi sit nesciunt in eos officiis illo. Officiis distinctio harum maiores voluptatem odio, quasi labore omnis, nemo at?</p>
        </div>
        <!-- TODO: Replace placement text -->
        <div class="feature-category">
            <strong class="icon icon-heart"><?= __('Goal features');?></strong>
            <ul>
                <li><?= __('Create &amp; share Goals for your project'); ?></li>
                <li><?= __('Team members can join to collaborate towards your Goal.');?></li>
                <li><?= __('Organize Goals into separate <q>Key Results</q>');?></li>
                <li><?= __('Post <q>Actions</q> that contribute to the <q>Key Result</q>');?></li>
                <li><?= __('Graphical progress that helps motivate your team');?></li>
            </ul>
        </div>
        <div class="feature-category">
            <strong class="icon icon-message"><?= __('Communication features');?></strong>
            <ul>
                <li><? __('Create private Circles where members can communicate');?></li>
                <li><? __('Give reactions to posts with likes and comments');?></li>
                <li><? __('Realtime chat messaging');?></li>
                <li><? __('Attach and share files up to 100MB per file');?></li>
                <li><? __('See how many members have read your posts and comments');?></li>
            </ul>
        </div>
        <div class="feature-category">
            <strong class="icon icon-lock"><?=__('Security Features');?></strong>
            <ul>
                <li><? __('Unlimited team members');?></li>
                <li><? __('Two-Step velification for stronger security');?></li>
                <li><? __('English and Japanese language available');?></li>
                <li><? __('Live chat support (Initial response will be within the next business day)');?></li>
                <li><? __('iOS(10 or higher) and Android(4.4 or higher) APPs');?></li>
            </ul>
        </div>
    </div>
</section>

<section id="faqs">
    <div class="container">
        <h2><?= __('Frequently Asked Questions');?></h2>
        <div class="question">
            <p class="question-entry"><strong><?= __('What are the payment methods available?');?></strong><span class="fa fa-angle-down"></span></p>
            <div class="answer">
                <p><?=__('"Team members who fit the following criteria are considered to be billable monthly active members:');?>
                <ul>
                    <li><?= __('Team members who are active by the payment date (those not deactivated by the team administrator) in the event that team members were added by the team administrator between the current month’s payment date and 1 day prior to the following month’s payment date, the number of billable members will be more than the number of active members falling on the current month’s payment date.');?></li>
                    <li><?= __('In addition, in that situation, added team member’s usage fee will be charged based on daily rate."');?></p>
            </div>
        </div>
        <div class="question">
            <p class="question-entry"><strong><?= __('How can I deactivate team members?');?></strong><span class="fa fa-angle-down"></span></p>
            <div class="answer">
                <p><?=__('The team administrator has authority to do so. The team administrator can deactivate members by going to the team member list and changing the corresponding member’s setting.');?></p>
                <p><?=__('Deactivated team members will lose their login permission to the team they have been deactivated from.');?></p>
                <p><?=__('In addition, in that situation, usage fee for deactivated members will not be refunded.');?></p>
            </div>
        </div>
        <div class="question">
            <p class="question-entry"><strong><?= __('What kind of authority does a team manager have?');?></strong><span class="fa fa-angle-down"></span></p>
            <div class="answer">
                <p><?= __('The team administrator can give associates team administrative roles in Goalous.');?></p>
                <p><?= __('Team administrators have the following authorities.');?>
                <ul>
                    <li><?=__('Team detail setting changes');?></li>
                    <li><?=__('Assign administrative roles');?></li>
                    <li><?=__('Default circles (All team) management');?></li>
                    <li><?=__('Team member deactivation');?></li>
                    <li><?=__('Deletion of inappropriate posts');?></li>
                    <li><?=__('Paid Plan detail changes');?></li>
                    <li><?=__('Settlement information reference changes');?></li>
                </ul>
            </div>
        </div>
        <div class="question">
            <p class="question-entry"><strong><?= __('What is the payment schedule for invoice?');?></strong><span class="fa fa-angle-down"></span></p>
            <div class="answer">
                <p><?=__('After creating your invoice contract, your first invoice will be issued the following business day. You will then have 14 days to pay the invoice. If payment has not been received within 8 days after your due date, an updated invoice will be issued on the following business day.');?>
                <p><?=__('Upon issuing the updated invoice, you will have 10 days to complete your payment. If this process is repeated multiple times, your company may be subject to review, and may be denied access to Goalous."');?>'
            </div>
        </div>
    </div>
</section>
<?= $this->element('Homepage/signup') ?>
<?= $this->App->viewEndComment()?>
