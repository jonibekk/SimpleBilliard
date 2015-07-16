<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 7/9/15
 * Time: 3:50 PM
 *
 * @var $user
 * @var $post_count
 * @var $action_count
 * @var $like_count
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
        <?= __d('gl', 'アクション') ?>: <?= h($action_count) ?><br>
        <?= __d('gl', '投稿') ?>: <?= h($post_count) ?><br>
        <?= __d('gl', 'いいね') ?>: <?= h($this->NumberEx->formatHumanReadable($like_count)) ?><br>
        <?php if ($this->Session->read('Auth.User.id') == $user['User']['id']): ?>
            <?= $this->Html->link(__d('gl', 'プロフィール編集'),
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
        <?= $this->Html->link(__d('gl', 'ゴール'), [
            'controller' => 'users',
            'action'     => 'view_goals',
            'user_id'    => $user['User']['id'],
        ]); ?>
        |
        <?= $this->Html->link(__d('gl', 'アクション'), [
            'controller' => 'users',
            'action'     => 'view_actions',
            'user_id'    => $user['User']['id'],
            'page_type' => 'image',
        ]); ?>
        |
        <?= $this->Html->link(__d('gl', '投稿'), [
            'controller' => 'users',
            'action'     => 'view_posts',
            'user_id'    => $user['User']['id'],
        ]); ?>
        |
        <?= $this->Html->link(__d('gl', '基本データ'), [
            'controller' => 'users',
            'action'     => 'view_info',
            'user_id'    => $user['User']['id'],
        ]); ?>
    </div>
</div>
<!-- END app/View/Elements/simplex_top_section.ctp -->
