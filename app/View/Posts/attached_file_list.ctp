<?php
/**
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Pages
 * @since         CakePHP(tm) v 0.10.0.1076
 * @var CodeCompletionView $this
 * @var                    $params
 * @var                    $current_circle
 * @var                    $user_status
 * @var                    $circle_member_count
 * @var                    $circle_status
 */
?>
<!-- START app/View/Posts/attached_file_list.ctp -->
<?php if ($this->Session->read('current_team_id')): ?>
    <?php
    if (isset($user_status)) {
        if (viaIsSet($params['controller']) == 'posts' && viaIsSet($params['action']) == 'attached_file_list' && ($user_status == 'joined' || $user_status == 'admin')) {
            echo $this->element("Feed/common_form");
        }
    }
    else {
        echo $this->element("Feed/common_form");
    }
    ?>
    <?= $this->element('Feed/feed_share_range_filter',
                       compact('current_circle', 'user_status', 'circle_member_count', 'circle_status',
                               'feed_filter')) ?>
    <a href="" class="alert alert-info feed-notify-box" role="alert" style="margin-bottom:5px;display:none;opacity:0;">
        <span class="num"></span><?= __d('gl', "件の新しい投稿があります。") ?></a>
    <div class="panel panel-default">
        <?php foreach ($files as $file): ?>
            <div class="panel-body pt_10px plr_11px pb_8px bd-t">
                <?php $p_id = isset($file['PostFile'][0]['post_id']) ? $file['PostFile'][0]['post_id'] : null; ?>
                <?php $p_id = isset($file['CommentFile'][0]['Comment']['post_id']) ? $file['CommentFile'][0]['Comment']['post_id'] : null; ?>
                <?= $this->element('Feed/attached_file_item',
                                   ['data' => $file, 'page_type' => 'file_list', 'post_id' => $p_id]) ?>
            </div>
        <?php endforeach ?>

    </div>

    <?= $this->element('Feed/circle_join_button', compact('current_circle', 'user_status')) ?>
<?php else: ?>
    <?= $this->Html->link(__d('gl', "チームを作成してください。"), ['controller' => 'teams', 'action' => 'add']) ?>
<?php endif; ?>
<!-- END app/View/Posts/attached_file_list.ctp -->
