<?php
/**
 * @property NumberExHelper    $NumberEx
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
        <?php
        // いいね数は数字を３桁にする

        ?>
        アクション: <?= h($action_count) ?><br>
        投稿: <?= h($post_count) ?><br>
        いいね: <?= h($this->NumberEx->formatHumanReadable($like_count)) ?><br>
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
</div>
<!-- END app/View/Elements/simplex_top_section.ctp -->
