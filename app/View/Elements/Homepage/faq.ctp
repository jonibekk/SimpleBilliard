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
        'question' => __('What should I do if the member leave our company?'),
        'answer'   => __('Team administrator can make the member left deactivated. Those members can\'t access your team but the posts they did would remain your team.')
    ],
    [
        'question' => __('I\'m concerned about security and backup systems.'),
        'answer'   => __('We save data as distribution. We reserve backups everyday. Using SSL, 2-Step Verification and Login lock, you can use Goalous with safe.')
    ],
    [
        'question' => __('Is there apps for smartphone and tablet?'),
        'answer'   => __('You can download Goalous on AppStore and PlayStore. You can use our application on your mobile. Also, you can access to Goalous on your mobile browser.')
    ],
    [
        'question' => __('Can we ask you to explain more in detail?'),
        'answer'   => __('Sure! Please contact with us from contact form. We are looking forward your contact!')
    ],
    [
        'question' => __('What should we do after the campaign?'),
        'answer'   => __('In order to continue using Goalous, you need to purchase a subscription plan.')
    ],
    [
        'question' => __('Can we customize Goalous?'),
        'answer'   => __('Definitely not, but we hope to get your feedback. If we find it as good idea, we immediately accept it.')
    ],
    [
        'question' => __('Could we ask you to support us?'),
        'answer'   => __('After logged in, you can get contact with us by support tool. We can help you as soon as possible.')
    ],
    [
        'question' => __('Which company offer Goalous?'),
        'answer'   => __('ISAO, make and deliever "Joyful!". Found on 1999, our sprits are "Open, Challanging, Link".')
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
            <h4 class="title"><?= __('Any other question?') ?></h4>
            <?= $this->Html->link(__('Contact us'), array('controller' => 'contact'),
                array('class' => 'btn btn-cta btn-cta-secondary btn-lg btn-block')); ?>
        </div>
    </div><!--//container-->
</section><!--//faq-->
<?= $this->App->viewEndComment()?>
