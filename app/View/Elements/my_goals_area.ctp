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
        <?= __d('gl', 'あなたのゴール') ?>(<?= $my_goals_count + $collabo_goals_count ?>)
    </span>
    <a href="<?= $this->Html->url(['controller' => 'goals', 'action' => 'add']) ?>"
       class="font_gargoyleGray-brownRed pull-right col-xxs-5 btn-goals-column-plus">
        <i class="fa fa-plus-circle font_brownRed">
        </i>
        <?= __d('gl', 'ゴールを作成') ?>
    </a>

</div>
<div id="LeaderGoals">
    <div class="col col-xxs-12 mt_16px font_gargoyleGray">
        <i class="fa fa-sun-o"></i><?= __d('gl', 'リーダー') ?>(<?= $my_goals_count ?>)
    </div>

    <?php if (empty($my_goals)): ?>
        <div class="col col-xxs-12 goals-column-empty-box">
            <a href="<?= $this->Html->url(['controller' => 'goals', 'action' => 'add']) ?>"
               class="font_lightGray-gray">
                <div class="goals-column-empty-icon"><i class="fa fa-plus-circle font_33px"></i></div>
                <div class="goals-column-empty-text font_14px"><?= __d('gl', '新しいゴールをつくる') ?></div>
            </a>
        </div>
    <?php else: ?>
        <?= $this->element('Goal/my_goal_area_items', ['goals' => $my_goals, 'type' => 'leader']) ?>
    <?php endif ?>
</div>
<?php if (count($my_goals) < $my_goals_count): ?>
    <a href="#" class="click-my-goals-read-more btn btn-link" next-page-num="2"
       get-url="<?= $this->Html->url(['controller' => 'goals', 'action' => 'ajax_get_my_goals']) ?>"
       goal-type="leader">
        <i class="fa fa-angle-double-down"><?= __d('gl', "もっと見る") ?></i>
    </a>
<?php endif; ?>
<div id="CollaboGoals">
    <div class="col col-xxs-12 mt_16px font_gargoyleGray">
        <i class="fa fa-child"></i><?= __d('gl', 'コラボレータ') ?>(<?= $collabo_goals_count ?>)
    </div>
    <?= $this->element('Goal/my_goal_area_items', ['goals' => $collabo_goals, 'type' => 'collabo']) ?>
</div>
<?php if (count($collabo_goals) < $collabo_goals_count): ?>
    <a href="#" class="click-collabo-goals-read-more btn btn-link" next-page-num="2"
       get-url="<?= $this->Html->url(['controller' => 'goals', 'action' => 'ajax_get_my_goals']) ?>"
       goal-type="collabo">
        <i class="fa fa-angle-double-down"><?= __d('gl', "もっと見る") ?></i>
    </a>
<?php endif; ?>
<div id="FollowGoals">
    <div class="col col-xxs-12 goals-column-head mt_32px">
        <span class="font_18px font_gargoyleGray goals-column-title"><?= __d('gl', 'フォロー中のゴール') ?>
            (<?= $follow_goals_count ?>)</span>

        <div class="pull-right">
            <a href="#" class="font_gargoyleGray-gray font_11px">
                <span class="lh_20px"><?= __d('gl', "ゴールを探す") ?></span>
            </a>
        </div>
    </div>
    <?= $this->element('Goal/my_goal_area_items', ['goals' => $follow_goals, 'type' => 'follow']) ?>
</div>
<?php if (count($follow_goals) < $follow_goals_count): ?>
    <a href="#" class="click-follow-goals-read-more btn btn-link" next-page-num="2"
       get-url="<?= $this->Html->url(['controller' => 'goals', 'action' => 'ajax_get_my_goals']) ?>"
       goal-type="follow">
        <i class="fa fa-angle-double-down"><?= __d('gl', "もっと見る") ?></i>
    </a>
<?php endif; ?>
<div id="PrevGoals">
    <div class="col col-xxs-12 goals-column-head mt_32px">
        <span class="font_18px font_gargoyleGray goals-column-title">
            <?= __d('gl', '前期の未評価のあなたのゴール') ?>
            (<?= $my_previous_goals_count ?>)
        </span>

    </div>
    <?= $this->element('Goal/my_goal_area_items', ['goals' => $my_previous_goals, 'type' => 'my_prev']) ?>
</div>
<?php if (count($my_previous_goals) < $my_previous_goals_count): ?>
    <a href="#" class="click-collabo-goals-read-more btn btn-link" next-page-num="2"
       get-url="<?= $this->Html->url(['controller' => 'goals', 'action' => 'ajax_get_my_goals']) ?>"
       goal-type="my_prev">
        <i class="fa fa-angle-double-down"><?= __d('gl', "もっと見る") ?></i>
    </a>
<?php endif; ?>
<!-- END app/View/Elements/my_goals_area.ctp -->
