<?php
/**
 * Created by PhpStorm.
 * User: daikihirakata
 * Date: 5/19/15
 * Time: 02:34
 *
 * @var CodeCompletionView $this
 * @var                    $post
 */
//echo json_encode($post);
?>
<?= $this->App->viewStartComment()?>
<div class="font_11px font_lightgray">
    <!-- only show if created within 1hr -->
    <?php if ($post['Post']['created'] > REQUEST_TIMESTAMP_ONE_HR_AGO) { ?>
            <span class="label label-primary">New</span>
    <?php } ?>

    <?= $this->TimeEx->elapsedTime(h($post['Post']['created'])) ?>
    <?php if ($post['Post']['type'] != Post::TYPE_KR_COMPLETE &&
        $post['Post']['type'] != Post::TYPE_ACTION
    ): ?>
        <?php //自分のみ
        if ($post['share_mode'] == Post::SHARE_ONLY_ME && $post['Post']['type'] == Post::TYPE_NORMAL): ?>
            <span class="font_lightgray"> ･ </span>
            <i class="fa fa-user"></i>&nbsp;<?= h($post['share_text']) ?>
        <?php //共有ユーザ
        elseif ($post['share_mode'] == Post::SHARE_PEOPLE): ?>
            <span class="font_lightgray"> ･ </span>
            <a href="<?= $this->Html->url([
                'controller' => 'posts',
                'action'     => 'ajax_get_share_circles_users_modal',
                'post_id'    => $post['Post']['id']
            ]) ?>"
               class="modal-ajax-get-share-circles-users link-dark-gray">
                <i class="fa fa-user"></i>&nbsp;<?= h($post['share_text']) ?>
            </a>
        <?php //共有サークル、共有ユーザ
        elseif ($post['share_mode'] == Post::SHARE_CIRCLE): ?>
            <span class="font_lightgray"> ･ </span>
            <a href="<?= $this->Html->url([
                'controller' => 'posts',
                'action'     => 'ajax_get_share_circles_users_modal',
                'post_id'    => $post['Post']['id']
            ]) ?>"
               class="modal-ajax-get-share-circles-users link-dark-gray">
                <i class="fa fa-circle-o"></i>&nbsp;<?= h($post['share_text']) ?>
            </a>
        <?php endif; ?>
    <?php endif; ?>
</div>
<?= $this->App->viewEndComment()?>
