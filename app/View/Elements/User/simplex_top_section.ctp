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
<!-- START app/View/Elements/User/simplex_top_section.ctp -->
<div class="panel-body profile-user-upper-panel">
    <div class="profile-user-avatar-wrap">
        <?php if ($this->Session->read('Auth.User.id') == $user['User']['id']): ?>
            <a href="<?= $this->Html->url(['controller' => 'users', 'action' => 'settings', '#' => 'profile']) ?>">
                <?=
                $this->Html->image('ajax-loader.gif',
                                   [
                                       'class'         => 'lazy profile-user-avatar',
                                       'data-original' => $this->Upload->uploadUrl($user['User'], 'User.photo',
                                                                                   ['style' => 'x_large']),
                                   ]
                )
                ?>
            </a>
        <?php else: ?>
            <?=
            $this->Html->image('ajax-loader.gif',
                               [
                                   'class'         => 'lazy profile-user-avatar',
                                   'data-original' => $this->Upload->uploadUrl($user['User'], 'User.photo',
                                                                               ['style' => 'x_large']),
                               ]
            )
            ?>
        <?php endif ?>
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
                <?= __('Action') ?>
            </span>
        </div>
        <div class="profile-user-numbers-post">
            <div class="profile-user-numbers-post-counts">
                <?= h($post_count) ?>
            </div>
            <span class="profile-user-numbers-category-post">
                <?= __('Posts') ?>
            </span>
        </div>
        <div class="profile-user-numbers-like">
            <div class="profile-user-numbers-like-counts">
                <?= h($this->NumberEx->formatHumanReadable($like_count, ['convert_start' => 10000])) ?>
            </div>
            <span class="profile-user-numbers-category-like">
                <?= __('いいね') ?>
            </span>
        </div>
        <?php if ($this->Session->read('Auth.User.id') == $user['User']['id']): ?>
            <?= $this->Html->link(__('プロフィール編集'),
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
        <?= nl2br($this->TextEx->autoLink($user['TeamMember']['comment'])) ?>
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
            <?= h(__('Goal')) ?>
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
            <?= h(__('Action')) ?>
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
            <?= h(__('Posts')) ?>
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
            <?= h(__('基本データ')) ?>
        </p>
    </a>
</div>
<!-- END app/View/Elements/User/simplex_top_section.ctp -->
