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
        "content"  => __("Pay only for what you need. Start with many. Try it free for 15 days and add more at any time."),
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
        "content"  => __("Pay only for what you need. Start with many. Try it free for 15 days and add more at any time."),
    ],
    [
        "property" => "og:url",
        "content"  => "https://www.goalous.com/pricing/",
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
        <p><?= __('You use all features of Goalous for a %s-day free trial. Give it a try!', 15);?></p>
        <div class="pricing-card">
            <h3><?= __('Paid Plan');?></h3>
            <p><span class="price-text"><?= $price; ?></span>
            <?= __('Per active member, per month');?></p>
            <?php if(!$isLoggedIn): ?>
                <a href="/signup/email?type=header" class="btn btn-cta btn-cta-primary"><?= __('Start %s-day Free Trial', 15);?></a>
            <?php elseif($isPaidPlan): ?>
                <a href="/" class="btn btn-cta btn-cta-primary"><?= __('Go Your Team') ?></a>
            <?php else: ?>
                <a href="/payments" class="btn btn-cta btn-cta-primary"><?= __('Upgrade to Paid Plan') ?></a>
            <?php endif; ?>
            <div class="hr"></div>
            <p><?=__('For teams and companies ready to create and share project goals on Goalous.');?></p>
        </div>
        <div class="feature-category">
            <strong class="icon icon-flag"><?= __('Goal features');?></strong>
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
                <li><?=__('Create private Circles where members can communicate');?></li>
                <li><?=__('Give reactions to posts with likes and comments');?></li>
                <li><?=__('Realtime chat messaging');?></li>
                <li><?=__('Attach and share files up to 100MB per file');?></li>
                <li><?=__('See how many members have read your posts and comments');?></li>
            </ul>
        </div>
        <div class="feature-category">
            <strong class="icon icon-lock"><?=__('Other Features');?></strong>
            <ul>
                <li><?=__('Unlimited team members');?></li>
                <li><?=__('Two-Step verification for stronger security');?></li>
                <li><?=__('English and Japanese language available');?></li>
                <li><?=__('Live chat support (Initial response will be within the next business day)');?></li>
                <li><?=__('iOS(8.4 or higher) and Android(6.0 or higher) APPs');?></li>
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
                <p><?=__('Credit cards can be used for paid plan payments. You can pay in US Dollars or Japanese Yen (for Japan only). If your organization is located in Japan, you can choose invoice payment.');?>
            </div>
        </div>
        <div class="question">
            <p class="question-entry"><strong><?= __('When does the free trial period and payment begin?');?></strong><span class="fa fa-angle-down"></span></p>
            <div class="answer">
                <p><?=__('For customers who registered on Goalous before 2017/9/29, the free trial period will end on 2017/10/15. Those who registered after 2017/9/29, the free trial period will end in 15 days. The payment begins on the day you apply for the paid plan.');?></p>
            </div>
        </div>
        <div class="question">
            <p class="question-entry"><strong><?= __('When should I apply for the paid plan?');?></strong><span class="fa fa-angle-down"></span></p>
            <div class="answer">
                <p><?=__('Please apply for the paid plan before the free trial period ends. Even if the free trial period is over, you can apply for a paid plan for a certain period (120 days).');?></p>
            </div>
        </div>
        <div class="question">
            <p class="question-entry"><strong><?= __('Are there any function limitations for free trial?');?></strong><span class="fa fa-angle-down"></span></p>
            <div class="answer">
                <p><?=__('Using Goalous on a free trial does not place, you will be able to use the same functions as a paid plan.');?></p>
            </div>
        </div>
        <div class="question">
            <p class="question-entry"><strong><?= __('What happens when the free trial period is over?');?></strong><span class="fa fa-angle-down"></span></p>
            <div class="answer">
                <p><?=__('When the free trial is over, you will only be able to use it in read-only mode for 30 days. During this time, you will not be able to action or post.');?></p>
                <p><?= __('Furthermore, when the read-only mode is over, there will be a 90 day lock mode. During this time, you cannot check any contents, only a paid plan application is possible.');?></p>
            </div>
        </div>
        <div class="question">
            <p class="question-entry"><strong><?= __('How is the paid plan fee charged?');?></strong><span class="fa fa-angle-down"></span></p>
            <div class="answer">
                <p><?=__('The paid plan is charged by calculating the number of active member usage on the day the paid plan contract starts (payment date), which is one month’s payment. “A one month period is considered to have elapsed on the day prior to the corresponding date in the month after the contract went into effect.”When there is no corresponding date, it will be by the end of the month. If the payment date is the 1st, it will be by the end of the same month.')?><br><br>
                    <?= __("Payment Period Example:");?><br>
                    2017/10/01 – 2017/10/30<br>
                    2017/10/31 – 2017/11/30<br>
                    2017/11/10 – 2017/12/09<br>
                    2018/01/31 – 2018/02/28
                </p>
            </div>
        </div>
        <div class="question">
            <p class="question-entry"><strong><?= __('Can I continue using Goalous for free?');?></strong><span class="fa fa-angle-down"></span></p>
            <div class="answer">
                <p><?=__('You cannot. Please apply for the paid plan once the free trial period is over.');?></p>
            </div>
        </div>
        <div class="question">
            <p class="question-entry"><strong><?= __('How are monthly active members charged?');?></strong><span class="fa fa-angle-down"></span></p>
            <div class="answer">
                <p><?=__('Team members who fit the following criteria are considered to be billable monthly active members:');?></p>
                <ul>
                    <li><?=__('Team members who are active by the payment date (those not deactivated by the team administrator)');?></li>
                    <li><?=__('In the event that team members were added by the team administrator between the current month’s payment date and 1 day prior to the following month’s payment date, the number of billable members will be more than the number of active members falling on the current month’s payment date. In addition, in that situation, added team member’s usage fee will be charged based on daily rate.');?></li>
                </ul>
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
                <p><?=__('Upon issuing the updated invoice, you will have 10 days to complete your payment. If this process is repeated multiple times, your company may be subject to review, and may be denied access to Goalous.');?>'
            </div>
        </div>
    </div>
</section>
<?= $this->element('Homepage/signup') ?>
<?= $this->App->viewEndComment()?>
