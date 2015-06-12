<?php
/**
 * Created by PhpStorm.
 * User: daikihirakata
 * Date: 5/19/15
 * Time: 02:34
 */
?>
<!-- START app/View/Elements/Feed/display_share_range.ctp -->
<div class="font_11px font_lightgray">
    <?= $this->TimeEx->elapsedTime(h($post['Post']['created'])) ?>
    <?php if ($post['Post']['type'] != Post::TYPE_ACTION
        && $post['Post']['type'] != Post::TYPE_KR_COMPLETE
    ): ?>
        <span class="font_lightgray"> ･ </span>
        <?php //公開の場合
        if ($post['share_mode'] == Post::SHARE_ALL): ?>
            <i class="fa fa-group"></i>&nbsp;<?= $post['share_text'] ?>
        <?php //自分のみ
        elseif ($post['share_mode'] == Post::SHARE_ONLY_ME): ?>
            <i class="fa fa-user"></i>&nbsp;<?= $post['share_text'] ?>
        <?php //共有ユーザ
        elseif ($post['share_mode'] == Post::SHARE_PEOPLE): ?>
            <a href="<?= $this->Html->url(['controller' => 'posts', 'action' => 'ajax_get_share_circles_users_modal', 'post_id'=>$post['Post']['id']]) ?>"
               class="modal-ajax-get-share-circles-users link-dark-gray">
                <i class="fa fa-user"></i>&nbsp;<?= $post['share_text'] ?>
            </a>
        <?php //共有サークル、共有ユーザ
        elseif ($post['share_mode'] == Post::SHARE_CIRCLE): ?>
            <a href="<?= $this->Html->url(['controller' => 'posts', 'action' => 'ajax_get_share_circles_users_modal', 'post_id'=>$post['Post']['id']]) ?>"
               class="modal-ajax-get-share-circles-users link-dark-gray">
                <i class="fa fa-circle-o"></i>&nbsp;<?= $post['share_text'] ?>
            </a>
        <?php endif; ?>
    <?php endif; ?>
</div>
<!-- END app/View/Elements/Feed/display_share_range.ctp -->
