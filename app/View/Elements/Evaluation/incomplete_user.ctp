<?php
/**
 * Created by PhpStorm.
 * User: saeki
 * Date: 15/04/15
 * Time: 10:28
 */
?>
<!-- START app/View/Elements/Evaluation/incomplete_user.ctp -->
<div class="col col-xxs-12 mpTB0">
    <?=
    $this->Upload->uploadImage($user, 'User.photo', ['style' => 'small'],
                               ['class' => 'comment-img'])
    ?>
    <div class="comment-body modal-comment" style="margin-top:5px;">
        <div class="font_12px font_bold modalFeedTextPadding">
            <?= h($user['display_username']) ?>
            <a class="modal-ajax-get pointer"
               href="<?= $this->Html->url(['controller' => 'evaluations', 'action' => 'ajax_get_evaluators_status', $user['id']]) ?>">
                <?= __d('gl', "詳細を見る") ?>
            </a>
        </div>
        <div class="font_12px modalFeedTextPadding">
            <?= __d('gl', "残り") ?> <?= h($user['incomplete_count']) ?>
            <? if ($user['incomplete_count'] == 1): ?>
                (<?= __d('gl', "最終評価のみ") ?>)
            <? endif ?>
        </div>
    </div>
</div>
<!-- END app/View/Elements/Evaluation/incomplete_user.ctp -->
