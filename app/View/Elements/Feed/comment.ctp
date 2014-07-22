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
    <div class="gl-comment-body">
        <? if ($user['id'] === $this->Session->read('Auth.User.id')): ?>
            <div class="dropdown pull-right">
                <a href="#" class="" data-toggle="dropdown" id="download">
                    <i class="fa fa-chevron-down"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="download">
                    <li><a href="#" class="target-toggle-click"
                           target-id="CommentEditForm_<?= $comment['id'] ?>"
                           click-target-id="CommentEditFormBody_<?= $comment['id'] ?>"

                            ><?= __d('gl', "コメントを編集") ?></a></li>
                    <li><?=
                        $this->Form->postLink(__d('gl', "コメントを削除"),
                                              ['controller' => 'posts', 'action' => 'comment_delete', $comment['id']],
                                              null, __d('gl', "本当にこのコメントを削除しますか？")) ?></li>
                </ul>
            </div>
        <? endif; ?>

        <span>
                    <?= h($user['display_username']) ?></span>
        <?= $this->TextEx->autoLink($comment['body']) ?>
        <? if ($user['id'] === $this->Session->read('Auth.User.id')): ?>
            <?= $this->element('Feed/comment_edit_form', compact('comment')) ?>
        <? endif; ?>

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
            <a href="<?= $this->Html->url(['controller' => 'posts', 'action' => 'ajax_get_comment_red_users', $comment['id']]) ?>"
               class="modal-ajax-get"><i
                    class="fa fa-check"></i>&nbsp;<span><?= $comment['comment_read_count'] ?></span></a>
            </span>
        </div>
    </div>
</div>
