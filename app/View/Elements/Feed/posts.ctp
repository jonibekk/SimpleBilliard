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
            <? if (isset($post['Goal']['id']) && $post['Goal']['id']): ?>
                <!--START Goal Post Header -->
                <div class="panel-body pt_10px plr_11px pb_8px bd-b">
                    <div class="col col-xxs-12">
                        <div class="pull-right">
                            <a href="<?= $this->Html->url(['controller' => 'goals', 'action' => 'ajax_get_goal_detail_modal', $post['Goal']['id']]) ?>"
                               class="no-line font_verydark modal-ajax-get">
                                <?=
                                $this->Html->image('ajax-loader.gif',
                                                   [
                                                       'class'         => 'lazy media-object',
                                                       'data-original' => $this->Upload->uploadUrl($post,
                                                                                                   "Goal.photo",
                                                                                                   ['style' => 'small']),
                                                       'width'         => '32px',
                                                       'error-img'     => "/img/no-image-link.png",
                                                   ]
                                )
                                ?>
                            </a>
                        </div>
                        <div class="ln_contain w_88per">
                            <a href="<?= $this->Html->url(['controller' => 'goals', 'action' => 'ajax_get_goal_detail_modal', $post['Goal']['id']]) ?>"
                               class="no-line font_verydark modal-ajax-get">
                                <i class="fa fa-flag">&nbsp;<?= h($post['Goal']['name']) ?></i>
                            </a>

                        </div>
                    </div>
                </div>
                <!--END Goal Post Header -->
            <? endif; ?>
            <div class="panel-body pt_10px plr_11px pb_8px">
                <div class="col col-xxs-12 feed-user">
                    <div class="pull-right">
                        <div class="dropdown">
                            <a href="#" class="font_lightGray-gray font_11px" data-toggle="dropdown" id="download">
                                <i class="fa fa-chevron-down feed-arrow"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="download">
                                <? if ($post['User']['id'] === $this->Session->read('Auth.User.id')): ?>
                                    <li><a href="#" class="target-toggle-click"
                                           target-id="PostEditForm_<?= $post['Post']['id'] ?>"
                                           opend-text="<?= __d('gl', "編集をやめる") ?>"
                                           closed-text="<?= __d('gl', "投稿を編集") ?>"
                                           click-target-id="PostEditFormBody_<?= $post['Post']['id'] ?>"
                                           hidden-target-id="PostTextBody_<?= $post['Post']['id'] ?>"
                                            ><?= __d('gl', "投稿を編集") ?></a>
                                    </li>
                                <? endif ?>
                                <? if ($my_member_status['TeamMember']['admin_flg'] || $post['User']['id'] === $this->Session->read('Auth.User.id')): ?>
                                    <li><?=
                                        $this->Form->postLink(__d('gl', "投稿を削除"),
                                                              ['controller' => 'posts', 'action' => 'post_delete', $post['Post']['id']],
                                                              null, __d('gl', "本当にこの投稿を削除しますか？")) ?></li>
                                <? endif ?>
                                <li><a href="#" class="copy_me"
                                       data-clipboard-text="<?=
                                       $this->Html->url(['controller' => 'posts', 'action' => 'feed', 'post_id' => $post['Post']['id']],
                                                        true) ?>">
                                        <?= __d('gl', "リンクをコピー") ?></a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <?=
                    $this->Upload->uploadImage($post['User'], 'User.photo', ['style' => 'medium'],
                                               ['class' => 'feed-img']) ?>
                    <div class="font_14px font_bold font_verydark"><?= h($post['User']['display_username']) ?></div>
                    <div class="font_11px font_lightgray">
                        <?= $this->TimeEx->elapsedTime(h($post['Post']['created'])) ?>
                        <? if ($post['Post']['type'] != Post::TYPE_ACTION): ?>
                            <span class="font_lightgray"> ･ </span>
                            <?
                            //公開の場合
                            if ($post['share_mode'] == Post::SHARE_ALL): ?>
                                <i class="fa fa-group"></i>&nbsp;<?= $post['share_text'] ?>
                            <?
                            //自分のみ
                            elseif ($post['share_mode'] == Post::SHARE_ONLY_ME): ?>
                                <i class="fa fa-user"></i>&nbsp;<?= $post['share_text'] ?>
                            <?
                            //共有ユーザ
                            elseif ($post['share_mode'] == Post::SHARE_PEOPLE): ?>
                                <a href="<?= $this->Html->url(['controller' => 'posts', 'action' => 'ajax_get_share_circles_users_modal', $post['Post']['id']]) ?>"
                                   class="modal-ajax-get-share-circles-users link-dark-gray">
                                    <i class="fa fa-user"></i>&nbsp;<?= $post['share_text'] ?>
                                </a>
                            <?
                            //共有サークル、共有ユーザ
                            elseif ($post['share_mode'] == Post::SHARE_CIRCLE): ?>
                                <a href="<?= $this->Html->url(['controller' => 'posts', 'action' => 'ajax_get_share_circles_users_modal', $post['Post']['id']]) ?>"
                                   class="modal-ajax-get-share-circles-users link-dark-gray">
                                    <i class="fa fa-circle-o"></i>&nbsp;<?= $post['share_text'] ?>
                                </a>
                            <? endif; ?>
                        <? endif; ?>
                    </div>
                </div>
                <? if ($post['User']['id'] === $this->Session->read('Auth.User.id')): ?>
                    <div class="col col-xxs-12 p_0px">
                        <?= $this->element('Feed/post_edit_form', compact('post')) ?>
                    </div>
                <? endif; ?>
                <div class="col col-xxs-12 feed-contents post-contents showmore font_14px font_verydark box-align"
                     id="PostTextBody_<?= $post['Post']['id'] ?>">
                    <? if ($post['Post']['type'] == Post::TYPE_NORMAL): ?>
                        <?= $this->TextEx->autoLink($post['Post']['body']) ?>
                    <? elseif ($post['Post']['type'] == Post::TYPE_ACTION): ?>
                        <i class="fa fa-check-circle">&nbsp;<?= h($post['ActionResult']['Action']['name']) ?></i>
                    <? else: ?>
                        <?= Post::$TYPE_MESSAGE[$post['Post']['type']] ?>
                    <? endif; ?>
                </div>
                <?
                $photo_count = 0;
                //タイプ別に切り分け
                if ($post['Post']['type'] == Post::TYPE_ACTION) {
                    $model_name = 'ActionResult';
                }
                else {
                    $model_name = 'Post';
                }
                for ($i = 1; $i <= 5; $i++) {
                    if ($post[$model_name]["photo{$i}_file_name"]) {
                        $photo_count++;
                    }
                }
                ?>
                <? if ($photo_count): ?>
                    <div class="col col-xxs-12 pt_10px">
                        <div id="CarouselPost_<?= $post['Post']['id'] ?>" class="carousel slide" data-ride="carousel">
                            <!-- Indicators -->
                            <? if ($photo_count >= 2): ?>
                                <ol class="carousel-indicators">
                                    <? $index = 0 ?>
                                    <? for ($i = 1; $i <= 5; $i++): ?>
                                        <? if ($post[$model_name]["photo{$i}_file_name"]): ?>
                                            <li data-target="#CarouselPost_<?= $post[$model_name]['id'] ?>"
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
                                    <? if ($post[$model_name]["photo{$i}_file_name"]): ?>
                                        <div class="item <?= ($index === 0) ? "active" : null ?>">
                                            <a href="<?=
                                            $this->Upload->uploadUrl($post, "{$model_name}.photo" . $i,
                                                                     ['style' => 'large']) ?>"
                                               rel="lightbox" data-lightbox="LightBoxPost_<?= $post['Post']['id'] ?>">
                                                <?=
                                                $this->Html->image('ajax-loader.gif',
                                                                   [
                                                                       'class'         => 'lazy bd-s',
                                                                       'data-original' => $this->Upload->uploadUrl($post,
                                                                                                                   "{$model_name}.photo" . $i,
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
                    <div class="col col-xxs-12 pt_10px">
                        <a href="<?= isset($site_info['url']) ? $site_info['url'] : null ?>" target="_blank"
                           class="no-line font_verydark">
                            <div class="site-info bd-radius_4px">
                                <div class="media">
                                    <div class="pull-left">
                                        <?=
                                        $this->Html->image('ajax-loader.gif',
                                                           [
                                                               'class'         => 'lazy media-object',
                                                               'data-original' => $this->Upload->uploadUrl($post,
                                                                                                           "Post.site_photo",
                                                                                                           ['style' => 'small']),
                                                               'width'         => '80px',
                                                               'error-img'     => "/img/no-image-link.png",
                                                           ]
                                        )
                                        ?>
                                    </div>
                                    <div class="media-body">
                                        <h4 class="media-heading font_18px"><?= isset($site_info['title']) ? mb_strimwidth(h($site_info['title']),
                                                                                                                           0,
                                                                                                                           50,
                                                                                                                           "...") : null ?></h4>

                                        <p class="font_11px media-url"><?= isset($site_info['url']) ? h($site_info['url']) : null ?></p>
                                        <? if (isset($site_info['description'])): ?>
                                            <div class="font_12px site-info-txt">
                                                <?= mb_strimwidth(h($site_info['description']), 0, 110, "...") ?>
                                            </div>
                                        <? endif; ?>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                <? elseif ($post['Post']['type'] == Post::TYPE_CREATE_GOAL && isset($post['Goal']['id']) && $post['Goal']['id']): ?>
                    <div class="col col-xxs-12 pt_10px">
                        <a href="<?= $this->Html->url(['controller' => 'goals', 'action' => 'ajax_get_goal_detail_modal', $post['Goal']['id']]) ?>"
                           class="no-line font_verydark modal-ajax-get">
                            <div class="site-info bd-radius_4px">
                                <div class="media">
                                    <div class="pull-left">
                                        <?=
                                        $this->Html->image('ajax-loader.gif',
                                                           [
                                                               'class'         => 'lazy media-object',
                                                               'data-original' => $this->Upload->uploadUrl($post,
                                                                                                           "Goal.photo",
                                                                                                           ['style' => 'medium_large']),
                                                               'width'         => '80px',
                                                           ]
                                        )
                                        ?>
                                    </div>
                                    <div class="media-body">
                                        <h4 class="media-heading font_18px"><?= mb_strimwidth(h($post['Goal']['name']),
                                                                                              0, 50,
                                                                                              "...") ?></h4>
                                        <? if (isset($post['Goal']['Purpose']['name'])): ?>
                                            <div class="font_12px site-info-txt">
                                                <?= mb_strimwidth(h($post['Goal']['Purpose']['name']), 0, 110, "...") ?>
                                            </div>
                                        <? endif; ?>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                <? endif; ?>
                <? if ($post['Post']['type'] == Post::TYPE_ACTION && isset($post['ActionResult']['Action']['KeyResult']['name'])): ?>
                    <div class="col col-xxs-12 pt_6px">
                        <i class="fa fa-key">&nbsp;<?= h($post['ActionResult']['Action']['KeyResult']['name']) ?></i>
                    </div>
                <? endif; ?>
                <? if ($post['User']['id'] === $this->Session->read('Auth.User.id')): ?>
                    <div class="col col-xxs-12 p_0px">
                        <?= $this->element('Feed/post_edit_form', compact('post')) ?>
                    </div>
                <? endif; ?>

                <div class="col col-xxs-12 font_12px pt_8px">
                    <a href="#" class="click-like font_lightgray <?= empty($post['MyPostLike']) ? null : "liked" ?>"
                       like_count_id="PostLikeCount_<?= $post['Post']['id'] ?>"
                       model_id="<?= $post['Post']['id'] ?>"
                       like_type="post">
                        <?= __d('gl', "いいね！") ?></a>
                    <span class="font_lightgray"> ･ </span>
                                <span>
                            <a href="<?= $this->Html->url(['controller' => 'posts', 'action' => 'ajax_get_post_liked_users', $post['Post']['id']]) ?>"
                               class="modal-ajax-get font_lightgray">
                                <i class="fa fa-thumbs-o-up"></i>&nbsp;<span
                                    id="PostLikeCount_<?= $post['Post']['id'] ?>"><?= $post['Post']['post_like_count'] ?></span>
                            </a><span class="font_lightgray"> ･ </span>
            <a href="<?= $this->Html->url(['controller' => 'posts', 'action' => 'ajax_get_post_red_users', $post['Post']['id']]) ?>"
               class="modal-ajax-get font_lightgray"><i
                    class="fa fa-check"></i>&nbsp;<span><?= $post['Post']['post_read_count'] ?></span>
            </a>
            </span>

                </div>
            </div>
            <div class="panel-body ptb_8px plr_11px comment-block">
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
                    <?=
                    $this->element('Feed/comment',
                                   ['comment' => $comment, 'user' => $comment['User'], 'like' => $comment['MyCommentLike']]) ?>
                <? endforeach ?>
                <div class="col-xxs-12 box-align feed-contents comment-contents">
                    <?=
                    $this->Upload->uploadImage($this->Session->read('Auth.User'), 'User.photo', ['style' => 'small'],
                                               ['class' => 'comment-img']) ?>
                    <div class="comment-body">
                        <?=
                        $this->Form->create('Comment', [
                            'url'           => ['controller' => 'posts', 'action' => 'comment_add'],
                            'inputDefaults' => [
                                'div'       => 'form-group mlr_-1px',
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
                            'wrap'                     => 'soft',
                            'rows'                     => 1,
                            'required'                 => true,
                            'placeholder'              => __d('gl', "コメントする"),
                            'class'                    => 'form-control tiny-form-text blank-disable font_12px comment-post-form box-align',
                            'target_show_id'           => "Comment_{$post['Post']['id']}",
                            'target-id'                => "CommentSubmit_{$post['Post']['id']}",
                            "data-bv-notempty-message" => __d('validate', "何も入力されていません。"),
                        ])
                        ?>
                        <div class="form-group" id="CommentFormImage_<?= $post['Post']['id'] ?>"
                             style="display: none">
                            <ul class="input-images">
                                <? for ($i = 1; $i <= 5; $i++): ?>
                                    <li>
                                        <?=
                                        $this->element('Feed/photo_upload',
                                                       ['type' => 'comment', 'index' => $i, 'submit_id' => "CommentSubmit_{$post['Post']['id']}", 'post_id' => $post['Post']['id']]) ?>
                                    </li>
                                <? endfor ?>
                            </ul>
                        </div>
                        <?= $this->Form->hidden('post_id', ['value' => $post['Post']['id']]) ?>
                        <div class="comment-btn" style="display: none" id="Comment_<?= $post['Post']['id'] ?>">
                            <a href="#" class="target-show-target-click font_12px comment-add-pic"
                               target-id="CommentFormImage_<?= $post['Post']['id'] ?>"
                               click-target-id="Comment__Post_<?= $post['Post']['id'] ?>_Photo_1">
                                <button type="button" class="btn pull-left photo-up-btn" data-toggle="tooltip"
                                        data-placement="bottom"
                                        title="画像を追加する"><i class="fa fa-camera post-camera-icon"></i>
                                </button>

                            </a>

                            <?=
                            $this->Form->submit(__d('gl', "コメントする"),
                                                ['class' => 'btn btn-primary pull-right submit-btn', 'id' => "CommentSubmit_{$post['Post']['id']}", 'disabled' => 'disabled']) ?>
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
