<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 6/6/14
 * Time: 3:19 PM
 *
 * @var CodeCompletionView $this
 * @var                    $goal
 */
?>
<!-- START app/View/Elements/Goal/modal_goal_detail.ctp -->
<div class="modal-dialog modal-dialog_300px">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close font_33px close-design" data-dismiss="modal" aria-hidden="true"><span
                    class="close-icon">&times;</span></button>
            <h4 class="modal-title"><?= __d('gl', "ゴール概要") ?></h4>
        </div>
        <div class="modal-body modal-circle-body">
            <div class="col col-xxs-12">
                <img src="<?= $this->Upload->uploadUrl($goal, 'Goal.photo', ['style' => 'x_large']) ?>">
            </div>
            <div class="col col-xxs-12">
                <? if (!empty($goal['SpecialKeyResult'])): ?>
                    <b class="font_18px font_verydark"><?= $goal['SpecialKeyResult'][0]['name'] ?></b>
                <? else: ?>
                    <span class="font_18px"><?= __d('gl', "ゴールが設定されていません") ?></span>
                <?endif; ?>
            </div>
            <div class="col col-xxs-12 bd-b mb-pb_5px">
                <?= $goal['Goal']['purpose'] ?>
            </div>
            <div class="col col-xxs-12">
                <!-- アクション、フォロワー -->
            </div>
            <div class="col col-xxs-12 bd-b mb-pb_5px">
                <div><?= __d('gl', "リーダー") ?></div>
                <? if (isset($goal['SpecialKeyResult'][0]['Leader'][0]['User'])): ?>
                    <img src="<?=
                    $this->Upload->uploadUrl($goal['SpecialKeyResult'][0]['Leader'][0]['User'],
                                             'User.photo', ['style' => 'small']) ?>"
                         style="width:32px;height: 32px;">
                    <?= h($goal['SpecialKeyResult'][0]['Leader'][0]['User']['display_username']) ?>
                <? endif; ?>
            </div>
            <div class="col col-xxs-12">
                <div><?= __d('gl', "コラボレータ") ?></div>
                <? if (isset($goal['SpecialKeyResult'][0]['Collaborator']) && !empty($goal['SpecialKeyResult'][0]['Collaborator'])): ?>
                    <? foreach ($goal['SpecialKeyResult'][0]['Collaborator'] as $collabo): ?>
                        <img src="<?=
                        $this->Upload->uploadUrl($collabo['User'],
                                                 'User.photo', ['style' => 'small']) ?>"
                             style="width:32px;height: 32px;" alt="<?= h($collabo['User']['display_username']) ?>"
                             title="<?= h($collabo['User']['display_username']) ?>">
                    <? endforeach ?>
                <? else: ?>
                    <?= __d('gl', "なし") ?>
                <?endif; ?>


            </div>


            <? $this->log($goal) ?>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?= __d('gl', "閉じる") ?></button>
        </div>
    </div>
</div>
<!-- END app/View/Elements/Goal/modal_goal_detail.ctp -->
