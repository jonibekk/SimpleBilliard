<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 7/17/14
 * Time: 4:23 PM
 *
 * @var                    $comment
 * @var                    $my_member_status
 * @var                    $user
 * @var                    $like
 * @var CodeCompletionView $this
 */
?>
<!-- START app/View/Elements/Feed/comment.ctp -->
<div class="font-size_12">
    <div class="col col-xxs-12 gl-comment-main">
        <?=
        $this->Upload->uploadImage($user, 'User.photo', ['style' => 'small'],
                                   ['class' => 'gl-comment-img'])
        ?>
        <div class="gl-comment-body">
            <div class="col col-xxs-12 gl-comment-text gl-comment-user">
                <? if ($user['id'] === $this->Session->read('Auth.User.id')): ?>
                    <div class="dropdown pull-right">
                        <a href="#" class="link-gray font-size_11" data-toggle="dropdown" id="download">
                            <i class="fa fa-chevron-down gl-comment-arrow"></i>
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
                <? elseif ($my_member_status['TeamMember']['admin_flg']): ?>
                    <div class="pull-right develop--link-gray">
                        <?=
                        $this->Form->postLink('<i class="fa fa-times gl-comment-cross"></i>',
                                              ['controller' => 'posts', 'action' => 'comment_delete', $comment['id']],
                                              ['escape' => false], __d('gl', "本当にこのコメントを削除しますか？")) ?>
                    </div>
                <? endif; ?>
                <div class="gl-comment-name font-verydark"><?= h($user['display_username']) ?></div>
            </div>
            <div class="col col-xxs-12 showmore gl-comment-text">
                <div class="gl-comment-contents font-verydark"><?= $this->TextEx->autoLink($comment['body']) ?></div>
            </div>
            <? if ($user['id'] === $this->Session->read('Auth.User.id')): ?>
                <?= $this->element('Feed/comment_edit_form', compact('comment')) ?>
            <? endif; ?>

            <?
            $photo_count = 0;
            for ($i = 1; $i <= 5; $i++) {
                if ($comment["photo{$i}_file_name"]) {
                    $photo_count++;
                }
            }
            ?>
            <? if ($photo_count): ?>
                <div class="col col-xxs-12 gl-comment-photo">

                <div id="CarouselComment_<?= $comment['id'] ?>" class="carousel slide" data-ride="carousel">
                        <!-- Indicators -->
                        <? if ($photo_count >= 2): ?>
                            <ol class="carousel-indicators">
                                <? $index = 0 ?>
                                <? for ($i = 1; $i <= 5; $i++): ?>
                                    <? if ($comment["photo{$i}_file_name"]): ?>
                                        <li data-target="#CarouselComment_<?= $comment['id'] ?>"
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
                                <? if ($comment["photo{$i}_file_name"]): ?>
                                    <div class="item <?= ($index === 0) ? "active" : null ?>">
                                        <a href="<?=
                                        $this->Upload->uploadUrl($comment, "Comment.photo" . $i,
                                                                 ['style' => 'large']) ?>" rel="lightbox"
                                           data-lightbox="LightBoxComment_<?= $comment['id'] ?>">
                                            <?=
                                            $this->Html->image('ajax-loader.gif',
                                                               [
                                                                   'class'         => 'lazy',
                                                                   //'style'         => 'width: 50px; height: 50px;',
                                                                   'data-original' => $this->Upload->uploadUrl($comment,
                                                                                                               "Comment.photo" . $i,
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
                            <a class="left carousel-control" href="#CarouselComment_<?= $comment['id'] ?>"
                               data-slide="prev">
                                <span class="glyphicon glyphicon-chevron-left"></span>
                            </a>
                            <a class="right carousel-control" href="#CarouselComment_<?= $comment['id'] ?>"
                               data-slide="next">
                                <span class="glyphicon glyphicon-chevron-right"></span>
                            </a>
                        <? endif; ?>
                    </div>

                </div>
            <? endif; ?>

            <? if ($comment['site_info']): ?>
                <? $site_info = json_decode($comment['site_info'], true) ?>
                <div class="col col-xxs-12">
                    <a href="<?= isset($site_info['url']) ? $site_info['url'] : null ?>" target="_blank"
                       class="no-line">
                        <div class="site-info">
                            <div class="media">
                                <div class="pull-left">
                                    <?=
                                    $this->Html->image('ajax-loader.gif',
                                                       [
                                                           'class'         => 'lazy media-object',
                                                           'data-original' => $this->Upload->uploadUrl($comment,
                                                                                                       "Comment.site_photo",
                                                                                                       ['style' => 'small']),
                                                           'width'         => '80px',
                                                       ]
                                    )
                                    ?>
                                </div>

                                <div class="media-body">
                                    <h4 class="media-heading font-size_18"><?= isset($site_info['title']) ? $site_info['title'] : null ?></h4>

                                    <p class="font-size_11"><?= isset($site_info['url']) ? $site_info['url'] : null ?></p>
                                    <? if (isset($site_info['description'])): ?>
                                        <div class="font-size_12">
                                            <?= $site_info['description'] ?>
                                        </div>
                                    <? endif; ?>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            <? endif; ?>


            <div class="gl-comment-info">
                <?= $this->TimeEx->elapsedTime(h($comment['created'])) ?><span class="font-lightgray"> ･ </span>
                <a href="#" class="click-like link-rose-red"
                   like_count_id="CommentLikeCount_<?= $comment['id'] ?>"
                   model_id="<?= $comment['id'] ?>"
                   like_type="comment">
                    <?= empty($like) ? __d('gl', "いいね！") : __d('gl', "いいね取り消し") ?></a><span
                    class="font-lightgray"> ･ </span>
            <span>
                            <a href="<?= $this->Html->url(['controller' => 'posts', 'action' => 'ajax_get_comment_liked_users', $comment['id']]) ?>"
                               class="modal-ajax-get link-rose-red">
                            <i class="fa fa-thumbs-o-up"></i>&nbsp;<span
                                    id="CommentLikeCount_<?= $comment['id'] ?>"><?= $comment['comment_like_count'] ?></span></a><span
                    class="font-lightgray"> ･ </span>
            <a href="<?= $this->Html->url(['controller' => 'posts', 'action' => 'ajax_get_comment_red_users', $comment['id']]) ?>"
               class="modal-ajax-get link-rose-red"><i
                    class="fa fa-check"></i>&nbsp;<span><?= $comment['comment_read_count'] ?></span></a>
            </span>
            </div>
        </div>
    </div>
</div>
<!-- END app/View/Elements/Feed/comment.ctp -->
