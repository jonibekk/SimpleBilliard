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
 * @var                    $file_type_options
 * @var                    $circle_file_list_base_url
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
        <?=
        $this->Form->input('file_type', [
            'label'        => false,
            'div'          => false,
            'required'     => true,
            'class'        => 'form-control disable-change-warning file-type-select font_12px',
            'id'           => 'SwitchFileType',
            'options'      => $file_type_options,
            'default'      => viaisset($this->request->params['named']['file_type']),
            'redirect-url' => $circle_file_list_base_url,
        ])
        ?>
        <?php foreach ($files as $file): ?>
            <div class="panel-body pt_10px plr_11px pb_8px bd-b">
                <?php
                if (!$p_id = viaIsSet($file['PostFile'][0]['post_id'])) {
                    $p_id = viaIsSet($file['CommentFile'][0]['Comment']['post_id']);
                }
                ?>
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
