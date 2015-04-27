<?php
/**
 * Created by PhpStorm.
 * User: saeki
 * Date: 15/04/15
 * Time: 10:35
 * @var $evaluatee
 */
?>
<!-- START app/View/Elements/Elements/modal_evaluators_status.ctp -->
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close font_33px close-design" data-dismiss="modal" aria-hidden="true"><span
                    class="close-icon">&times;</span></button>
            <h4 class="modal-title"><?= __d('gl', "%sの評価状況", $evaluatee['EvaluateeUser']['display_username']) ?></h4>
        </div>
        <div class="modal-body modal-feed-body">
            <? if (!empty($evaluators)): ?>
                <div class="row borderBottom">
                    <? foreach ($evaluators as $user): ?>
                        <?=
                        $this->element('Evaluation/evaluators_status',
                                       ['user' => $user]) ?>
                    <? endforeach ?>
                </div>
            <? else: ?>
                <?= __d('gl', "まだ、この投稿を読んだ人はいません。") ?>
            <? endif ?>
        </div>
        <div class="modal-footer modal-feed-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?= __d('gl', "閉じる") ?></button>
        </div>
    </div>
</div>
<!-- END app/View/Elements/Elements/modal_evaluators_status.ctp -->
