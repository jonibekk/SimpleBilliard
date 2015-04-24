<?php
/**
 * Created by PhpStorm.
 * User: saeki
 * Date: 15/04/15
 * Time: 18:42
 * @var $user
 */
?>
<!-- START app/View/Elements/Evaluation/evaluators_status.ctp -->
<div class="col col-xxs-12 mpTB0">
    <? if($user['Evaluation']['evaluate_type'] == Evaluation::TYPE_FINAL_EVALUATOR): ?>
        <i class="fa fa-user user-icon fa-2x text-align_c comment-img mt_5px"></i>
    <? else: ?>
        <?=
        $this->Upload->uploadImage($user['EvaluatorUser'], 'User.photo', ['style' => 'small'],
                                   ['class' => 'comment-img'])
        ?>
    <? endif;?>
    <div class="comment-body modal-comment" style="margin-top:5px;">
        <div class="font_12px font_bold modalFeedTextPadding">
            <? if($user['Evaluation']['evaluate_type'] != Evaluation::TYPE_FINAL_EVALUATOR): ?>
                <?= h($user['EvaluatorUser']['display_username']) ?> ・
            <? endif ?>
            <span><?= h($user['Evaluation']['evaluator_type_name']) ?></span>

            <div class="font_12px font_lightgray modalFeedTextPaddingSmall">
                <? if ($user['Evaluation']['status'] == Evaluation::TYPE_STATUS_DONE): ?>
                    <?= __d('gl', "完了") ?>(<?= $this->TimeEx->elapsedTime(h($user['Evaluation']['modified']),
                                                                          viaIsSet($type)) ?>)
                <? else: ?>
                    <span style="color:red"><?= __d('gl', "未完了") ?></span>
                <? endif; ?>
            </div>
        </div>
    </div>
</div>
<!-- END app/View/Elements/Evaluation/evaluators_status.ctp -->
