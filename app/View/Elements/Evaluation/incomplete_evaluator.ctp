<?php
/**
 * Created by PhpStorm.
 * User: saeki
 * Date: 15/04/17
 * Time: 15:07
 * @var $user
 */
?>
<!-- START app/View/Elements/Evaluation/incomplete_evaluator.ctp -->
<div class="col col-xxs-12 mpTB0">
    <?=
    $this->Upload->uploadImage($user, 'User.photo', ['style' => 'small'],
                               ['class' => 'comment-img'])
    ?>
    <div class="comment-body modal-comment" style="margin-top:5px;">
        <div class="font_12px font_bold modalFeedTextPadding">
            <?= h($user['display_username']) ?>
            <a class="modal-ajax-get pointer"
               href="<?= $this->Html->url(['controller' => 'evaluations', 'action' => 'ajax_get_evaluatees_by_evaluator', $user['id']]) ?>">
                <?= __d('gl', "詳細を見る") ?>
            </a>
        </div>
        <div class="font_12px modalFeedTextPadding">
            <?= __d('gl', "残り") ?> <?= h($user['incomplete_count']) ?>
        </div>
    </div>
</div>
<!-- END app/View/Elements/Evaluation/incomplete_evaluator.ctp -->
