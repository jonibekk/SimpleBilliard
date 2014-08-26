<!-- START app/View/Pages/features.ctp -->
<!-- start: title -->
<div class="page-title">
    <div class="section-wrapper">
        <div class="container">
            <h2>
                <?= __d('home', 'Features') ?>
            </h2>
        </div>
    </div>
</div>
<!-- end: title -->

<!-- start: features -->
<div class="section-page-features">
    <div class="section-wrapper">

        <div class="wrapper-features">
            <div class="container">
                <div class="row">

                    <div class="span6">
                        <?= $this->Html->image('homepage/photos/feature-1.png') ?>
                    </div>
                    <div class="span6">
                        <div class="separator">
                            <div class="">
                                <div class="lines">
                                    <div>
                                        <h3 class="lines-title">
                                            <?= __d('home', 'Set up a goal') ?>
                                        </h3>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <p>
                            <?=
                            __d('home',
                                "Understanding always comes after a goal is set. If you plan on going somewhere, you first need to set a destination, or you will just wander around. So, set a goal before you start working on it.")
                            ?>
                        </p>

                        <p>
                            <?=
                            __d('home',
                                "Also, goals must be clear and ambitious. Your goal should be attainable, concrete, measurable, challenging, and have a reasonable deadline.  When setting a goal, remember to be honest with your wish. Do not think about \"Have-To's\", think about \"Want-To's\". You are setting the goal for yourself, and your team. You won't be motivated if you are feeling forced or unconfident.")
                            ?>
                        </p>

                        <p>
                            <?=
                            __d('home',
                                'In addition, using Goalous, you can designate multiple lists to reach your goal. In order to accurately determine your accomplishments, you need to set a measurable value for each list.')
                            ?>
                        </p>
                    </div>

                </div>
            </div>
        </div>

        <div class="wrapper-features">
            <div class="container">
                <div class="row">

                    <div class="span6">
                        <div class="separator">
                            <div class="">
                                <div class="lines">
                                    <div>
                                        <h3 class="lines-title">
                                            <?= __d('home', 'Make it open') ?>
                                        </h3>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <p>
                            <?=
                            __d('home',
                                "Every goal you set up on Goalous will be visible to every member of your team. They will know your goal, and all of your progress. By making it open, you will gain more information than if you do it by yourself. It gives you a better understanding of your current situation, and the confidence to make the right decision.")
                            ?>
                        </p>

                        <p>
                            <?=
                            __d('home',
                                "Feeling reluctant to share your goal with others? That's the same as you are not willing to work with your team members to achieve something. If you belong to a group, why not corporate with others?  To plow ahead more efficiently, having a mutual aim is essential.")
                            ?>
                        </p>

                    </div>

                    <div class="span6">
                        <?=
                        $this->Html
                            ->image('homepage/photos/feature-2.png',
                                    array(
                                        'class' => 'pull-right'
                                    ))
                        ?>
                    </div>

                </div>
            </div>
        </div>

        <div class="wrapper-features">
            <div class="container">
                <div class="row">

                    <div class="span6">
                        <?= $this->Html->image('homepage/photos/feature-3.png') ?>
                    </div>
                    <div class="span6">
                        <div class="separator">
                            <div class="">
                                <div class="lines">
                                    <div>
                                        <h3 class="lines-title">
                                            <?= __d('home', 'Help each other') ?>
                                        </h3>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <p>
                            <?=
                            __d('home',
                                'On Goalous, you can collaborate with others towards a goal. Choose members you would like to work with, send an invitation message to them, and assign them roles. If they accept your request, they will become your collaborators.')
                            ?>
                        </p>

                        <p>
                            <?=
                            __d('home',
                                'In addition, the goal you invited others into will be shared with your collaborators. They can work on the goal and record their progress on Goalous.')
                            ?>
                        </p>
                    </div>

                </div>
            </div>
        </div>

    </div>
</div>
<!-- end: features -->
<!-- start: features -->
<div class="section-page-features">
    <div class="section-wrapper">

        <div class="wrapper-features">
            <div class="container">
                <div class="row-fluid">

                    <div class="span6">
                        <div class="separator">
                            <div class="">
                                <div class="lines">
                                    <div>
                                        <h3 class="lines-title">
                                            <?= __d('home', 'Send compliments') ?>
                                        </h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <p>
                            <?=
                            __d('home',
                                'It is said that when someone is complimented, their brain becomes stimulated and shows better performance.Even you have probably experienced it before, right? On Goalous, you can send various kinds of emblems to each other to give them a pat on shoulder. You can even let all the team members know that you sent someone a compliment.')
                            ?>
                        </p>

                        <p>
                            <?=
                            __d('home',
                                "Those small \"thank you\" s can make a big impact on one's motivation.") ?>
                        </p>

                    </div>

                    <div class="span6">
                        <?=
                        $this->Html
                            ->image('homepage/photos/feature-4.png',
                                    array(
                                        'class' => 'pull-right'
                                    ))
                        ?>
                    </div>

                </div>
            </div>
        </div>

    </div>
</div>
<!-- end: features -->

<!-- start: social icon -->
<div class="section-wrapper section-icon social-icon">
    <div class="container">
        <div class="row">

            <div class="social-icon-phone clear-phone">
                <div class="span1 offset3">
                    <div class="flip-container">
                        <div class="flipper">
                            <div class="front">
                                <div class="flip-a">
                                    <a href="http://www.facebook.com/goalous"><i
                                            class="icon-facebook icon-2x"></i></a>
                                </div>
                            </div>
                            <div class="back">
                                <div class="flip-b facebook">
                                    <a href="http://www.facebook.com/goalous"><span
                                            class="icon-back"><i class="icon-facebook icon-2x"></i></span></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="span1">
                    <div class="flip-container">
                        <div class="flipper">
                            <div class="front ">
                                <div class="flip-a">
                                    <a href="https://twitter.com/goalous"><i
                                            class="icon-twitter icon-2x"></i></a>
                                </div>
                            </div>
                            <div class="back">
                                <div class="flip-b twitter">
                                    <a href="https://twitter.com/goalous"><span
                                            class="icon-back"><i class="icon-twitter icon-2x"></i></span></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="span1">
                    <div class="flip-container">
                        <div class="flipper">
                            <div class="front ">
                                <div class="flip-a">

                                    <a href="https://plus.google.com/u/0/b/114558325021409202574/114558325021409202574/posts"><i
                                            class="icon-google-plus icon-2x"></i></a>
                                </div>
                            </div>
                            <div class="back">
                                <div class="flip-b google">
                                    <a
                                        href="https://plus.google.com/u/0/b/114558325021409202574/114558325021409202574/posts"><span
                                            class="icon-back"><i class="icon-google-plus icon-2x"></i></span></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="social-icon-phone clear-phone">
                <div class="span1">
                    <div class="flip-container">
                        <div class="flipper">
                            <div class="front ">
                                <div class="flip-a">
                                    <a href="https://pinterest.com/goalous/"><i
                                            class="icon-pinterest icon-2x"></i></a>
                                </div>
                            </div>
                            <div class="back">
                                <div class="flip-b pinterest">
                                    <a href="https://pinterest.com/goalous/"><span
                                            class="icon-back"><i class="icon-pinterest icon-2x"></i></span></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<!-- end: social icon -->
<? $this->append('ogp') ?>
<meta property="og:site_name" content="Goalous"/>
<meta property="og:type" content="article"/>
<meta property="og:title" content="Goalous"/>
<meta property="og:description"
      content="<?= __d('home', "Collaborative achievement goal tool") ?>"/>
<meta property="og:image" content=""/>
<meta property="og:url" content="https://www.goalous.com/"/>
<? $this->end() ?>

<?
$this->Html
    ->meta('description',
           __d('home',
               "Goalous is a tool that all members of a team are able to action in high motivated toward the achievement of self goals.Because the members create public goals in a team,they collaborate each other and get praise to your actions."),
           array(
               'inline' => false
           ));
?>
<!-- END app/View/Pages/features.ctp -->
