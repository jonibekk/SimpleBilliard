<?php
/**
 * Created by PhpStorm.
 * User: saeki
 * Date: 15/04/17
 * Time: 16:50
 */
?>
<!-- START app/View/Elements/Elements/modal_incomplete_oneself_evaluators.ctp -->
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close font_33px close-design" data-dismiss="modal" aria-hidden="true"><span
                    class="close-icon">&times;</span></button>
            <h4 class="modal-title"><?= __d('gl', "自己評価未評価者") ?></h4>
        </div>
        <div class="modal-body modal-feed-body">
            <? if (!empty($oneself_incomplete_users)): ?>
                <div class="row borderBottom">
                    <? foreach ($oneself_incomplete_users as $user): ?>
                        <?=
                        $this->element('Evaluation/incomplete_oneself',
                                       ['user' => $user['EvaluatorUser']]) ?>
                    <? endforeach ?>
                </div>
            <? else: ?>
                <?= __d('gl', "全員の評価が完了しています。") ?>
            <? endif ?>
        </div>
        <div class="modal-footer modal-feed-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?= __d('gl', "閉じる") ?></button>
        </div>
    </div>
</div>
<!-- END app/View/Elements/Elements/modal_incomplete_oneself_evaluators.ctp -->