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
<div class="panel-body goal-detail-upper-panel">
    <div class="goal-detail-avator-wrap">
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
        <p class="goal-detail-goal-name">
            <?= h($goal['Goal']['name']) ?>
        </p>
        <p class="goal-detail-goal-purpose">
            <?= h($goal['Purpose']['name']) ?>
        </p>
    </div>

    <div class="goal-detail-numbers-wrap">
        <div class="goal-detail-numbers-action">
            <div class="goal-detail-numbers-action-counts">
                <?= __d('gl', 'アクション') ?>
            </div>
            <span class="goal-detail-numbers-category-action">
                 <?= h($action_count) ?>
            </span>
        </div>
        <div class="goal-detail-numbers-member">
            <div class="goal-detail-numbers-member-counts">
                <?= __d('gl', 'メンバー') ?>
            </div>
            <span class="goal-detail-numbers-category-member">
                 <?= h($member_count) ?>
            </span>
        </div>
        <div class="goal-detail-numbers-follower">
            <div class="goal-detail-numbers-follower-counts">
                <?= __d('gl', 'フォロワー') ?>
            </div>
            <span class="goal-detail-numbers-category-follower">
                 <?= h($follower_count) ?>
            </span>
        </div>
        <?php if ($is_leader): ?>
            <?= $this->Html->link(__d('gl', 'ゴール編集'),
                                  [
                                      'controller' => 'goals',
                                      'action'     => 'add',
                                      'goal_id'    => $goal['Goal']['id'],
                                      'mode'       => 3,
                                  ],
                                  [
                                      'class' => 'btn-profile-edit'
                                  ])
            ?>
        <?php endif ?>
    </div>
    <div class="goal-detail-tab-group">
        <a class="goal-detail-info-tab"
                href="<?= $this->Html->url(
                    [
                        'controller' => 'goals',
                        'action'     => 'view_info',
                        'goal_id'    => $goal['Goal']['id'],
                    ]); ?>">
                <i class="fa fa-flag goal-detail-tab-icon"></i>
                <p class="goal-detail-tab-title">
                    <?= h(__d('gl', '基本情報')) ?>
                </p>
        </a>
        <a class="goal-detail-kr-tab"
            href="<?= $this->Html->url(
                [
                    'controller' => 'goals',
                    'action'     => 'view_krs',
                    'goal_id'    => $goal['Goal']['id'],
                ]); ?>">
            <i class="fa fa-flag goal-detail-tab-icon"></i>
            <p class="goal-detail-tab-title">
                <?= h(__d('gl', '成果')) ?>
            </p>
        </a>
        <a class="goal-detail-action-tab"
            href="<?= $this->Html->url(
                [
                    'controller' => 'goals',
                    'action'     => 'view_actions',
                    'goal_id'    => $goal['Goal']['id'],
                    'page_type'  => 'image'
                ]); ?>">
            <i class="fa fa-flag goal-detail-tab-icon"></i>
            <p class="goal-detail-tab-title">
                <?= h(__d('gl', 'アクション')) ?>
            </p>
        </a>
        <a class="goal-detail-member-tab"
            href="<?= $this->Html->url(
                [
                    'controller' => 'goals',
                    'action'     => 'view_members',
                    'goal_id'    => $goal['Goal']['id'],
                ]); ?>">
            <i class="fa fa-flag goal-detail-tab-icon"></i>
            <p class="goal-detail-tab-title">
                <?= h(__d('gl', 'メンバー')) ?>
            </p>
        </a>
        <a class="goal-detail-member-tab"
            href="<?= $this->Html->url(
                [
                    'controller' => 'goals',
                    'action'     => 'view_followers',
                    'goal_id'    => $goal['Goal']['id'],
                ]); ?>">
            <i class="fa fa-flag goal-detail-tab-icon"></i>
            <p class="goal-detail-tab-title">
                <?= h(__d('gl', 'フォロワー')) ?>
            </p>
        </a>
    </div>
</div>
<!-- END app/View/Elements/simplex_top_section.ctp -->
