<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 8/7/14
 * Time: 11:36 AM
 *
 * @var CodeCompletionView $this
 * @var                    $my_goals
 * @var                    $collabo_goals
 */
?>
<!-- START app/View/Elements/my_goals_area.ctp -->
    <div class="col col-xxs-12 goals-column-head">
            <i class="fa fa-flag font_18px mt_5px goals-column-title"><span class="pl_5px"><?= __d('gl', 'あなたのゴール') ?>
                (<?= count($my_goals) + count($collabo_goals) ?>)</span></i>

        <div class="pull-right">
            <div class="dropdown">
                <a href="#" class="font_lightGray-gray font_11px" data-toggle="dropdown" id="download">
                    <span class="line-height_20px"><?= __d('gl', "全て") ?></span><i
                        class="fa fa-caret-down gl-feed-arrow line-height_20px"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-right frame-arrow-icon" role="menu"
                    aria-labelledby="dropdownMenu1">
                    <li role="presentation"><a role="menuitem" tabindex="-1" href="#"><?=
                            __d('gl',
                                "完了しているゴール") ?></a>
                    </li>
                    <li role="presentation"><a role="menuitem" tabindex="-1" href="#"><?= __d('gl', "今期のゴール") ?></a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div id="LeaderGoals">
        <div class="col col-xxs-12 mt_16px">
            <i class="fa fa-sun-o"><?= __d('gl', 'リーダー') ?>(<?= count($my_goals) ?>)</i>
        </div>

        <? if (empty($my_goals)): ?>
            <div class="col col-xxs-12 goals-column-empty-box">
                <a href="<?= $this->Html->url(['controller' => 'goals', 'action' => 'add']) ?>"
                   class="font_lightGray-gray">
                    <div class="goals-column-empty-icon"><i class="fa fa-plus-circle font_33px"></i></div>
                    <div class="goals-column-empty-text font_14px"><?= __d('gl', '新しいゴールをつくる') ?></div>
                </a>
            </div>
        <? else: ?>
            <?= $this->element('Goal/my_goal_area_items', ['goals' => $my_goals, 'type' => 'leader']) ?>
            <div class="col col-xxs-12 goals-column-plus-box">
                <i class="fa fa-plus-circle">
                    <a href="<?= $this->Html->url(['controller' => 'goals', 'action' => 'add']) ?>"
                       class="font_gray-rougeOrange"><?= __d('gl', '新しいゴールをつくる') ?></a>
                </i>

            </div>
        <? endif ?>
    </div>
    <div id="CollaboGoals">
        <div class="col col-xxs-12 mt_16px">
            <i class="fa fa-child"><?= __d('gl', 'コラボレータ') ?>(<?= count($collabo_goals) ?>)</i>
        </div>
        <?= $this->element('Goal/my_goal_area_items', ['goals' => $collabo_goals, 'type' => 'collabo']) ?>
    </div>
    <div id="FollowGoals">
        <div class="col col-xxs-12 goals-column-head">
            <i class="fa fa-heart-o font_18px goals-column-title mt_32px"><span class="pl_5px"><?= __d('gl', 'フォロー中のゴール') ?>
                    (<?= count($my_goals) + count($collabo_goals) ?>)</span></i>
        </div>
    </div>
<!-- END app/View/Elements/my_goals_area.ctp -->