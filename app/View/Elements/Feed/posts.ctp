<?php
/**
 * Created by PhpStorm.
 * User: daikihirakata
 * Date: 2014/07/06
 * Time: 1:03
 *
 * @var                    $posts
 * @var                    $my_member_status
 * @var CodeCompletionView $this
 */
?>
<? if (!empty($posts)): ?>
    <!-- START app/View/Elements/Feed/posts.ctp -->
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
                                <li><a href="#" class="target-toggle-click"
                                       target-id="PostEditForm_<?= $post['Post']['id'] ?>"
                                       click-target-id="PostEditFormBody_<?= $post['Post']['id'] ?>"
                                        ><?= __d('gl', "投稿を編集") ?></a>
                                </li>
                                <li><?=
                                    $this->Form->postLink(__d('gl', "投稿を削除"),
                                                          ['controller' => 'posts', 'action' => 'post_delete', $post['Post']['id']],
                                                          null, __d('gl', "本当にこの投稿を削除しますか？")) ?></li>
                            </ul>
                        </div>
                    </div>
                <? elseif ($my_member_status['TeamMember']['admin_flg']): ?>
                    <div class="pull-right">
                        <?=
                        $this->Form->postLink('<i class="fa fa-times"></i>',
                                              ['controller' => 'posts', 'action' => 'post_delete', $post['Post']['id']],
                                              ['escape' => false], __d('gl', "本当にこの投稿を削除しますか？")) ?>
                    </div>
                <? endif; ?>
                <?=
                $this->Upload->uploadImage($post['User'], 'User.photo', ['style' => 'medium'],
                                           ['class' => 'gl-feed-img']) ?>
                <div class="font-size_14"><?= h($post['User']['display_username']) ?></div>
                <div class="font-size_11"><?= $this->TimeEx->elapsedTime(h($post['Post']['created'])) ?></div>
            </div>
            <div class="col col-xxs-12 showmore font-size_14">
                <?= $this->TextEx->autoLink($post['Post']['body']) ?>
            </div>

            <?
            $photo_count = 0;
            for ($i = 1; $i <= 5; $i++) {
                if ($post['Post']["photo{$i}_file_name"]) {
                    $photo_count++;
                }
            }
            ?>
            <? if ($photo_count): ?>
                <div class="col col-xxs-12">
                    <div id="CarouselPost_<?= $post['Post']['id'] ?>" class="carousel slide" data-ride="carousel">
                        <!-- Indicators -->
                        <? if ($photo_count >= 2): ?>
                            <ol class="carousel-indicators">
                                <? $index = 0 ?>
                                <? for ($i = 1; $i <= 5; $i++): ?>
                                    <? if ($post['Post']["photo{$i}_file_name"]): ?>
                                        <li data-target="#CarouselPost_<?= $post['Post']['id'] ?>"
                                            data-slide-to="<?= $index ?>"
                                            class="<?= ($index === 0) ? "active" : null ?>"></li>
                                        <? $index++ ?>
                                    <? endif ?>
                                <? endfor ?>
                            </ol>
                        <? endif; ?>
                        <!-- Wrapper for slides -->
                        <div class="carousel-inner">
                            <? $index = 0 ?>
                            <? for ($i = 1; $i <= 5; $i++): ?>
                                <? if ($post['Post']["photo{$i}_file_name"]): ?>
                                    <div class="item <?= ($index === 0) ? "active" : null ?>">
                                        <a href="<?=
                                        $this->Upload->uploadUrl($post, "Post.photo" . $i,
                                                                 ['style' => 'large']) ?>"
                                           rel="lightbox" data-lightbox="LightBoxPost_<?= $post['Post']['id'] ?>">
                                            <?=
                                            $this->Html->image('ajax-loader.gif',
                                                               [
                                                                   'class'         => 'lazy',
                                                                   'data-original' => $this->Upload->uploadUrl($post,
                                                                                                               "Post.photo" . $i,
                                                                                                               ['style' => 'small'])
                                                               ]
                                            )
                                            ?>
                                        </a>
                                        <? $index++ ?>
                                    </div>
                                <? endif ?>
                            <? endfor ?>
                        </div>

                        <!-- Controls -->
                        <? if ($photo_count >= 2): ?>
                            <a class="left carousel-control" href="#CarouselPost_<?= $post['Post']['id'] ?>"
                               data-slide="prev">
                                <span class="glyphicon glyphicon-chevron-left"></span>
                            </a>
                            <a class="right carousel-control" href="#CarouselPost_<?= $post['Post']['id'] ?>"
                               data-slide="next">
                                <span class="glyphicon glyphicon-chevron-right"></span>
                            </a>
                        <? endif; ?>
                    </div>

                </div>
            <? endif; ?>
            <? if ($post['Post']['site_info']): ?>
                <? $site_info = json_decode($post['Post']['site_info'], true) ?>
                <div class="col col-xxs-12">
                    <div class="site-info">
                        <div class="media">
                            <div class="pull-left">
                                <? if (isset($site_info['image'])): ?>
                                    <?=
                                    $this->Html->image('ajax-loader.gif',
                                                       [
                                                           'class'         => 'lazy media-object',
                                                           'data-original' => $site_info['image'],
                                                           'width'         => '80px',
                                                           'height' => 'auto'
                                                       ]
                                    )
                                    ?>
                                <? else: ?>
                                    <?=
                                    $this->Html->image('no-image.jpg',
                                                       ['class' => 'media-object', 'width' => '80px', 'height' => '80px']) ?>
                                <?endif; ?>
                            </div>

                            <div class="media-body">
                                <a href="<?= isset($site_info['url']) ? $site_info['url'] : null ?>" target="_blank">
                                    <h4 class="media-heading font-size_18"><?= isset($site_info['title']) ? $site_info['title'] : null ?></h4>
                                </a>

                                <p class="font-size_11"><?= isset($site_info['url']) ? $site_info['url'] : null ?></p>
                                <? if (isset($site_info['description'])): ?>
                                    <div class="font-size_12">
                                        <?= $site_info['description'] ?>
                                    </div>
                                <? endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <? endif; ?>
            <? if ($post['User']['id'] === $this->Session->read('Auth.User.id')): ?>
                <div class="col col-xxs-12">
                    <?= $this->element('Feed/post_edit_form', compact('post')) ?>
                </div>
            <? endif; ?>

            <div class="col col-xxs-12 font-size_12">
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
            <? if ($post['Post']['comment_count'] > 3 && count($post['Comment']) == 3): ?>
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
                <div class="font-size_12">
                    <?=
                    $this->element('Feed/comment',
                                   ['comment' => $comment, 'user' => $comment['User'], 'like' => $comment['MyCommentLike']]) ?>
                </div>
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
                        'type'          => 'file',
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
                        'class' => 'form-control tiny-form-text blank-disable font-size_12',
                        'target_show_id'           => "Comment_{$post['Post']['id']}",
                        'target-id'                => "CommentSubmit_{$post['Post']['id']}",
                        "data-bv-notempty-message" => __d('validate', "何も入力されていません。"),
                    ])
                    ?>
                    <div class="form-group" id="CommentFormImage_<?= $post['Post']['id'] ?>"
                         style="display: none">
                        <ul class="gl-input-images">
                            <? for ($i = 1; $i <= 5; $i++): ?>
                                <li>
                                    <?=
                                    $this->element('Feed/photo_upload',
                                                   ['type' => 'comment', 'index' => $i, 'submit_id' => "CommentSubmit_{$post['Post']['id']}"]) ?>
                                </li>
                            <? endfor ?>
                        </ul>
                    </div>
                    <?= $this->Form->hidden('post_id', ['value' => $post['Post']['id']]) ?>
                    <div class="" style="display: none" id="Comment_<?= $post['Post']['id'] ?>">
                        <a href="#" class="target-show-this-del font-size_12"
                           target-id="CommentFormImage_<?= $post['Post']['id'] ?>"><i class="fa fa-file-o"></i>&nbsp;<?=
                            __d('gl',
                                "画像を追加する") ?>
                        </a>

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
    <!-- END app/View/Elements/Feed/posts.ctp -->
<? endif ?>
