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
 * @var                    $follow_goals
 */
?>
<!-- START app/View/Elements/my_goals_area.ctp -->
<div class="col col-xxs-12 goals-column-head">
    <span class="font_18px mt_5px font_gargoyleGray goals-column-title">
        <?= __d('gl', 'あなたのゴール') ?>(<?= count($my_goals) + count($collabo_goals) ?>)
    </span>
        <a href="<?= $this->Html->url(['controller' => 'goals', 'action' => 'add']) ?>"
           class="font_gargoyleGray-brownRed pull-right col-xxs-4 btn-goals-column-plus">
            <i class="fa fa-plus-circle font_brownRed">
            </i>
            <?= __d('gl', 'ゴールを作成') ?>
        </a>

</div>
<div id="LeaderGoals">
    <div class="col col-xxs-12 mt_16px font_gargoyleGray">
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
    <? endif ?>
</div>
<div id="CollaboGoals">
    <div class="col col-xxs-12 mt_16px font_gargoyleGray">
        <i class="fa fa-child"><?= __d('gl', 'コラボレータ') ?>(<?= count($collabo_goals) ?>)</i>
    </div>
    <?= $this->element('Goal/my_goal_area_items', ['goals' => $collabo_goals, 'type' => 'collabo']) ?>
</div>
<div id="FollowGoals">
    <div class="col col-xxs-12 goals-column-head mt_32px">
        <span class="font_18px font_gargoyleGray goals-column-title"><?= __d('gl', 'フォロー中のゴール') ?>
            (<?= count($follow_goals) ?>)</span>

        <div class="pull-right">
            <a href="#" class="font_gargoyleGray-gray font_11px">
                <span class="lh_20px"><?= __d('gl', "ゴールを探す") ?></span>
            </a>
        </div>
    </div>
    <?= $this->element('Goal/my_goal_area_items', ['goals' => $follow_goals, 'type' => 'follow']) ?>
</div>

<!-- END app/View/Elements/my_goals_area.ctp -->