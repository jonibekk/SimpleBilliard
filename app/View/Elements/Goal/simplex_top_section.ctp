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
                               'class'         => 'goal-detail-avator lazy',
                               'data-original' => $this->Upload->uploadUrl($goal['Goal'], 'Goal.photo',
                                                                           ['style' => 'x_large']),
                           ]
        )
        ?>
    </div>

    <div class="goal-detail-numbers-wrap">
        <div class="goal-detail-numbers-action">
            <div class="goal-detail-numbers-action-counts">
                <?= h($action_count) ?>
            </div>
            <span class="goal-detail-numbers-category-action">
                <?= __d('gl', 'アクション') ?>
            </span>
        </div>
        <div class="goal-detail-numbers-member">
            <div class="goal-detail-numbers-member-counts">
                <?= h($member_count) ?>
            </div>
            <span class="goal-detail-numbers-category-member">
                <?= __d('gl', 'メンバー') ?>
            </span>
        </div>
        <div class="goal-detail-numbers-follower">
            <div class="goal-detail-numbers-follower-counts">
                <?= h($follower_count) ?>
            </div>
            <span class="goal-detail-numbers-category-follower">
                <?= __d('gl', 'フォロワー') ?>
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
    <p class="goal-detail-goal-name">
        <?= h($goal['Goal']['name']) ?>
    </p>

    <p class="goal-detail-goal-purpose">
        <?= h($goal['Purpose']['name']) ?>
    </p>

</div>
<div class="goal-detail-tab-group">
    <a class="goal-detail-info-tab <?= $this->request->params['action'] == 'view_info' ? "profile-user-tab-active" : null ?>"
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
    <a class="goal-detail-kr-tab <?= $this->request->params['action'] == 'view_krs' ? "profile-user-tab-active" : null ?>"
       href="<?= $this->Html->url(
           [
               'controller' => 'goals',
               'action'     => 'view_krs',
               'goal_id'    => $goal['Goal']['id'],
           ]); ?>">
        <i class="fa fa-key goal-detail-tab-icon"></i>

        <p class="goal-detail-tab-title">
            <?= h(__d('gl', '成果')) ?>
        </p>
    </a>
    <a class="goal-detail-action-tab <?= $this->request->params['action'] == 'view_actions' ? "profile-user-tab-active" : null ?>"
       href="<?= $this->Html->url(
           [
               'controller' => 'goals',
               'action'     => 'view_actions',
               'goal_id'    => $goal['Goal']['id'],
               'page_type'  => 'image'
           ]); ?>">
        <i class="fa fa-check goal-detail-tab-icon"></i>

        <p class="goal-detail-tab-title">
            <?= h(__d('gl', 'アクション')) ?>
        </p>
    </a>
    <a class="goal-detail-member-tab <?= $this->request->params['action'] == 'view_members' ? "profile-user-tab-active" : null ?>"
       href="<?= $this->Html->url(
           [
               'controller' => 'goals',
               'action'     => 'view_members',
               'goal_id'    => $goal['Goal']['id'],
           ]); ?>">
        <i class="fa fa-users goal-detail-tab-icon"></i>

        <p class="goal-detail-tab-title">
            <?= h(__d('gl', 'メンバー')) ?>
        </p>
    </a>
    <a class="goal-detail-member-tab <?= $this->request->params['action'] == 'view_followers' ? "profile-user-tab-active" : null ?>"
       href="<?= $this->Html->url(
           [
               'controller' => 'goals',
               'action'     => 'view_followers',
               'goal_id'    => $goal['Goal']['id'],
           ]); ?>">
        <i class="fa fa-heart goal-detail-tab-icon"></i>

        <p class="goal-detail-tab-title">
            <?= h(__d('gl', 'フォロワー')) ?>
        </p>
    </a>
</div>
<!-- END app/View/Elements/simplex_top_section.ctp -->
