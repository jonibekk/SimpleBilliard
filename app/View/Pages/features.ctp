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
$meta_features = [
    [
        "name"    => "description",
        "content"  => __("Goalous has many sophisticated features for teams around the world."),
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
        "content"  => __("Goalous has many sophisticated features for teams around the world."),
    ],
    [
        "property" => "og:url",
        "content"  => "https://www.goalous.com/features/",
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
$num_ogp = count($meta_features);
for ($i = 0; $i < $num_ogp; $i++) {
    echo $this->Html->meta($meta_features[$i]);
}
?>
<title><?= __('Features | Goalous') ?></title>
<link rel="alternate" hreflang="ja" href="<?= $this->Html->url('/ja/features') ?>"/>
<link rel="alternate" hreflang="en" href="<?= $this->Html->url('/en/features') ?>"/>
<link rel="alternate" hreflang="x-default" href="<?= $this->Html->url('/features') ?>"/>
<?php $this->end() ?>
<?= $this->App->viewStartComment()?>
<!-- ******FEATURES PROMO****** -->
<section id="features-promo" class="features-promo section">
    <div class="bg-mask"></div>
    <div class="container">
        <div class="row">
            <div class="features-intro col-md-5 col-sm-6 col-xs-12">
                <h2 class="title"><?= __('You can improve your job and organization!') ?></h2>
                <ul class="list-unstyled features-list">
                    <li><i class="fa fa-check"></i><?= __('Make decisions quicker.') ?></li>
                    <li><i class="fa fa-check"></i><?= __('Make your company vision clear to the employees.') ?></li>
                    <li><i class="fa fa-check"></i><?= __('Make performance evaluations transparent') ?></li>
                    <li><i class="fa fa-check"></i><?= __('Make your organization more flat.') ?></li>
                    <li><i class="fa fa-check"></i><?= __('Your work becomes joyful!') ?></li>
                </ul>
            </div><!--//intro-->
            <div class="features-video col-md-7 col-sm-6 col-xs-12 col-xs-offset-0">
                <div class="video-container">
                    <iframe width="720" height="405" src="https://www.youtube.com/embed/<?= $this->Lang->getLangCode() == LangHelper::LANG_CODE_JP ? 'jwG1Lsq3Wyw' : 'dArw8d4uh00'?>?rel=0"
                            frameborder="0" allowfullscreen></iframe>
                </div><!--//video-container-->
            </div><!--//video-->
        </div><!--//row-->
    </div><!--//container-->
</section><!--//features-promo-->

<!-- ******FEATURES****** -->
<section id="features" class="features section">
    <div class="container">
        <div class="item row flex">
            <div
                class="content col-md-5 col-sm-5 col-xs-12 pull-right col-md-offset-1 col-sm-offset-1 col-xs-offset-0 from-right col-right">
                <h3 class="title"><i class="fa fa-flag-o"></i><?= __('Make your colleages Goal clear.') ?></h3>
                <div class="details">
                    <p><?= __('What are you working on it? Why are you working on it? Make what you are doing clear to your colleagues.') ?></p>
                </div>
            </div><!--//content-->
            <div class="figure col-md-6 col-sm-6 col-xs-12 from-left col-left">
                <?= $this->Html->image('homepage/features/screenshot-1.jpg', array(
                    'alt'   => __('You can understand your colleages easily, have you ever known another way to know this?'),
                    'class' => 'img-responsive'
                )) ?>
            </div><!--//figure-->
        </div><!--//item-->

        <hr/>

        <div class="item row flex">
            <div class="content col-md-5 col-sm-5 col-xs-12 from-left col-left">
                <h3 class="title"><i class="fa fa-image"></i><?= __('A Picture is worth a thousand words! Photo Actions.') ?></h3>
                <div class="details">
                    <p>
                        <?=
                        __('Reporting, Sharing, Self-promotion! It all should be done through photos!') .
                        '<br>' .
                        __('Take a fun photo after a meeting and suddenly your work has become more joyful!')
                        ?>
                    </p>
                </div>
            </div><!--//content-->
            <div
                class="figure col-md-6 col-sm-6 col-xs-12 col-md-offset-1 col-sm-offset-1 col-xs-offset-0 from-right col-right">
                <?= $this->Html->image('homepage/features/screenshot-2.jpg',
                    array('alt' => __('Pictures let us describe better.'), 'class' => 'img-responsive')) ?>
            </div><!--//figure-->
        </div><!--//item-->

        <hr/>

        <div class="item row flex">
            <div
                class="content col-md-5 col-sm-5 col-xs-12 pull-right col-md-offset-1 col-sm-offset-1 col-xs-offset-0 from-right col-right">
                <h3 class="title"><i class="fa fa-circle-o"></i><?= __('Sharing in a circle.') ?></h3>
                <div class="details">
                    <p><?= __('Create circles, and share with project members division members or anyone in the company.') ?></p>
                </div>
            </div><!--//content-->
            <div class="figure col-md-6 col-sm-6 col-xs-12 from-left col-left">
                <?= $this->Html->image('homepage/features/screenshot-3.jpg', array(
                    'alt'   => __('Let\'s start conversation about any activities and topics!'),
                    'class' => 'img-responsive'
                )) ?>
            </div><!--//figure-->
        </div><!--//item-->

        <hr/>

        <div class="item row flex">
            <div class="content col-md-5 col-sm-5 col-xs-12 from-left col-left">
                <h3 class="title"><i class="fa fa-send-o"></i><?= __('Messaging') ?></h3>
                <div class="details">
                    <p><?= __('Communicate with your team members using the Web and Mobile app. No longer necessary to use Email.') ?></p>
                </div>
            </div><!--//content-->
            <div
                class="figure col-md-6 col-sm-6 col-xs-12 col-md-offset-1 col-sm-offset-1 col-xs-offset-0 from-right col-right">
                <?= $this->Html->image('homepage/features/screenshot-4.jpg',
                    array('alt' => __('You can use messanger in Goalous.'), 'class' => 'img-responsive')) ?>
            </div><!--//figure-->
        </div><!--//item-->

        <hr/>

        <div class="item row flex">
            <div
                class="content col-md-5 col-sm-5 col-xs-12 pull-right col-md-offset-1 col-sm-offset-1 col-xs-offset-0 from-right col-right">
                <h3 class="title"><i class="fa fa-shield"></i><?= __('Evaluations with evidence!') ?></h3>
                <div class="details">
                    <p>
                        <?=
                        __('Having a performance meeting where you ask "What have you done this quarter" is pointless! Through Goalous you already know their Goals and Actions.') .
                        '<br>' .
                        __('You have already known their Goals and Actions.')
                        ?>
                    </p>
                </div>
            </div><!--//content-->
            <div class="figure col-md-6 col-sm-6 col-xs-12 from-left col-left">
                <?= $this->Html->image('homepage/features/screenshot-5.jpg',
                    array('alt' => __('Evaluation? Do it in Goalous!'), 'class' => 'img-responsive')) ?>
            </div><!--//figure-->
        </div><!--//item-->

        <hr/>

        <div class="item row flex">
            <div class="content col-md-5 col-sm-5 col-xs-12 from-left col-left">
                <h3 class="title"><i class="fa fa-line-chart"></i><?= __('Insight') ?></h3>
                <div class="details">
                    <p><?= __('Is the work really effective? Check Goalous for the insight and ranking to see actual engagement.') ?></p>
                </div>
            </div><!--//content-->
            <div
                class="figure col-md-6 col-sm-6 col-xs-12 col-md-offset-1 col-sm-offset-1 col-xs-offset-0 from-right col-right">
                <?= $this->Html->image('homepage/features/screenshot-6.jpg', array(
                    'alt'   => __('Your team works better? Easy to know by Goalous.'),
                    'class' => 'img-responsive'
                )) ?>
            </div><!--//figure-->
        </div><!--//item-->
        <div class="container text-center">
            <h2 class="title"><?= __('Goalous: even more features to make your work joyful.') ?></h2></div>
        <div class="row">
            <div class="benefits col-md-12">

                <div class="item clearfix">
                    <div class="icon col-md-3 col-xs-12 text-center">
                        <i class="fa fa-child"></i>
                    </div><!--//icon-->
                    <div class="content col-md-9 col-xs-12">
                        <h3 class="title"><?= __('Goal Collaboration') ?></h3>
                        <p class="desc">
                            <?=
                            __('You can easily find common points in other divisions or other floors. Suddenly, these common points can lead you more efficiently to your Goals.') .
                            '<br>' .
                            __('To help each other, then you can achieve your Goals.')
                            ?>
                        </p>
                    </div><!--//content-->
                </div><!--//item-->
                <div class="item clearfix">
                    <div class="icon col-md-3  col-xs-12 text-center">
                        <i class="fa fa-venus-double"></i>
                    </div><!--//icon-->
                    <div class="content col-md-9 col-xs-12">
                        <h3 class="title"><?= __('Coach') ?></h3>
                        <p class="desc"><?= __('Find the best coach for you, and set them as your coach.') ?></p>
                    </div><!--//content-->
                </div><!--//item-->
                <div class="item clearfix">
                    <div class="icon col-md-3  col-xs-12 text-center">
                        <i class="fa fa-user"></i>
                    </div><!--//icon-->
                    <div class="content col-md-9 col-xs-12">
                        <h3 class="title"><?= __('Profile') ?></h3>
                        <p class="desc"><?= __('Company database at your fingers. Pictures together with names, what are their goals in their work and what actions have they done? All viewable through Goalous.') ?></p>
                    </div><!--//content-->
                </div><!--//item-->
                <div class="item clearfix">
                    <div class="icon col-md-3  col-xs-12 text-center">
                        <i class="fa fa-rocket"></i>
                    </div><!--//icon-->
                    <div class="content col-md-9 col-xs-12">
                        <h3 class="title"><?= __('Vision') ?></h3>
                        <p class="desc"><?= __('Do your employees really know the Company Vision? Make it more known!') ?></p>
                    </div><!--//content-->
                </div><!--//item-->
            </div>
        </div><!--//container-->
</section>

<?= $this->element('Homepage/signup') ?>
<?= $this->App->viewEndComment()?>
