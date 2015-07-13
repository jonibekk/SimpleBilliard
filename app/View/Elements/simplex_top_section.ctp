<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 7/9/15
 * Time: 3:50 PM
 *
 * @var CodeCompletionView $this
 */
?>
<!-- START app/View/Elements/simplex_top_section.ctp -->
<div class="panel-body">
    <div style="float:left">
        <?=
        $this->Html->image('ajax-loader.gif',
                           [
                               'class'         => 'lazy',
                               'data-original' => $this->Upload->uploadUrl($user['User'], 'User.photo',
                                                                           ['style' => 'large']),
                           ]
        )
        ?>
        <br>
        <?= h($user['User']['display_username']) ?>
    </div>

    <div>
        アクション: <?= h($action_count) ?><br>
        投稿: <?= h($post_count) ?><br>
        いいね: <?= h($like_count) ?><br>
        <?php if ($this->Session->read('Auth.User.id') == $user['User']['id']): ?>
            <?= $this->Html->link('プロフィール編集',
                                  [
                                      'controller' => 'users',
                                      'action'     => 'settings',
                                      '#'          => 'profile'
                                  ],
                                  [
                                      'class' => ''
                                  ])
            ?>
        <?php endif ?>
    </div>
    <div style="clear:both">
        <?= h($user['TeamMember']['comment']) ?>
    </div>
    <div>
        <?= $this->Html->link('ゴール', [
            'controller' => 'users',
            'action'     => 'view_goals',
            'user_id'    => $user['User']['id'],
        ]); ?>
        |
        <?= $this->Html->link('アクション', [
            'controller' => 'users',
            'action'     => 'view_actions',
            'user_id'    => $user['User']['id'],
        ]); ?>
        |
        <?= $this->Html->link('投稿', [
            'controller' => 'users',
            'action'     => 'view_posts',
            'user_id'    => $user['User']['id'],
        ]); ?>
        |
        <?= $this->Html->link('基本データ', [
            'controller' => 'users',
            'action'     => 'view_info',
            'user_id'    => $user['User']['id'],
        ]); ?>
    </div>
</div>
<!-- END app/View/Elements/simplex_top_section.ctp -->
