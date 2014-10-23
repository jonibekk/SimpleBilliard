<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 9/26/14
 * Time: 11:14 AM
 *
 * @var CodeCompletionView $this
 * @var                    $goals
 */
?>
<!-- START app/View/Elements/Goal/index_items.ctp -->
<? foreach ($goals as $goal): ?>
    <div class="col col-xxs-12 my-goals-item">
        <div class="col col-xxs-3 col-xs-2">
            <a href="<?= $this->Html->url(['controller' => 'goals', 'action' => 'ajax_get_goal_detail_modal', $goal['Goal']['id']]) ?>"
               class="modal-ajax-get">
                <?=
                $this->Html->image('ajax-loader.gif',
                                   [
                                       'class'         => 'lazy img-rounded',
                                       'style'         => 'width: 48px; height: 48px;',
                                       'data-original' => $this->Upload->uploadUrl($goal, 'Goal.photo',
                                                                                   ['style' => 'medium'])
                                   ]
                )
                ?></a>
        </div>
        <div class="col col-xxs-9 col-xs-10 pl_5px">
            <div class="col col-xxs-12">
                <a href="<?= $this->Html->url(['controller' => 'goals', 'action' => 'ajax_get_goal_detail_modal', $goal['Goal']['id']]) ?>"
                   class="modal-ajax-get"><b
                        class="line-numbers ln_2 font_verydark"><?= h($goal['Goal']['name']) ?></b></a>
            </div>
            <div class="col col-xxs-12 font_lightgray font_12px">
                <? if (!empty($goal['Leader'])): ?>
                    <?=
                    __d('gl', "リーダー: %s",
                        h($goal['Leader'][0]['User']['display_username'])) ?>
                <? endif; ?>
                | <?= __d('gl', "コラボ: ") ?>
                <? if (count($goal['Collaborator']) == 0): ?>
                    <?= __d('gl', "0人") ?>
                <? else: ?>
                    <? foreach ($goal['Collaborator'] as $key => $collaborator): ?>
                        <?= h($collaborator['User']['display_username']) ?>
                        <? if (isset($goal['Collaborator'][$key + 1])) {
                            echo ", ";
                        } ?>
                        <? if ($key == 1) {
                            break;
                        } ?>
                    <? endforeach ?>
                    <? if (($other_count = count($goal['Collaborator']) - 2) > 0): ?>
                        <?= __d('gl', "他%s人", $other_count) ?>
                    <? endif; ?>
                <?endif; ?>
            </div>
            <? if ($goal['Goal']['user_id'] != $this->Session->read('Auth.User.id') && isset($goal['Goal'])): ?>
                <div class="col col-xxs-12 mt_5px">
                    <? if (empty($goal['MyFollow'])) {
                        $follow_class = 'follow-off';
                        $follow_style = null;
                        $follow_text = __d('gl', "フォロー");
                    }
                    else {
                        $follow_class = 'follow-on';
                        $follow_style = 'display:none;';
                        $follow_text = __d('gl', "フォロー中");
                    }?>
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
                        $follow_disabled = null;
                    }?>
                    <div class="col col-xxs-6 col-xs-4 mr_5px">
                        <a class="btn btn_pureWhite font_verydark-white bd-circle_20 toggle-follow sm-btn <?= $follow_class ?>"
                           href="#"
                           data-class="toggle-follow"
                           goal-id="<?= $goal['Goal']['id'] ?>"
                            <?= $follow_disabled ?>
                            >
                            <i class="fa fa-heart font_rougeOrange" style="<?= $follow_style ?>"></i>
                            <span class="ml_5px"><?= $follow_text ?></span>
                        </a>
                    </div>
                    <div class="col col-xxs-5 col-xs-4">
                        <a class="btn btn_pureWhite bd-circle_20 font_verydark-white modal-ajax-get-collabo sm-btn <?= $collabo_class ?>"
                           data-toggle="modal"
                           data-target="#ModalCollabo_<?= $goal['Goal']['id'] ?>"
                           href="<?= $this->Html->url(['controller' => 'goals', 'action' => 'ajax_get_collabo_change_modal', $goal['Goal']['id']]) ?>">
                            <i class="fa fa-child font_rougeOrange font_18px" style="<?= $collabo_style ?>"></i>
                            <span class="ml_5px font_14px"><?= $collabo_text ?></span>
                        </a>
                    </div>
                </div>
            <? endif; ?>
        </div>
    </div>
<? endforeach ?>
<!-- End app/View/Elements/Goal/index_items.ctp -->