<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 7/17/14
 * Time: 4:23 PM
 *
 * @var                    $comment
 * @var                    $user
 * @var                    $like
 * @var CodeCompletionView $this
 */
?>
<div class="col col-xxs-12">
    <?=
    $this->Upload->uploadImage($user, 'User.photo', ['style' => 'small'],
                               ['class' => 'gl-comment-img'])
    ?>
    <div class="gl-comment-body"><span>
                    <?= h($user['display_username']) ?></span>
        <?= nl2br($this->Text->autoLink(h($comment['body']))) ?>
        <div>
            <?= $this->TimeEx->elapsedTime(h($comment['created'])) ?>
            <a href="#" class="click-like"
               like_count_id="CommentLikeCount_<?= $comment['id'] ?>"
               model_id="<?= $comment['id'] ?>"
               like_type="comment">
                <?= empty($like) ? __d('gl', "いいね！") : __d('gl', "いいね取り消し") ?></a>
            <span class="pull-right">
                            <a href="<?= $this->Html->url(['controller' => 'posts', 'action' => 'ajax_get_comment_liked_users', $comment['id']]) ?>"
                               class="modal-ajax-get">
                                <i class="fa fa-thumbs-o-up"></i>&nbsp;<span
                                    id="CommentLikeCount_<?= $comment['id'] ?>"><?= $comment['comment_like_count'] ?></span></a>
            <a href="#"><i class="fa fa-check"></i>&nbsp;<span><?= $comment['comment_read_count'] ?></span></a>
            </span>
        </div>
    </div>
</div>
