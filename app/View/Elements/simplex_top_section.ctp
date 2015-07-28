<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 7/9/15
 * Time: 3:50 PM
 *
 * @var CodeCompletionView $this
 * @var                    $user
 * @var                    $post_count
 * @var                    $action_count
 * @var                    $like_count
 */
?>
<!-- START app/View/Elements/simplex_top_section.ctp -->
<div class="panel-body profile-user-upper-panel">
    <div class="profile-user-avator-wrap">
        <?=
        $this->Html->image('ajax-loader.gif',
                           [
                               'class'         => 'lazy profile-user-avator',
                               'data-original' => $this->Upload->uploadUrl($user['User'], 'User.photo',
                                                                           ['style' => 'large']),
                           ]
        )
        ?>
        <p class="profile-user-name">
            <?= h($user['User']['display_username']) ?>
        </p>
    </div>

    <div class="profile-user-numbers-wrap">
        <div class="profile-user-numbers-action">
            <div class="profile-user-numbers-action-counts">
                <?= h($action_count) ?>
            </div>
            <span class="profile-user-numbers-category-action">
                <?= __d('gl', 'アクション') ?>
            </span>
        </div>
        <div class="profile-user-numbers-post">
            <div class="profile-user-numbers-post-counts">
                <?= h($post_count) ?>
            </div>
            <span class="profile-user-numbers-category-post">
                <?= __d('gl', '投稿') ?>
            </span>
        </div>
        <div class="profile-user-numbers-like">
            <div class="profile-user-numbers-like-counts">
                <?= h($this->NumberEx->formatHumanReadable($like_count)) ?>
            </div>
            <span class="profile-user-numbers-category-like">
                <?= __d('gl', 'いいね') ?>
            </span>
        </div>
        <?php if ($this->Session->read('Auth.User.id') == $user['User']['id']): ?>
            <?= $this->Html->link(__d('gl', 'プロフィール編集'),
                                  [
                                      'controller' => 'users',
                                      'action'     => 'settings',
                                      '#'          => 'profile'
                                  ],
                                  [
                                      'class' => 'btn-profile-edit'
                                  ])
            ?>
        <?php endif ?>
    </div>
    <div class="profile-user-comments showmore">
        <?= $this->TextEx->autoLink($user['TeamMember']['comment']) ?>
    </div>
</div>
<div class="profile-user-tab-group">
    <a class="profile-user-goal-tab <?= $this->request->params['action'] == 'view_goals' ? "profile-user-tab-active" : null ?>"
       href="<?= $this->Html->url(
           [
               'controller' => 'users',
               'action'     => 'view_goals',
               'user_id'    => $user['User']['id'],
           ]); ?>">
        <i class="fa fa-flag profile-user-tab-icon"></i>

        <p class="profile-user-tab-title">
            <?= h(__d('gl', 'ゴール')) ?>
        </p>
    </a>
    <a class="profile-user-action-tab <?= $this->request->params['action'] == 'view_actions' ? "profile-user-tab-active" : null ?>"
       href="<?= $this->Html->url(
           [
               'controller' => 'users',
               'action'     => 'view_actions',
               'user_id'    => $user['User']['id'],
               'page_type'  => 'image',
           ]); ?>">
        <i class="fa fa-check-circle profile-user-tab-icon"></i>

        <p class="profile-user-tab-title">
            <?= h(__d('gl', 'アクション')) ?>
        </p>
    </a>
    <a class="profile-user-post-tab <?= $this->request->params['action'] == 'view_posts' ? "profile-user-tab-active" : null ?>"
       href="<?= $this->Html->url(
           [
               'controller' => 'users',
               'action'     => 'view_posts',
               'user_id'    => $user['User']['id'],
           ]); ?>">
        <i class="fa fa-comment-o profile-user-tab-icon"></i>

        <p class="profile-user-tab-title">
            <?= h(__d('gl', '投稿')) ?>
        </p>
    </a>
    <a class="profile-user-status-tab <?= $this->request->params['action'] == 'view_info' ? "profile-user-tab-active" : null ?>"
       href="<?= $this->Html->url(
           [
               'controller' => 'users',
               'action'     => 'view_info',
               'user_id'    => $user['User']['id'],
           ]) ?>">
        <i class="fa fa-user profile-user-tab-icon"></i>

        <p class="profile-user-tab-title">
            <?= h(__d('gl', '基本データ')) ?>
        </p>
    </a>
</div>
<!-- END app/View/Elements/simplex_top_section.ctp -->
