<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 7/9/15
 * Time: 3:50 PM
 *
 * @var $goal
 * @var $action_count
 * @var $member_count
 * @var $follower_count
 * @var $is_leader
 */
?>
<!-- START app/View/Elements/simplex_top_section.ctp -->
<div class="panel-body">
    <div style="float:left">
        <?=
        $this->Html->image('ajax-loader.gif',
                           [
                               'class'         => 'lazy',
                               'data-original' => $this->Upload->uploadUrl($goal['Goal'], 'Goal.photo',
                                                                           ['style' => 'large']),
                           ]
        )
        ?>
        <br>
        <?= h($goal['Goal']['name']) ?><br>
        <?= h($goal['Purpose']['name']) ?>
    </div>

    <div>
        <?= __d('gl', 'アクション') ?>: <?= h($action_count) ?><br>
        <?= __d('gl', 'メンバー') ?>: <?= h($member_count) ?><br>
        <?= __d('gl', 'フォロワー') ?>: <?= h($follower_count) ?><br>
        <?php if ($is_leader): ?>
            <?= $this->Html->link(__d('gl', 'ゴール編集'),
                                  [
                                      'controller' => 'goals',
                                      'action'     => 'add',
                                      'goal_id'    => $goal['Goal']['id'],
                                      'mode'       => 3,
                                  ],
                                  [
                                      'class' => ''
                                  ])
            ?>
        <?php endif ?>
    </div>
    <div style="clear:both">
        <?= $this->Html->link(__d('gl', '基本情報'), [
            'controller' => 'goals',
            'action'     => 'view_info',
            'goal_id'    => $goal['Goal']['id'],
        ]); ?>
        |
        <?= $this->Html->link(__d('gl', '成果'), [
            'controller' => 'goals',
            'action'     => 'view_krs',
            'goal_id'    => $goal['Goal']['id'],
        ]); ?>
        |
        <?= $this->Html->link(__d('gl', 'アクション'), [
            'controller' => 'goals',
            'action'     => 'view_actions',
            'goal_id'    => $goal['Goal']['id'],
        ]); ?>
        |
        <?= $this->Html->link(__d('gl', 'メンバー'), [
            'controller' => 'goals',
            'action'     => 'view_members',
            'goal_id'    => $goal['Goal']['id'],
        ]); ?>
        |
        <?= $this->Html->link(__d('gl', 'フォロワー'), [
            'controller' => 'goals',
            'action'     => 'view_followers',
            'goal_id'    => $goal['Goal']['id'],
        ]); ?>
    </div>
</div>
<!-- END app/View/Elements/simplex_top_section.ctp -->
