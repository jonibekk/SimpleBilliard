<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 7/9/15
 * Time: 3:33 PM
 *
 * @var $goal
 */
?>
<!-- START app/View/Goals/view_info.ctp -->
<div class="col-sm-8 col-sm-offset-2">
    <div class="panel panel-default">
        <?= $this->element('Goal/simplex_top_section') ?>
        <div class="panel-body goal-detail-info-panel">
            <i class="fa fa-flag"></i>
            <div class="goal-detail-info-data">
                <span class="font_bold"><?= h($goal['Goal']['name']) ?></span>
                <i class="fa fa-arrow-right goal-detail-weight-5-icon"></i>
                <p class="goal-detail-info-purpose"><?= __d('gl', '目的') ?>：  <?= $goal['Purpose']['name'] ?></p>
                <p class="goal-detail-info-category"><?= __d('gl', 'カテゴリー') ?>： <?= h($goal['GoalCategory']['name']) ?></p>
            </div>
            <i class="fa-bullseye fa"></i>
            <div class="goal-detail-info-progress">
                <?= h(round($goal['Goal']['target_value'], 1)) ?> [<?= h(KeyResult::$UNIT[$goal['Goal']['value_unit']]) ?>]            (← <?= h(round($goal['Goal']['start_value'], 1)) ?>)
            </div>
            <i class="fa-calendar fa"></i>
            <div class="goal-detail-info-date">
                <?= $this->Time->format('Y/m/d', $goal['Goal']['end_date']) ?>
            </div>
            <?= __d('gl', 'メンバー') ?><br>
            <?php
            $member_all = array_merge($goal['Leader'], $goal['Collaborator']);
            $member_view_num = 5;
            $over_num = count($member_all) - $member_view_num;
            ?>
            <?php foreach ($member_all as $member): ?>
                <?php
                if ($member_view_num-- == 0) {
                    break;
                }
                ?>
                <?=
                $this->Html->link($this->Upload->uploadImage($member['User'], 'User.photo', ['style' => 'small'],
                                                             ['class' => 'goal-detail-info-avator img-circle',
                                                              'style' => 'width:38px;']),
                                  ['controller' => 'users',
                                   'action'     => 'view_goals',
                                   'user_id'    => $member['User']['id']],
                                  ['escape' => false]
                )
                ?>
            <?php endforeach ?>
            <?php if ($over_num > 0): ?>
                (<?= $this->Html->link($over_num, [
                    'controller' => 'goals',
                    'action'     => 'view_members',
                    'goal_id'    => $goal['Goal']['id'],
                ]) ?>)
            <?php endif ?>

            <br>
            <?= __d('gl', '詳細') ?><br>
            <?= h($goal['Goal']['description']) ?>
        </div>
    </div>
</div>
<!-- END app/View/Goals/view_info.ctp -->
