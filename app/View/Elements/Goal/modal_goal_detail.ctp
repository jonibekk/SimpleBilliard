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
            <button type="button" class="close font_33px close-design" data-dismiss="modal" aria-hidden="true">
                <span class="close-icon">&times;</span></button>
            <h4 class="modal-title"><?= __d('gl', "ゴール概要") ?></h4>
        </div>
        <div class="modal-body modal-circle-body">
            <div class="col col-xxs-12">
                <div class="col col-xxs-6">
                    <img src="<?= $this->Upload->uploadUrl($goal, 'Goal.photo', ['style' => 'large']) ?>" width="128"
                         height="128">
                </div>
                <? if ($goal['Goal']['user_id'] != $this->Session->read('Auth.User.id') && isset($goal['Goal']) && !empty($goal['Goal'])): ?>
                    <div class="col col-xxs-6">
                        <? if (empty($goal['MyFollow']) && !viaIsSet($goal['User']['TeamMember'][0]['coach_user_id'])) {
                            $follow_class = 'follow-off';
                            $follow_style = null;
                            $follow_text = __d('gl', "フォロー");
                            $follow_disabled = null;
                        }
                        elseif (viaIsSet($goal['User']['TeamMember'][0]['coach_user_id'])) {
                            $follow_class = 'follow-off';
                            $follow_style = null;
                            $follow_text = __d('gl', "フォロー");
                            $follow_disabled = "disabled";
                        }
                        else {
                            $follow_class = 'follow-on';
                            $follow_style = 'display:none;';
                            $follow_text = __d('gl', "フォロー中");
                            $follow_disabled = null;
                        } ?>
                        <? if (isset($goal['MyCollabo']) && !empty($goal['MyCollabo'])) {
                            $collabo_class = 'collabo-on';
                            $collabo_style = 'display:none;';
                            $collabo_text = __d('gl', "コラボり中");
                            $follow_disabled = "disabled";
                        }
                        else {
                            $collabo_class = 'collabo-off';
                            $collabo_style = null;
                            $collabo_text = __d('gl', "コラボる");
                        } ?>
                        <a class="btn btn-white bd-circle_22px pull-right mt_16px toggle-follow font_verydark <?= $follow_class ?>"
                           href="#" <?= $follow_disabled ?>="<?= $follow_disabled ?>"
                        data-class="toggle-follow"
                        goal-id="<?= $goal['Goal']['id'] ?>">
                        <i class="fa fa-heart font_rougeOrange" style="<?= $follow_style ?>"></i>
                        <span class="ml_5px"><?= $follow_text ?></span>
                        </a>
                        <a class="btn btn-white bd-circle_22px pull-right mt_16px font_verydark-white modal-ajax-get-collabo <?= $collabo_class ?>"
                           data-toggle="modal"
                           data-target="#ModalCollabo_<?= $goal['Goal']['id'] ?>"
                           href="<?= $this->Html->url(['controller' => 'goals', 'action' => 'ajax_get_collabo_change_modal', $goal['Goal']['id']]) ?>">
                            <i class="fa fa-child font_rougeOrange font_18px" style="<?= $collabo_style ?>"></i>
                            <span class="ml_5px font_14px"><?= $collabo_text ?></span>
                        </a>
                    </div>
                <? endif; ?>
            </div>
            <? if (isset($goal['Goal']) && !empty($goal['Goal'])): ?>
                <div class="col col-xxs-12 font_11px">
                    <i class="fa fa-folder"></i><span class="pl_2px"><?= h($goal['GoalCategory']['name']) ?></span>
                </div>
                <div class="col col-xxs-12">
                    <p class="font_18px font_verydark"><?= h($goal['Goal']['name']) ?></p>
                </div>
                <div class="col col-xxs-12 bd-b mb-pb_5px">
                    <?= h($goal['Purpose']['name']) ?>
                </div>
                <div class="col col-xxs-12 bd-b mb-pb_5px">
                    <i class="fa fa-bullseye"></i><span class="pl_2px"><?= __d('gl', '程度') ?></span>

                    <div><?= __d('gl', '単位: %s', KeyResult::$UNIT[$goal['Goal']['value_unit']]) ?></div>
                    <? if ($goal['Goal']['value_unit'] != KeyResult::UNIT_BINARY): ?>
                        <div><?= __d('gl', '達成時: %s', (double)$goal['Goal']['target_value']) ?></div>
                        <div><?= __d('gl', '開始時: %s', (double)$goal['Goal']['start_value']) ?></div>
                    <? endif; ?>
                </div>
                <div class="col col-xxs-12">
                    <!-- アクション、フォロワー -->
                </div>
                <div class="col col-xxs-12 bd-b mb-pb_5px">
                    <div><i class="fa fa-sun-o"></i><span class="pl_2px"><?= __d('gl', "リーダー") ?></span></div>
                    <? if (isset($goal['Leader'][0]['User'])): ?>
                        <img src="<?=
                        $this->Upload->uploadUrl($goal['Leader'][0]['User'],
                                                 'User.photo', ['style' => 'small']) ?>"
                             style="width:32px;height: 32px;">
                        <?= h($goal['Leader'][0]['User']['display_username']) ?>
                    <? endif; ?>
                </div>
                <div class="col col-xxs-12 bd-b mb-pb_5px">
                    <div><i class="fa fa-child"></i><span class="pl_2px"><?= __d('gl', "コラボレータ") ?>
                            &nbsp;(<?= count($goal['Collaborator']) ?>)</span></div>
                    <? if (isset($goal['Collaborator']) && !empty($goal['Collaborator'])): ?>
                        <? foreach ($goal['Collaborator'] as $collabo): ?>
                            <img src="<?=
                            $this->Upload->uploadUrl($collabo['User'],
                                                     'User.photo', ['style' => 'small']) ?>"
                                 style="width:32px;height: 32px;" alt="<?= h($collabo['User']['display_username']) ?>"
                                 title="<?= h($collabo['User']['display_username']) ?>">
                        <? endforeach ?>
                    <? else: ?>
                        <?= __d('gl', "なし") ?>
                    <? endif; ?>
                </div>
                <div class="col col-xxs-12 bd-b mb-pb_5px">
                    <div><i class="fa fa-heart"></i><span class="pl_2px"><?= __d('gl', "フォロワー") ?>
                            &nbsp;(<?= count($goal['Follower']) ?>)</span></div>
                    <? if (isset($goal['Follower']) && !empty($goal['Follower'])): ?>
                        <? foreach ($goal['Follower'] as $follower): ?>
                            <img src="<?=
                            $this->Upload->uploadUrl($follower['User'],
                                                     'User.photo', ['style' => 'small']) ?>"
                                 style="width:32px;height: 32px;" alt="<?= h($follower['User']['display_username']) ?>"
                                 title="<?= h($follower['User']['display_username']) ?>">
                        <? endforeach ?>
                    <? else: ?>
                        <?= __d('gl', "なし") ?>
                    <? endif; ?>
                </div>
                <div class="col col-xxs-12">
                    <div><i class="fa fa-ellipsis-h"></i><span class="pl_2px"><?= __d('gl', '詳細') ?></span></div>
                    <div>
                        <?= $this->TextEx->autoLink($goal['Goal']['description']) ?>
                    </div>
                </div>
            <? endif; ?>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?= __d('gl', "閉じる") ?></button>
        </div>
    </div>
</div>
<!-- END app/View/Elements/Goal/modal_goal_detail.ctp -->
