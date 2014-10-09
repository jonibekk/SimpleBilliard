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
                <div class="col col-xxs-6">
                    <img src="<?= $this->Upload->uploadUrl($goal, 'Goal.photo', ['style' => 'large']) ?>" width="128"
                         height="128">
                </div>
                <? if ($goal['Goal']['user_id'] != $this->Session->read('Auth.User.id') && isset($goal['SpecialKeyResult'][0]) && !empty($goal['SpecialKeyResult'][0])): ?>
                    <div class="col col-xxs-6">
                        <? if (empty($goal['SpecialKeyResult'][0]['MyFollow'])) {
                            $follow_class = 'follow-off';
                            $follow_style = null;
                            $follow_text = __d('gl', "フォロー");
                        }
                        else {
                            $follow_class = 'follow-on';
                            $follow_style = 'display:none;';
                            $follow_text = __d('gl', "フォロー中");
                        }?>
                        <? if (isset($goal['SpecialKeyResult'][0]['MyCollabo']) && !empty($goal['SpecialKeyResult'][0]['MyCollabo'])) {
                            $collabo_class = 'collabo-on';
                            $collabo_style = 'display:none;';
                            $collabo_text = __d('gl', "コラボり中");
                            $follow_disabled = "disabled";
                        }
                        else {
                            $collabo_class = 'collabo-off';
                            $collabo_style = null;
                            $collabo_text = __d('gl', "コラボる");
                            $follow_disabled = null;
                        }?>
                        <a class="btn btn_pureWhite bd-circle_20 pull-right mt_16px toggle-follow font_verydark-white <?= $follow_class ?>"
                           href="#" <?= $follow_disabled ?>="<?= $follow_disabled ?>"
                        data-class="toggle-follow"
                        kr-id="<?= $goal['SpecialKeyResult'][0]['id'] ?>">
                        <i class="fa fa-heart font_rougeOrange" style="<?= $follow_style ?>"></i>
                        <span class="ml_5px"><?= $follow_text ?></span>
                        </a>
                        <a class="btn btn_pureWhite bd-circle_20 pull-right mt_16px font_verydark-white <?= $collabo_class ?>"
                           data-toggle="modal"
                           data-target="#ModalCollabo_<?= $goal['SpecialKeyResult'][0]['id'] ?>" href="#">
                            <i class="fa fa-child font_rougeOrange font_18px" style="<?= $collabo_style ?>"></i>
                            <span class="ml_5px font_14px"><?= $collabo_text ?></span>
                        </a>
                    </div>
                <? endif; ?>
            </div>
            <? if (isset($goal['SpecialKeyResult'][0]) && !empty($goal['SpecialKeyResult'][0])): ?>
                <div class="col col-xxs-12">
                    <b class="font_18px font_verydark"><?= $goal['SpecialKeyResult'][0]['name'] ?></b>
                </div>
                <div class="col col-xxs-12 bd-b mb-pb_5px">
                    <?= $goal['Goal']['purpose'] ?>
                </div>
                <div class="col col-xxs-12 bd-b mb-pb_5px">
                    <div><?= __d('gl', '程度') ?></div>
                    <div><?= __d('gl', '単位: %s', KeyResult::$UNIT[$goal['SpecialKeyResult'][0]['value_unit']]) ?></div>
                    <? if ($goal['SpecialKeyResult'][0]['value_unit'] != KeyResult::UNIT_BINARY): ?>
                        <div><?= __d('gl', '達成時: %s', (double)$goal['SpecialKeyResult'][0]['target_value']) ?></div>
                        <div><?= __d('gl', '開始時: %s', (double)$goal['SpecialKeyResult'][0]['start_value']) ?></div>
                    <? endif; ?>
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
            <? endif; ?>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?= __d('gl', "閉じる") ?></button>
        </div>
    </div>
</div>
<!-- END app/View/Elements/Goal/modal_goal_detail.ctp -->
