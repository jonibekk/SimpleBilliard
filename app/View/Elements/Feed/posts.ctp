<?php
/**
 * Created by PhpStorm.
 * User: daikihirakata
 * Date: 2014/07/06
 * Time: 1:03
 *
 * @var                    $posts
 * @var CodeCompletionView $this
 */
?>
<? if (!empty($posts)): ?>
    <? foreach ($posts as $post_key => $post): ?>
        <div class="panel panel-default">
            <div class="panel-body gl-feed">
                <div class="col col-xxs-12">
                    <? if ($post['User']['id'] === $this->Session->read('Auth.User.id')): ?>
                        <div class="pull-right">
                            <div class="dropdown">
                                <a href="#" class="" data-toggle="dropdown" id="download">
                                    <i class="fa fa-chevron-down"></i>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="download">
                                    <li><a href="#" class="target-toggle"
                                           target-id="PostEditFormBody_<?= $post['Post']['id'] ?>"><?=
                                            __d('gl',
                                                "投稿を編集") ?></a>
                                    </li>
                                    <li><?=
                                        $this->Form->postLink(__d('gl', "投稿を削除"),
                                                              ['controller' => 'posts', 'action' => 'post_delete', $post['Post']['id']],
                                                              null, __d('gl', "本当にこの投稿を削除しますか？")) ?></li>
                                </ul>
                            </div>
                        </div>
                    <? endif; ?>
                    <?=
                    $this->Upload->uploadImage($post['User'], 'User.photo', ['style' => 'medium'],
                                               ['class' => 'gl-feed-img']) ?>
                    <div><?= h($post['User']['display_username']) ?></div>
                    <div><?= $this->TimeEx->elapsedTime(h($post['Post']['created'])) ?></div>
                </div>
                <div class="col col-xxs-12">
                    <?= nl2br($this->Text->autoLink(h($post['Post']['body']))) ?>
                    <? if ($post['User']['id'] === $this->Session->read('Auth.User.id')): ?>
                        <?= $this->element('Feed/post_edit_form', compact('post')) ?>
                    <? endif; ?>
                </div>
                <div class="col col-xxs-12">
                    <a href="#" class="click-like"
                       like_count_id="PostLikeCount_<?= $post['Post']['id'] ?>"
                       model_id="<?= $post['Post']['id'] ?>"
                       like_type="post">
                        <?= empty($post['MyPostLike']) ? __d('gl', "いいね！") : __d('gl', "いいね取り消し") ?></a>

                    &nbsp;<a class="trigger-click"
                             href="#"
                             target-id="<?= "CommentFormBody_{$post['Post']['id']}" ?>"><?=
                        __d('gl',
                            "コメントする") ?></a>
                                <span class="pull-right">
                            <a href="<?= $this->Html->url(['controller' => 'posts', 'action' => 'ajax_get_post_liked_users', $post['Post']['id']]) ?>"
                               class="modal-ajax-get">
                                <i class="fa fa-thumbs-o-up"></i>&nbsp;<span
                                    id="PostLikeCount_<?= $post['Post']['id'] ?>"><?= $post['Post']['post_like_count'] ?></span>
                            </a>
            <a href="<?= $this->Html->url(['controller' => 'posts', 'action' => 'ajax_get_post_red_users', $post['Post']['id']]) ?>"
               class="modal-ajax-get"><i
                    class="fa fa-check"></i>&nbsp;<span><?= $post['Post']['post_read_count'] ?></span>
            </a>
            </span>

                </div>
            </div>
            <div class="panel-body gl-feed gl-comment-block">
                <? if ($post['Post']['comment_count'] > 3): ?>
                    <a href="#" class="btn btn-link click-comment-all"
                       id="Comments_<?= $post['Post']['id'] ?>"
                       parent-id="Comments_<?= $post['Post']['id'] ?>"
                       get-url="<?= $this->Html->url(["controller" => "posts", 'action' => 'ajax_get_comment', $post['Post']['id']]) ?>"
                        >
                        <i class="fa fa-comment-o"></i>&nbsp;<?=
                        __d('gl', "他%s件のコメントを見る",
                            $post['Post']['comment_count'] - 3) ?></a>
                <? endif; ?>
                <? foreach ($post['Comment'] as $comment): ?>
                    <?=
                    $this->element('Feed/comment',
                                   ['comment' => $comment, 'user' => $comment['User'], 'like' => $comment['MyCommentLike']]) ?>
                <? endforeach ?>
                <div class="col col-xxs-12">
                    <?=
                    $this->Upload->uploadImage($this->Session->read('Auth.User'), 'User.photo', ['style' => 'small'],
                                               ['class' => 'gl-comment-img']) ?>
                    <div class="gl-comment-body">
                        <?=
                        $this->Form->create('Comment', [
                            'url'           => ['controller' => 'posts', 'action' => 'comment_add'],
                            'inputDefaults' => [
                                'div'       => 'form-group',
                                'label'     => false,
                                'wrapInput' => '',
                                'class'     => 'form-control'
                            ],
                            'class'         => '',
                            'novalidate'    => true,
                        ]); ?>
                        <?=
                        $this->Form->input('body', [
                            'id'                       => "CommentFormBody_{$post['Post']['id']}",
                            'label'                    => false,
                            'type'                     => 'textarea',
                            'rows'                     => 1,
                            'required'                 => true,
                            'placeholder'              => __d('gl', "コメントする"),
                            'class'                    => 'form-control tiny-form-text blank-disable',
                            'target_show_id'           => "Comment_{$post['Post']['id']}",
                            'target-id'                => "CommentSubmit_{$post['Post']['id']}",
                            "data-bv-notempty-message" => __d('validate', "何も入力されていません。"),
                        ])
                        ?>
                        <?= $this->Form->hidden('post_id', ['value' => $post['Post']['id']]) ?>
                        <div class="" style="display: none" id="Comment_<?= $post['Post']['id'] ?>">
                            <?=
                            $this->Form->submit(__d('gl', "コメントする"),
                                                ['class' => 'btn btn-primary pull-right', 'id' => "CommentSubmit_{$post['Post']['id']}", 'disabled' => 'disabled']) ?>
                            <div class="clearfix"></div>
                        </div>
                        <?= $this->Form->end() ?>
                    </div>
                </div>
            </div>
        </div>
    <? endforeach ?>
<? endif ?>
