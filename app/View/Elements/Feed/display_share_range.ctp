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
<?= $this->App->viewStartComment() ?>
<div class="font_11px font_lightgray oneline-ellipsis">
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
            <a href="#"
               data-url="<?= $this->Html->url([
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
            <a href="<?= "/circles/" . $post['PostShareCircle'][0]['circle_id'] ."/posts"?>"
               class="link-dark-gray">
                <i class="fa fa-circle-o"></i>&nbsp;<?= h($post['share_text']) ?>
            </a>
        <?php endif; ?>
    <?php endif; ?>
    <?php if ($post['Post']['type'] == Post::TYPE_ACTION): ?>
        <a href="#"
           data-url="<?= $this->Html->url([
               'controller' => 'goals',
               'action'     => 'ajax_get_goal_description_modal',
               'goal_id'    => $post['Goal']['id']
           ]) ?>"
           class="ml_8px modal-ajax-get link-dark-gray">
            <i class="fa fa-flag-o"></i>
            <span><?= h($post['Goal']['name']) ?></span>
        </a>

    <?php endif; ?>
</div>
<?= $this->App->viewEndComment() ?>
