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
<!-- START app/View/Elements/Goals/my_goal_area_items.ctp -->
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
                        <? if (isset($goal['SpecialKeyResult'][0]['id']) && !empty($goal['SpecialKeyResult'][0]['id'])): ?>
                            <li role="presentation">
                                <a href="<?= $this->Html->url(['controller' => 'goals', 'action' => 'ajax_get_add_key_result_modal', $goal['SpecialKeyResult'][0]['id']]) ?>"
                                   class="modal-ajax-get-add-key-result"
                                    >
                                    <?= __d('gl', "主な成果を追加") ?></a>
                            </li>
                        <? endif; ?>
                        <li role="presentation"><a role="menuitem" tabindex="-1"
                                                   href="<?= $this->Html->url(['controller' => 'goals', 'action' => 'add', $goal['Goal']['id'], 'mode' => 3]) ?>"><?=
                                __d('gl',
                                    "編集") ?></a>
                        </li>
                        <li role="presentation">
                            <?=
                            $this->Form->postLink(__d('gl', "削除"),
                                                  ['controller' => 'goals', 'action' => 'delete', $goal['Goal']['id']],
                                                  null, __d('gl', "本当にこのゴールを削除しますか？")) ?>
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
                        <? if (isset($goal['SpecialKeyResult'][0]['id']) && !empty($goal['SpecialKeyResult'][0]['id'])): ?>
                            <li role="presentation">
                                <a href="<?= $this->Html->url(['controller' => 'goals', 'action' => 'ajax_get_add_key_result_modal', $goal['SpecialKeyResult'][0]['id']]) ?>"
                                   class="modal-ajax-get-add-key-result"
                                    >
                                    <?= __d('gl', "主な成果を追加") ?></a>
                            </li>
                        <? endif; ?>
                    </ul>
                </div>
            <? endif; ?>

            <? if (empty($goal['SpecialKeyResult'])): ?>
                <div class="col col-xxs-10 goals-column-add-box">
                    <a href="<?= $this->Html->url(['controller' => 'goals', 'action' => 'add', $goal['Goal']['id'], 'mode' => 2]) ?>"
                       class="font_rougeOrange">
                        <div class="goals-column-add-icon"><i class="fa fa-plus-circle"></i></div>
                        <div class="goals-column-add-text font_12px"><?= __d('gl', '基準を追加する') ?></div>
                    </a>
                </div>
            <? else: ?>
                <b class="line-numbers ln_2">
                    <i class="fa fa-flag"></i>
                    <?= h($goal['SpecialKeyResult'][0]['name']) ?></b>
            <?endif; ?>
        </div>
        <div class="col col-xxs-12 font_12px line-numbers ln_1 goals-column-purpose">
            <?= h($goal['Goal']['purpose']) ?>
        </div>
        <div class="col col-xxs-12">
            <div class="progress gl-progress goals-column-progress-bar">
                <div class="progress-bar progress-bar-info" role="progressbar"
                     aria-valuenow="<?= h($goal['Goal']['progress']) ?>" aria-valuemin="0"
                     aria-valuemax="100" style="width: <?= h($goal['Goal']['progress']) ?>%;">
                    <span class="ml_12px"><?= h($goal['Goal']['progress']) ?>%</span>
                </div>
            </div>
        </div>
        <div class="col col-xxs-12">
            <? if (isset($goal['SpecialKeyResult'][0]['end_date']) && !empty($goal['SpecialKeyResult'][0]['end_date'])): ?>
                <div class="pull-left font_12px">
                    <? if (($limit_day = ($goal['SpecialKeyResult'][0]['end_date'] - time()) / (60 * 60 * 24)) < 0): ?>
                        <?= __d('gl', "%d日経過", $limit_day * -1) ?>
                    <? else: ?>
                        <?= __d('gl', "残り%d日", $limit_day) ?>
                    <?endif; ?>
                </div>
            <? endif; ?>
            <div class="pull-right font_12px check-status">
                <? if (isset($goal['SpecialKeyResult'][0]['valued_flg']) && $goal['SpecialKeyResult'][0]['valued_flg']): ?>
                    <i class="fa fa-check-circle icon-green"></i><?= __d('gl', "認定") ?>
                <? else: ?>
                    <i class="fa fa-check-circle"></i><?= __d('gl', "未認定") ?>
                <?endif; ?>
            </div>
        </div>
    </div>
<? endforeach ?>
<!-- End app/View/Elements/Goals/my_goal_area_items.ctp -->