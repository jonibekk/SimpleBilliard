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
<?= $this->App->viewStartComment()?>

<?php $faq = [
    [
        'question' => __('What is different from other enterprise SNS?'),
        'answer'   => __('People are connected by Goal, this is the different point from others. We can follow Goals or collaborate with Goals. Just post an action to Goals, everyone can know what you did, when you did for Goals. Enjoy your team communication.')
    ],
    // Now in www prod, evaluation feature is unavailable.
    // [
    //     'question' => __('How to evaluate personal Goals?'),
    //     'answer'   => __('Evaluation can be done at end of term or in next term. There are 2 types of evaluations, Goal evaluation and total evaluation.')
    // ],
    [
        'question' => __('What should I do if a member leaves our company?'),
        'answer'   => __('The team administrator can can change the member to deactivated status. Deactivated members are unable to acess Goalous but their posting history remains.')
    ],
    [
        'question' => __('I\'m concerned about security and backup systems.'),
        'answer'   => __('We have a distributed back-up system, back-ups are done everyday. Goalous can be used securely With SSL, 2-Step Verification and Login Lock.')
    ],
    [
        'question' => __('Are there apps for smartphone and tablet?'),
        'answer'   => __('Goalous can be used on your smartphone! You can download the App through PlayStore and AppStore. You can also access Goalous on your mobile browser')
    ],
    [
        'question' => __('Could you give a more detailed explanation?'),
        'answer'   => __('Sure! Please contact us using the contact form. We are looking forward to hearing from you.')
    ],
    [
        'question' => __('Can we customize Goalous?'),
        'answer'   => __('We are unable to accept customize requests at this time. We would love to hear your feedback though! We are implementing new ideas constantly.')
    ],
    [
        'question' => __('How can we receive support?'),
        'answer'   => __('After you login, please contact us using the support tool. We will help you as soon as possible.')
    ],
    [
        'question' => __('What company manages Goalous?'),
        'answer'   => __('Colorkrew, make and deliver "Joyful!". Found on 1999, our sprits are "Open, Challanging, Link".')
    ],
]
?>
<!-- ******FAQ****** -->
<section id="faq" class="faq section has-bg-color">
    <div class="container">
        <h2 class="title text-center"><?= __('Frequent questions') ?></h2>
        <div class="row faq-lists">
            <?php foreach ($faq as $key => $value): ?>
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <div class="panel">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a data-parent="#accordion" data-toggle="collapse" class="panel-toggle"
                                   href="#faq<?= $key ?>">
                                    <i class="fa fa-plus-square"></i>
                                    <!-- 質問文 -->
                                    <?= $value['question'] ?>
                                </a>
                            </h4>
                        </div>

                        <div class="panel-collapse collapse" id="faq<?= $key ?>">
                            <div class="panel-body">
                                <!-- 回答 -->
                                <?= $value['answer'] ?>
                            </div>
                        </div>
                    </div><!--//panel-->
                </div>
                <!-- 左右を2コ1にするために右側のfaqのあとにパーティションを挿入 -->
                <?php if ($key % 2 === 1) {
                    echo '<hr class="faq-partition col-xs-12">';
                } ?>
            <?php endforeach; ?>

        </div><!--//row-->
        <div class="more text-center col-md-6 col-md-offset-3">
            <h4 class="title"><?= __('Any other questions?') ?></h4>
            <?= $this->Html->link(__('Contact us'), array('controller' => 'pages', 'action' => 'contact', 'lang' => $this->Lang->getLangCode()),
                array('class' => 'btn btn-cta btn-cta-secondary btn-lg btn-block')); ?>
        </div>
    </div><!--//container-->
</section><!--//faq-->
<?= $this->App->viewEndComment()?>
