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
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @var CodeCompletionView
 * @var
 * @var
 */
?>
<?= $this->App->viewStartComment()?>
<?php $this->append('meta') ?>
<?php
$isLangEn =  $this->Lang->getLangCode() == LangHelper::LANG_CODE_EN;
/*
- meta description
- og:description
- og:url
- title
 */
$meta_lp = [
    [
        "name"    => "description",
        "content" => __("Use GKA(Goal-Key Result-Action) invented based on OKR methodology to improve communication across your organization."),
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
        "content"  => __('OKR and Communication Software | Goalous'),
    ],
    [
        "property" => "og:description",
        "content"  => __("Use GKA(Goal-Key Result-Action) invented based on OKR methodology to improve communication across your organization."),
    ],
    [
        "property" => "og:url",
        "content"  => "https://www.goalous.com/",
    ],
    [
        "property" => "og:image",
        "content"  => AppUtil::fullBaseUrl(ENV_NAME)."/img/homepage/top/ogp". ($isLangEn ? '_en' : '').".jpg",
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
$num_ogp = count($meta_lp);
for ($i = 0; $i < $num_ogp; $i++) {
    echo $this->Html->meta($meta_lp[$i]);
}
?>
<title><?= __('Goalous | Enjoy your work. Achieve your Goal.') ?></title>
<link rel="alternate" hreflang="ja" href="<?= $this->Html->url('/ja/') ?>"/>
<link rel="alternate" hreflang="en" href="<?= $this->Html->url('/en/') ?>"/>
<link rel="alternate" hreflang="x-default" href="<?= $this->Html->url('/') ?>"/>
<?php $this->end() ?>

<?php
$easierInputIconPath = '/img/homepage/top/easier_input_icon'.($isLangEn ? '_en' : '').'.svg';
$bannerSeminarPath = '/img/homepage/top/seminar_banner'.($isLangEn ? '_en' : '').'.jpg';
$contactPageUrl = ($isLangEn ? '/en' : '').'/contact';
?>
<!-- ******PROMO****** -->
<?php if ($isLangEn):?>
    <style>
    .main_visual .visual_container .text_area {
        width: 720px;
        left: 43%;
        position: absolute;
    }

    .main_visual .visual_container .team_amount {
        font-size: 3rem;
        color: #C20819;
        font-weight: 600;
        margin: 45px 0 0 90px;
    }

    @media screen and (max-width: 1029px) {
        .main_visual .visual_container .text_area {
            width: 100%;
            position: static;
            text-align: center;
        }

        .main_visual .visual_container .team_amount {
            font-size: 5vw;
            margin: 0 auto;
        }
    }
    .main_visual .visual_container h2 {
        font-size: 50px;
        margin: 20px 0 45px;
        line-height: 90px;
        letter-spacing: 3px;
    }

    @media screen and (max-width: 1029px) {
        .main_visual .visual_container h2 {
            width: 50%;
            font-size: 5vw;
            line-height: 8vw;
            letter-spacing: 3px;
            text-align: left;
            padding-left: 10px;
            margin: 0;
        }
    }
    .main_visual .visual_container .inq_btn_cnt .baloon {
        position: absolute;
        top: -50px;
        right: 140px;
        z-index: 100;
    }

    @media screen and (max-width: 1029px) {
        .main_visual .visual_container .inq_btn_cnt .baloon {
            position: absolute;
            top: -50px;
            right: 0;
            z-index: 100;
            width: 70px;
        }
    }
    @media screen and (max-width: 1029px) {
        .main_visual .visual_container .inq_btn_cnt .inq_btn {
            display: block;
            width: 100%;
            background-color: #C20819;
            padding: .6em 1em;
            font-size: 1.5em;
        }
    }
    @media screen and (max-width: 1029px) {
        .cnt_container_gray h3.borderttl {
            width: auto;
            font-size: 1.8em;
            border-bottom: none;
            padding: 10px 0 5px;
            margin: 50px auto 35px;
            font-weight: bold;
            background: -webkit-gradient(linear, left top, left bottom, color-stop(70%, transparent), color-stop(0%, #C20819));
            background: linear-gradient(transparent 95%, #C20819 0%);
            display: inline;
            line-height: 50px;
        }
    }

</style>
<?php endif;?>
<section class="main_visual">
    <div class="visual_container">
        <img src="/img/homepage/top/mainvisual_parts.svg" class="main_img pcscreen">
        <div class="text_area">
            <p class="team_amount"><?= __('%s teams now on-board!', 800)?></p>

            <p class="main_explain"><?= __('Introducing “Goalous”<br class="spbr"> the next generation OKR SNS') ?></p>
            <div class="spflex">
                <img src="/img/homepage/top/mainvisual_parts.svg" class="main_img_sp spscreen">
                <h2><?= __('Your organization will change drastically.')?></h2>
            </div>
            <div class="inq_btn_cnt">
                <img src="<?= $easierInputIconPath?>" class="baloon" alt="">
                <a href="<?= $contactPageUrl?>" class="inq_btn"><img src="/img/homepage/top/mail_icon_w.svg"
                                                                                       height="28px" class="svgs"><?= __('Contact us')?></a>
            </div>
        </div>
    </div>
    <div class="seminar_banner pcscreen">
        <a href="https://peatix.com/group/66244" target="_blank"><img src="<?=$bannerSeminarPath?>"
                                                                      alt="<?= ('Free Goalous Seminar!Learn all of the ways you can improve your organization using Goalous.')?>"
                                                                      width="100%"></a>
    </div>
</section>

<section class="">
    <div class="cnt_container_gray">
        <div class="seminar_banner spscreen">
            <a href="https://peatix.com/group/66244" target="_blank">
                <img src="/img/homepage/top/seminar_sp_img.jpg" alt="<?= ('Free Goalous Seminar!Learn all of the ways you can improve your organization using Goalous.')?>" width="100%">
                <h3 class="seminarTtl"><?= __('Free Goalous Seminar!')?></h3>
                        <p class="seminarDetail"><?= ('Learn all of the ways you can improve your organization using Goalous.')?></p>
                        <a href="https://peatix.com/group/66244" target="_blank" class="seminarLink"><?= ('View more details')?></a>
            </a>
        </div>
        <h3 class="borderttl fadein"><?= __("Do you have these problems within your organization?<br class=\"spbr\">")?></h3>
        <ul class="intro_detail">
            <li class="fadein">
                <span><?= __("Individual")?></span>
                <img src="/img/homepage/top/intro_img01.svg" height="200px" class="introart">
                <h4><?= __("Employees being passive")?></h4>
                <p><?= __("Employees do their work seriously, but they are not pro-active. It is caused by company’s vision not permeate employees. If employees understand the management’s principles more, they would be able to know what to do, and creating actions towards own’s goals.")?></p>
            </li>
            <li class="fadein">
                <span><?= __("Department")?></span>
                <img src="/img/homepage/top/intro_img02.svg" height="200px" class="introart">
                <h4><?= __("The team has no sense of unity")?></h4>
                <p><?= __("A sense of unity at school festivals and clubs! Do you think that it is not related to working adults?There is no sense of unity if you are blind to other people’s goals.Understanding each other is the first step to creating a sense of unity in your company.")?></p>
            </li>
            <li class="fadein">
                <span><?= __("Organization")?></span>
                <img src="/img/homepage/top/intro_img03.svg" height="200px" class="introart">
                <h4><?= __("Nothing “new” is born in organization")?></h4>
                <p><?= __("In this fast-changing era, those who do not change cannot survive.In order to make a change, a chain of communication in various places is necessary.With “proactive individuals” and “united team”, a new collaboration is formed and something “new” will be created!")?></p>
            </li>
        </ul>
    </div>
    <div class="cnt_container pattern1">
        <div class="introduction">
            <h3 class="fadein"><?= __("“Goalous solves those problems!”")?></h3>
            <img src="/img/homepage/top/intro_edo.jpg" class="spscreen fadein">
            <p class="fadein"><?= __("\"OKR\" is said to be an excellent goal management format worldwide. You may have heard of it before.           Based on this “OKR” concept, we have developed a new goal management framework for creating “pro-activeness,” “team unity,” and “fun”. That is “GKA”.           Goalous is the only service that incorporates GKA, which can be said to be compatible with OKR, into the social networking system.           Now, let’s create something “new” in your organization with Goalous.")?></p>
            <p class="supplement fadein"><?= __("* OKR is an abbreviation for Objectives and Key Results. It is a model created by Intel in the 1970s that is the best practice to connect the goals of organizations and employees and measure progress based on the results achieved. Currently, it is used in Google, Uber, LinkedIn, Twitter, Sears, Zynga, Oracle, Yahoo!, Spotify, Box, GoPro, Flipboard, etc.")?></p>
            <p><a href="/blog" target="_blank" class="arrow textlink"><?= __("Learn more about GKA（Check out our Goalous Blog）")?></a></p>
            <img src="/img/homepage/top/intro_edo.jpg" height="520px" class="pcscreen fadein">
        </div>
    </div>
</section>

<div class="cnt_container_inq">
    <h3 class="fadein"><?= __("Start today, Goalous!")?></h3>
    <div class="inq_btn_cnt fadein">
        <img src="<?= $easierInputIconPath?>" class="baloon" alt="">
        <a href="<?= $contactPageUrl?>" class="inq_btn"><img src="/img/homepage/top/mail_icon.svg" class="svgs"><?= __("Contact us")?></a>
    </div>
</div>

<section class="">
    <div class="cnt_container pattern2">
        <h3 class="functionTtl"><?= __("“So it can be solved, Image of using Goalous”")?></h3>
        <div class="function">
            <div class="detail1 fadein">
                <img src="/img/homepage/top/function_img01.svg" class="" alt="">
                <h4><?= __("Make a goal")?></h4>
                <p><?= __("Let ’s set the objectives of the team to “Goal”. It is also important to set a “key result” to be done to achieve the goal. All members collaborate and share goals. If the goals are clear, employees will be able to think and act pro-actively on what to do to achieve it.")?></p>
            </div>
            <div class="detail2 fadein">
                <img src="/img/homepage/top/function_img02.svg" class="" alt="">
                <h4><?= __("Take action")?></h4>
                <p><?= __("Take action to achieve your goals. Photo action is more intuitive than words. Everyone knows the action is for what goal.             Express your thoughts with Like! and comments. A deeper understanding of communication with each other. Various actions give you a sense of the whole company and create a sense of unity.")?></p>
            </div>
            <div class="detail3 fadein">
                <img src="/img/homepage/top/function_img03.svg" class="" alt="">
                <h4><?= __("A new one is born")?></h4>
                <p><?= __("“I ’d like to introduce you to the company,” “I ’ll help you with that job,” “Would you like to change this rule?” A new kind of communication is born in the same direction. Let's start with the first action!")?></p>
            </div>
            <div class="detail4 fadein">
                <img src="/img/homepage/top/function_img04.svg" class="" alt="">
                <h4><?= __("Evaluate")?></h4>
                <p><?= __("Goalous has features that can be evaluated by looking at the progress of the goal and all actions. If you look at the action, you can feel not only the efforts of the members but also the heat of the thoughts. Employees who are evaluated can obtain a more satisfactory evaluation.")?></p>
            </div>
        </div>
</section>

<section class="casestudy">
    <div class="cnt_container_gray">
        <h3 class="casestudy"><?= __("CASE STUDIES")?></h3>
        <ul class="case_detail fadein">
            <li>
                <a href="/case_study?company=yabusaki">
                    <h4 class="csttl"><img src="/img/homepage/top/casestudy_logo_yabusaki.png" class="yabusaki" alt="ヤブサキ産業株式会社"
                                           height="45px"></h4>
                    <p><?= __("Interactive communication between management and employees have become more active than ever before.")?></p>
                </a>
            </li>
            <li>
                <a href="/case_study">
                    <h4 class="csttl"><img src="/img/homepage/top/casestudy_logo_witone.png" class="witone" alt="株式会社ウィットワン" height="45px">
                    </h4>
                    <p><?= __("Promote communication with amazing speed. Team members taking initiatives increased marginally.")?></p>
                </a>
            </li>
        </ul>
    </div>
</section>

<div class="cnt_container_inq">
    <h3 class="fadein"><?= __("Let's go to Goalous!")?></h3>
    <div class="inq_btn_cnt fadein">
        <img src="<?= $easierInputIconPath?>" class="baloon" alt="">
        <a href="<?= $contactPageUrl?>" class="inq_btn"><img src="/img/homepage/top/mail_icon.svg" class="svgs"><?= __("Contact us")?></a>
    </div>
</div>

<section class="">
    <div class="cnt_container pattern3 seminar_attended">
        <h3><?= __("Feedback from our seminar participants")?></h3>
        <p><?= __("We hold Organizational reform seminars regularly. This seminar is very popular every time for <br>CEO’s, upper / lower management, HR Team, team management and those who want to improve their organizational capabilities.<br> Please feel free to join us at our next seminar!")?></p>
        <ul>
            <li class="fadein"><p><?= __("I felt that it was ideal to go from fairness to a fun world and apply that mind to business.")?></p></li>
            <li class="fadein"><p><?= __("The way of thinking was ideal but I was quite skeptical about making that a reality. But hearing that ISAO has achieved that ideal, I would like to observe how ISAO grows with this ideal moving forward.")?></p></li>
            <li class="fadein"><p><?= __("I started visualizing our evaluations at my company, so I would like to consider using Goalous for that as well.")?></p></li>
            <li class="fadein"><p><?= __("It was very interesting! A lot of keywords came to mind that applied to my company as well.")?></p></li>
            <li class="fadein"><p><?= __("I thought about our current internal corporate plans and personnel goal management and it made me want to make that process more enjoyable for all parties involved.")?></p></li>
            <li class="fadein"><p><?= __("Thanks for today. I will apply GKA for my organization as well.")?></p></li>
            <li class="fadein"><p><?= __("It was an interesting content that I wanted our management to hear.")?></p></li>
            <li class="fadein"><p><?= __("I thought that the organization could change depending on the approach.")?></p></li>
            <li class="fadein"><p><?= __("I thought it was nice to be able to hit the spotlight to all the employees.")?></p></li>
        </ul>
        <div class="seminar_banner pcscreen fadein">
            <a href="https://peatix.com/group/66244" target="_blank"><img src="<?=$bannerSeminarPath?>"
                                                                          alt="<?= ('Free Goalous Seminar!Learn all of the ways you can improve your organization using Goalous.')?>"
                                                                          width="100%"></a>
        </div>
    </div>
    <div class="seminar_banner spscreen fadein">
        <a href="https://peatix.com/group/66244" target="_blank">
            <img src="/img/homepage/top/seminar_sp_img.jpg" alt="<?= ('Free Goalous Seminar!Learn all of the ways you can improve your organization using Goalous.')?>" width="100%">
            <h3 class="seminarTtl"><?= __('Free Goalous Seminar!')?></h3>
            <p class="seminarDetail"><?= __('Learn all of the ways you can improve your organization using Goalous.')?></p>
            <a href="https://peatix.com/group/66244" target="_blank" class="seminarLink"><?= __('View more details')?></a>
        </a>
    </div>
</section>

<section class="">
    <div class="cnt_container_black">
        <h3><?= __("Check out the video for Goalous")?></h3>
        <dl class="fadein">
            <dd>
                <a href="#" id="play-trigger" class="play-trigger" data-toggle="modal" data-target="#tour-video">
                    <img src="/img/homepage/top/movie_thumb01.jpg" width="400px" alt="<?= __("Introduction video for Goalous")?>">
                </a></dd>
            <dt><?= __("Introduction video for Goalous")?></dt>
        </dl>
        <!-- Video Modal -->
        <div class="modal modal-video" id="tour-video" tabindex="-1" role="dialog" aria-labelledby="videoModalLabel"
             aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 id="videoModalLabel" class="modal-title"><?= __('About Goalous') ?></h4>
                    </div>
                    <div class="modal-body">
                        <div class="video-container">
                            <iframe id="promoVideo"
                                    data-src="<?= $this->Lang->getLangCode() == LangHelper::LANG_CODE_JP ? 'jwG1Lsq3Wyw' : 'dArw8d4uh00'?>"
                                    width="720"
                                    height="405"
                                    frameborder="0"
                                    webkitallowfullscreen
                                    mozallowfullscreen
                                    allowfullscreen>
                            </iframe>
                        </div><!--//video-container-->
                    </div><!--//modal-body-->
                </div><!--//modal-content-->
            </div><!--//modal-dialog-->
        </div><!--//modal-->

    </div>
</section>

<div class="cnt_container_inq_bigger">
    <h3 class="fadein"><?= __("Let's go to Goalous!")?></h3>
    <div class="inq_btn_cnt fadein">
        <img src="<?= $easierInputIconPath?>" class="baloon" alt="">
        <a href="<?= $contactPageUrl?>" class="inq_btn"><img src="/img/homepage/top/mail_icon.svg" class="svgs"><?= __("Contact us")?></a>
    </div>
</div>

<?php $this->append('script'); ?>
<script type="text/javascript">
    require.config({
        baseUrl: '/js/modules/'
    });
    $(document).ready(function () {
        // 登録可能な email の validate
        require(['validate'], function (validate) {
            window.bvCallbackAvailableEmailNotVerified = validate.bvCallbackAvailableEmailNotVerified;
        });
        $('#HomeSignupEmail').bootstrapValidator({
            container: "#HomeEmailErrorContainer"
        });
    });
</script>
<?php $this->end(); ?>

<?= $this->App->viewEndComment()?>
