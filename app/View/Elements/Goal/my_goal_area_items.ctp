<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 9/26/14
 * Time: 11:14 AM
 *
 * @var CodeCompletionView $this
 * @var                    $goals
 * @var                    $type
 */
?>
<!-- START app/View/Elements/Goal/my_goal_area_items.ctp -->
<? foreach ($goals as $goal): ?>
    <div class="col col-xxs-12 my-goals-column-item bd-radius_4px shadow-default mt_8px">
        <div class="col col-xxs-12">
            <? if ($type == 'leader'): ?>
                <div class="pull-right goals-column-function bd-radius_4px dropdown">
                    <a href="#" class="font_lightGray-gray font_14px plr_4px pt_1px pb_2px"
                       data-toggle="dropdown"
                       id="download">
                        <i class="fa fa-cog"></i><i class="fa fa-caret-down goals-column-fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-right frame-arrow-icon" role="menu"
                        aria-labelledby="dropdownMenu1">
                        <?
                        //目的のみの場合とそうでない場合でurlが違う
                        $edit_url = ['controller' => 'goals', 'action' => 'add', 'mode' => 2, 'purpose_id' => $goal['Purpose']['id']];
                        $del_url = ['controller' => 'goals', 'action' => 'delete_purpose', $goal['Purpose']['id']];
                        if (isset($goal['Goal']['id']) && !empty($goal['Goal']['id'])) {
                            $edit_url = ['controller' => 'goals', 'action' => 'add', $goal['Goal']['id'], 'mode' => 3];
                            $del_url = ['controller' => 'goals', 'action' => 'delete', $goal['Goal']['id']];
                        }
                        ?>
                        <? if (!empty($goal['Goal'])): ?>
                            <li role="presentation">
                                <a href="<?= $this->Html->url(['controller' => 'goals', 'action' => 'ajax_get_add_key_result_modal', $goal['Goal']['id']]) ?>"
                                   class="modal-ajax-get-add-key-result">
                                    <i class="fa fa-plus-circle"></i><span class="ml_2px">
                                            <?= __d('gl', "出したい成果を追加") ?></span>
                                </a>
                            </li>
                        <? endif; ?>
                        <? if (!viaIsSet($goal['Evaluation'])): ?>
                            <li role="presentation"><a role="menuitem" tabindex="-1"
                                                       href="<?= $this->Html->url($edit_url) ?>">
                                    <i class="fa fa-pencil"></i><span class="ml_2px"><?= __d('gl', "ゴールを編集") ?></span>
                                </a>
                            </li>
                            <li role="presentation">
                                <?=
                                $this->Form->postLink('<i class="fa fa-trash"></i><span class="ml_5px">' .
                                                      __d('gl', "ゴールを削除") . '</span>',
                                                      $del_url,
                                                      ['escape' => false], __d('gl', "本当にこのゴールを削除しますか？")) ?>
                            </li>
                        <? endif; ?>
                    </ul>
                </div>
            <? elseif
            ($type == 'collabo'
            ): ?>
                <div class="pull-right goals-column-function bd-radius_4px dropdown">
                    <a href="#" class="font_lightGray-gray font_14px plr_4px pt_1px pb_2px"
                       data-toggle="dropdown"
                       id="download">
                        <i class="fa fa-cog"></i><i class="fa fa-caret-down goals-column-fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-right frame-arrow-icon" role="menu"
                        aria-labelledby="dropdownMenu1">
                        <? if (isset($goal['Goal']['id']) && !empty($goal['Goal']['id'])): ?>
                            <li role="presentation">
                                <a href="<?= $this->Html->url(['controller' => 'goals', 'action' => 'ajax_get_add_key_result_modal', $goal['Goal']['id']]) ?>"
                                   class="modal-ajax-get-add-key-result"
                                    ><i class="fa fa-plus-circle"></i><span class="ml_2px">
                                    <?= __d('gl', "出したい成果を追加") ?></span></a>
                                <a class="modal-ajax-get-collabo"
                                   data-toggle="modal"
                                   data-target="#ModalCollabo_<?= $goal['Goal']['id'] ?>"
                                   href="<?= $this->Html->url(['controller' => 'goals', 'action' => 'ajax_get_collabo_change_modal', $goal['Goal']['id']]) ?>">
                                    <i class="fa fa-pencil"></i>
                                    <span class="ml_2px"><?= __d('gl', "コラボを編集") ?></span>
                                </a>
                            </li>
                        <? endif; ?>
                    </ul>
                </div>
            <? endif; ?>

            <? if (empty($goal['Goal'])): ?>
                <div class="col col-xxs-10 goals-column-add-box">
                    <a href="<?= $this->Html->url(['controller' => 'goals', 'action' => 'add', 'purpose_id' => $goal['Purpose']['id'], 'mode' => 2]) ?>"
                       class="font_rougeOrange">
                        <div class="goals-column-add-icon"><i class="fa fa-plus-circle"></i></div>
                        <div class="goals-column-add-text font_12px"><?= __d('gl', '基準を追加する') ?></div>
                    </a>
                </div>
            <? else: ?>
                <div class="ln_contain w_88per">
                    <a href="<?= $this->Html->url(['controller' => 'goals', 'action' => 'ajax_get_goal_detail_modal', $goal['Goal']['id']]) ?>"
                       class="modal-ajax-get">
                        <p class="ln_trigger-f5 font_gray">
                            <i class="fa fa-flag"></i>
                            <?= h($goal['Goal']['name']) ?></p>
                    </a>
                </div>
            <? endif; ?>
        </div>
        <div class="col col-xxs-12 font_12px ln_1 goals-column-purpose">
            <?= h($goal['Purpose']['name']) ?>
        </div>
        <? if (isset($goal['Goal']['id'])): ?>
            <div class="col col-xxs-12">
                <div class="progress mb_0px goals-column-progress-bar">
                    <div class="progress-bar progress-bar-info" role="progressbar"
                         aria-valuenow="<?= h($goal['Goal']['progress']) ?>" aria-valuemin="0"
                         aria-valuemax="100" style="width: <?= h($goal['Goal']['progress']) ?>%;">
                        <span class="ml_12px"><?= h($goal['Goal']['progress']) ?>%</span>
                    </div>
                </div>
            </div>
            <? if ($type != 'follow'): ?>
                <div class="col col-xxs-12 goalsCard-actionResult" id="AddActionFormWrapper_<?= $goal['Goal']['id'] ?>">
                    <form action="#" id="" method="post" accept-charset="utf-8">
                        <div class="form-group mb_5px develop--font_normal">
                            <textarea
                                class="form-control col-xxs-10 goalsCard-actionInput mb_12px add-select-options not-autosize click-get-ajax-form-replace"
                                rows="1" placeholder="<?= __d('gl', "今日やったアクションを共有しよう！") ?>"
                                cols="30" init-height="20"
                                tmp-target-height="53"
                                replace-elm-parent-id="AddActionFormWrapper_<?= $goal['Goal']['id'] ?>"
                                click-target-id="ActionFormName_<?= $goal['Goal']['id'] ?>"
                                ajax-url="<?= $this->Html->url(['controller' => 'goals', 'action' => 'ajax_get_new_action_form', $goal['Goal']['id'], 'ar_count' => $goal['Goal']['action_result_count']]) ?>"
                                ></textarea>
                        </div>
                        <? if ($goal['Goal']['action_result_count'] > 0): ?>
                            <a class="goalsCard-activity inline-block col-xxs-2 click-show-post-modal font_gray-brownRed pointer"
                               id="ActionListOpen_<?= $goal['Goal']['id'] ?>"
                               href="<?= $this->Html->url(['controller' => 'posts', 'action' => 'ajax_get_goal_action_feed', 'goal_id' => $goal['Goal']['id'], 'type' => Post::TYPE_ACTION]) ?>">
                                <i class="fa fa-check-circle mr_1px font_brownRed"></i><span
                                    class="ls_number"><?= $goal['Goal']['action_result_count'] ?></span>
                            </a>
                        <? else: ?>
                            <div class="goalsCard-activity0 inline-block col-xxs-2">
                                <i class="fa fa-check-circle mr_1px"></i><span
                                    class="ls_number">0</span>
                            </div>
                        <? endif; ?>
                    </form>
                </div>
            <? endif; ?>
            <div class="col col-xxs-12 goalsCard-krSeek">
                <? if (isset($goal['Goal']['end_date']) && !empty($goal['Goal']['end_date'])): ?>
                    <div class="pull-right font_12px">
                        <? if (($limit_day = ($goal['Goal']['end_date'] - REQUEST_TIMESTAMP) / (60 * 60 * 24)) < 0): ?>
                            <?= __d('gl', "%d日経過", $limit_day * -1) ?>
                        <? else: ?>
                            <?= __d('gl', "残り%d日", $limit_day) ?>
                        <? endif; ?>
                    </div>
                <? endif; ?>
                <?
                $url = ['controller' => 'goals', 'action' => 'ajax_get_key_results', $goal['Goal']['id'], true];
                if ($type == "follow") {
                    $url = ['controller' => 'goals', 'action' => 'ajax_get_key_results', $goal['Goal']['id']];
                }
                ?>
                <a href="#"
                   class="link-dark-gray toggle-ajax-get pull-left btn-white bd-radius_14px p_4px font_12px lh_18px"
                   target-id="KeyResults_<?= $goal['Goal']['id'] ?>"
                   ajax-url="<?= $this->Html->url($url) ?>"
                   id="KRsOpen_<?= $goal['Goal']['id'] ?>"
                    >
                    <i class="fa fa-caret-down feed-arrow lh_18px"></i>
                    <?= __d('gl', "出したい成果をみる") ?>(<?= count($goal['KeyResult']) ?>)
                </a>
            </div>
            <div class="con col-xxs-12 none" id="KeyResults_<?= $goal['Goal']['id'] ?>"></div>
        <? endif; ?>
    </div>
<? endforeach ?>
<!-- End app/View/Elements/Goal/my_goal_area_items.ctp -->
