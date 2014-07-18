<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 7/17/14
 * Time: 4:23 PM
 *
 * @var                    $comment
 * @var                    $user
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
                <?= empty($comment['MyCommentLike']) ? __d('gl', "いいね！") : __d('gl', "いいね取り消し") ?></a>
            <span id="CommentLikeCount_<?= $comment['id'] ?>"><?= $comment['comment_like_count'] ?></span></div>
    </div>
</div>
