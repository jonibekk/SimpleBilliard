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
                        <i class="fa fa-cog"><i class="fa fa-caret-down goals-column-fa-caret-down"></i></i>
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
                                    <i class="fa fa-plus-circle"><span class="ml_2px">
                                            <?= __d('gl', "出したい成果を追加") ?></span></i>
                                </a>
                            </li>
                        <? endif; ?>
                        <li role="presentation"><a role="menuitem" tabindex="-1"
                                                   href="<?= $this->Html->url($edit_url) ?>">
                                <i class="fa fa-pencil"><span class="ml_2px"><?= __d('gl', "ゴールを編集") ?></span>
                                </i>
                            </a>
                        </li>
                        <li role="presentation">
                            <?=
                            $this->Form->postLink('<i class="fa fa-trash"><span class="ml_5px">' .
                                                  __d('gl', "ゴールを削除") . '</span></i>',
                                                  $del_url,
                                                  ['escape' => false], __d('gl', "本当にこのゴールを削除しますか？")) ?>
                        </li>
                    </ul>
                </div>
            <? elseif ($type == 'collabo'): ?>
                <div class="pull-right goals-column-function bd-radius_4px dropdown">
                    <a href="#" class="font_lightGray-gray font_14px plr_4px pt_1px pb_2px"
                       data-toggle="dropdown"
                       id="download">
                        <i class="fa fa-cog"><i class="fa fa-caret-down goals-column-fa-caret-down"></i></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-right frame-arrow-icon" role="menu"
                        aria-labelledby="dropdownMenu1">
                        <? if (isset($goal['Goal']['id']) && !empty($goal['Goal']['id'])): ?>
                            <li role="presentation">
                                <a href="<?= $this->Html->url(['controller' => 'goals', 'action' => 'ajax_get_add_key_result_modal', $goal['Goal']['id']]) ?>"
                                   class="modal-ajax-get-add-key-result"
                                    ><i class="fa fa-plus-circle"><span class="ml_2px">
                                    <?= __d('gl', "出したい成果を追加") ?></span></i></a>

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
                <div class="col col-xxs-12 goalsCard-actionResult">
                    <?= $this->Form->create('ActionResult', [
                        'inputDefaults' => [
                            'div'       => 'form-group mb_5px develop--font_normal',
                            'wrapInput' => false,
                            'class'     => 'form-control',
                        ],
                        'url'           => ['controller' => 'goals', 'action' => 'add_completed_action', $goal['Goal']['id']],
                        'type'          => 'file',
                    ]); ?>
                    <?=
                    $this->Form->input('Action.name', [
                                                        'label'          => false,
                                                        'rows'           => 1,
                                                        'placeholder'    => __d('gl', "アクションをいれる"),
                                                        'class'          => 'form-control tiny-form-text blank-disable col-xxs-10 goalsCard-actionInput mb_12px add-select-options',
                                                        'id'             => "ActionFormName_" . $goal['Goal']['id'],
                                                        'target_show_id' => "ActionFormDetail_" . $goal['Goal']['id'],
                                                        'target-id'      => "ActionFormSubmit_" . $goal['Goal']['id'],
                                                        'select-id'      => "ActionKeyResultId_" . $goal['Goal']['id'],
                                                        'add-select-url' => $this->Html->url(['controller' => 'goals', 'action' => 'ajax_get_kr_list', $goal['Goal']['id']])
                                                    ]
                    )
                    ?>
                    <div class="goalsCard-activity inline-block col-xxs-2">
                        <i class="fa fa-check-circle mr_1px"></i><span
                            class="ls_number"><?= $goal['Goal']['action_count'] ?></span>
                    </div>
                    <div class="none" id="ActionFormDetail_<?= $goal['Goal']['id'] ?>">
                        <div class="form-group">
                            <label class="font_normal col-xxs-4 lh_40px" for="ActionPhotos">
                                <i class="fa fa-camera mr_2px"></i><?= __d('gl', "画像") ?>
                            </label>

                            <div class="col-xxs-8">
                                <ul class="col input-images post-images">
                                    <? for ($i = 1; $i <= 5; $i++): ?>
                                        <li>
                                            <?= $this->element('Feed/photo_upload_mini',
                                                               ['type' => 'action_result', 'index' => $i, 'submit_id' => 'PostSubmit', 'has_many' => true]) ?>
                                        </li>
                                    <? endfor ?>
                                </ul>
                            </div>
                        </div>
                        <label class="font_normal col-xxs-4 lh_40px" for="KeyResults_<?= $goal['Goal']['id'] ?>">
                            <i class="fa fa-key mr_2px"></i><?= __d('gl', "成果") ?>
                        </label>
                        <?=
                        $this->Form->input('Action.key_result_id', [
                                                                     'label'   => false, //__d('gl', "紐付ける出したい成果を選択(オプション)"),
                                                                     'options' => [null => __d('gl', "選択なし")],
                                                                     'class'   => 'form-control col-xxs-8 selectKrForAction',
                                                                     'id'      => 'ActionKeyResultId_' . $goal['Goal']['id'],
                                                                 ]
                        )
                        ?>
                        <div class="form-group col-xxs-12 mt_12px">
                            <a href="#" target-id="ActionFormName_<?= $goal['Goal']['id'] ?>"
                               class="btn btn-white tiny-form-text-close font_verydark"><?= __d('gl', "キャンセル") ?></a>
                            <?= $this->Form->submit(__d('gl', "アクション登録"), [
                                'div'      => false,
                                'id'       => "ActionFormSubmit_" . $goal['Goal']['id'],
                                'class'    => 'btn btn-info pull-right',
                                'disabled' => 'disabled',
                            ]); ?>
                        </div>
                    </div>
                    <?= $this->Form->end() ?>
                </div>
            <? endif; ?>
            <div class="col col-xxs-12 goalsCard-krSeek">
                <? if (isset($goal['Goal']['end_date']) && !empty($goal['Goal']['end_date'])): ?>
                    <div class="pull-right font_12px">
                        <? if (($limit_day = ($goal['Goal']['end_date'] - time()) / (60 * 60 * 24)) < 0): ?>
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
