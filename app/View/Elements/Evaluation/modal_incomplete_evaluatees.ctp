<?php
/**
 * Created by PhpStorm.
 * User: saeki
 * Date: 15/04/14
 * Time: 17:35
 */

?>
<!-- START app/View/Elements/Elements/modal_incomplete_evaluatees.ctp -->
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close font_33px close-design" data-dismiss="modal" aria-hidden="true"><span
                    class="close-icon">&times;</span></button>
            <h4 class="modal-title"><?= __d('gl', "未完了の被評価者(%s)", count($incomplete_evaluatees)) ?></h4>
        </div>
        <div class="modal-body modal-feed-body">
            <? if (!empty($incomplete_evaluatees)): ?>
                <div class="row borderBottom">
                    <? foreach ($incomplete_evaluatees as $user): ?>
                        <?=
                        $this->element('Feed/read_like_user',
                                       ['user' => $user['User'], 'created' => $user['User']['created']]) ?>
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
<!-- END app/View/Elements/Elements/modal_incomplete_evaluatees.ctp -->
