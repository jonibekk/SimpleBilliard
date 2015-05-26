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
<?php if (!empty($posts)):
    $items_per_group = 20;

    ?>
    <!-- START app/View/Elements/Feed/posts.ctp -->
    <?php foreach ($posts as $post_key => $post): ?>
        <div class="panel panel-default">
            <?php if ((isset($post['Goal']['id']) && $post['Goal']['id']) || isset($post['Circle']['id'])): ?>
                <!--START Goal Post Header -->

                <?php if (isset($post['Goal']['id']) && $post['Goal']['id']): ?>
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
                                    <i class="fa fa-flag font_gray"></i>&nbsp;<?= h($post['Goal']['name']) ?>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php elseif (isset($post['Circle']['id'])): ?>
                    <div class="panel-body pt_10px plr_11px pb_8px bd-b">
                        <div class="col col-xxs-12">
                            <div class="pull-right">
                                <a href="<?= $this->Html->url(['controller' => 'posts', 'action' => 'circle_feed', $post['Circle']['id']]) ?>"
                                   class="no-line font_verydark modal-ajax-get">
                                    <?=
                                    $this->Html->image('ajax-loader.gif',
                                                       [
                                                           'class'         => 'lazy media-object',
                                                           'data-original' => $this->Upload->uploadUrl($post,
                                                                                                       "Circle.photo",
                                                                                                       ['style' => 'small']),
                                                           'width'         => '32px',
                                                           'error-img'     => "/img/no-image-link.png",
                                                       ]
                                    )
                                    ?>
                                </a>
                            </div>
                            <div class="ln_contain w_88per">
                                <a href="<?= $this->Html->url(['controller' => 'posts', 'action' => 'circle_feed', $post['Circle']['id']]) ?>"
                                   class="no-line font_verydark modal-ajax-get">
                                    <i class="fa fa-circle-o font_gray"></i>&nbsp;<?= h($post['Circle']['name']) ?>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>



                <!--END Goal Post Header -->

            <?php endif; ?>
            <div class="panel-body pt_10px plr_11px pb_8px">
                <div class="col col-xxs-12 feed-user">
                    <div class="pull-right">
                        <div class="dropdown">
                            <a href="#" class="font_lightGray-gray font_11px" data-toggle="dropdown" id="download">
                                <i class="fa fa-chevron-down feed-arrow"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="download">
                                <?php if ($post['User']['id'] === $this->Session->read('Auth.User.id')
                                    && $post['Post']['type'] != Post::TYPE_ACTION
                                    && $post['Post']['type'] != Post::TYPE_KR_COMPLETE
                                    && $post['Post']['type'] != Post::TYPE_GOAL_COMPLETE
                                ): ?>
                                    <li><a href="#" class="target-toggle-click"
                                           target-id="PostEditForm_<?= $post['Post']['id'] ?>"
                                           opend-text="<?= __d('gl', "編集をやめる") ?>"
                                           closed-text="<?= __d('gl', "投稿を編集") ?>"
                                           click-target-id="PostEditFormBody_<?= $post['Post']['id'] ?>"
                                           hidden-target-id="PostTextBody_<?= $post['Post']['id'] ?>"
                                           ajax-url="<?= $this->Html->url(['controller' => 'posts', 'action' => 'ajax_get_edit_post_form', $post['Post']['id']]) ?>"
                                            ><?= __d('gl', "投稿を編集") ?></a>
                                    </li>
                                <?php endif ?>
                                <?php if ($my_member_status['TeamMember']['admin_flg'] || $post['User']['id'] === $this->Session->read('Auth.User.id')): ?>
                                    <?php if ($post['Post']['type'] != Post::TYPE_ACTION): ?>
                                        <li><?=
                                            $this->Form->postLink(__d('gl', "投稿を削除"),
                                                                  ['controller' => 'posts', 'action' => 'post_delete', $post['Post']['id']],
                                                                  null, __d('gl', "本当にこの投稿を削除しますか？")) ?></li>
                                    <?php endif; ?>
                                <?php endif ?>
                                <li>
                                    <a href="#" class="copy_me"
                                       onclick="copyToClipboard('<?=
                                       $this->Html->url(['controller' => 'posts', 'action' => 'feed', 'post_id' => $post['Post']['id']],
                                                        true) ?>')">
                                        <?= __d('gl', "リンクをコピー") ?></a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <?=
                    $this->Html->image('ajax-loader.gif',
                                       [
                                           'class'         => 'lazy feed-img',
                                           'data-original' => $this->Upload->uploadUrl($post['User'], 'User.photo',
                                                                                       ['style' => 'medium']),
                                       ]
                    )
                    ?>
                    <div class="font_14px font_bold font_verydark"><?= h($post['User']['display_username']) ?></div>
                    <?= $this->element('Feed/display_share_range', compact('post')) ?>
                </div>
                <?= $this->element('Feed/post_body', compact('post')) ?>
                <?php $photo_count = 0;
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
                <?php if ($photo_count): ?>
                    <div class="col col-xxs-12 pt_10px">
                        <div id="CarouselPost_<?= $post['Post']['id'] ?>" class="carousel slide" data-ride="carousel">
                            <!-- Indicators -->
                            <?php if ($photo_count >= 2): ?>
                                <ol class="carousel-indicators">
                                    <?php $index = 0 ?>
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <?php if ($post[$model_name]["photo{$i}_file_name"]): ?>
                                            <li data-target="#CarouselPost_<?= $post[$model_name]['id'] ?>"
                                                data-slide-to="<?= $index ?>"
                                                class="<?= ($index === 0) ? "active" : null ?>"></li>
                                            <?php $index++ ?>
                                        <?php endif ?>
                                    <?php endfor ?>
                                </ol>
                            <?php endif; ?>
                            <!-- Wrapper for slides -->
                            <div class="carousel-inner">
                                <?php $index = 0 ?>
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <?php if ($post[$model_name]["photo{$i}_file_name"]): ?>
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
                                            <?php $index++ ?>
                                        </div>
                                    <?php endif ?>
                                <?php endfor ?>
                            </div>

                            <!-- Controls -->
                            <?php if ($photo_count >= 2): ?>
                                <a class="left carousel-control" href="#CarouselPost_<?= $post['Post']['id'] ?>"
                                   data-slide="prev">
                                    <span class="glyphicon glyphicon-chevron-left"></span>
                                </a>
                                <a class="right carousel-control" href="#CarouselPost_<?= $post['Post']['id'] ?>"
                                   data-slide="next">
                                    <span class="glyphicon glyphicon-chevron-right"></span>
                                </a>
                            <?php endif; ?>
                        </div>

                    </div>
                <?php endif; ?>
                <?php if ($post['Post']['site_info']): ?>
                    <?php $site_info = json_decode($post['Post']['site_info'], true) ?>
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
                                        <?php if (isset($site_info['description'])): ?>
                                            <div class="font_12px site-info-txt">
                                                <?= mb_strimwidth(h($site_info['description']), 0, 110, "...") ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php elseif ($post['Post']['type'] == Post::TYPE_CREATE_GOAL && isset($post['Goal']['id']) && $post['Goal']['id']): ?>
                    <?= $this->element('Feed/goal_sharing_block', compact('post')) ?>
                <?php elseif ($post['Post']['type'] == Post::TYPE_CREATE_CIRCLE && isset($post['Circle']['id']) && $post['Circle']['id']): ?>
                    <div class="col col-xxs-12 pt_10px">
                        <a href="<?= $this->Html->url(['controller' => 'posts', 'action' => 'feed', 'circle_id' => $post['Circle']['id']]) ?>"
                           class="no-line font_verydark">
                            <div class="site-info bd-radius_4px">
                                <div class="media">
                                    <div class="pull-left">
                                        <?=
                                        $this->Html->image('ajax-loader.gif',
                                                           [
                                                               'class'         => 'lazy media-object',
                                                               'data-original' => $this->Upload->uploadUrl($post,
                                                                                                           "Circle.photo",
                                                                                                           ['style' => 'medium_large']),
                                                               'width'         => '80px',
                                                           ]
                                        )
                                        ?>
                                    </div>
                                    <div class="media-body">
                                        <h4 class="media-heading font_18px"><?= mb_strimwidth(h($post['Circle']['name']),
                                                                                              0, 50,
                                                                                              "...") ?></h4>
                                        <?php if (isset($post['Circle']['description'])): ?>
                                            <div class="font_12px site-info-txt">
                                                <?= mb_strimwidth(h($post['Circle']['description']), 0, 110, "...") ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endif; ?>



                <?php if ($post['Post']['type'] == Post::TYPE_ACTION && isset($post['ActionResult']['KeyResult']['name'])): ?>
                    <div class="col col-xxs-12 pt_6px feed-contents">
                        <i class="fa fa-key disp_i"></i>&nbsp;<?= h($post['ActionResult']['KeyResult']['name']) ?>
                    </div>
                <?php endif; ?>
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
                <?php if ($post['Post']['comment_count'] > 3 && count($post['Comment']) == 3): ?>
                    <a href="#" class="btn btn-link click-comment-all"
                       id="Comments_<?= $post['Post']['id'] ?>"
                       parent-id="Comments_<?= $post['Post']['id'] ?>"
                       get-url="<?= $this->Html->url(["controller" => "posts", 'action' => 'ajax_get_old_comment', $post['Post']['id'], $post['Post']['comment_count'] - 3, 'long_text' => $long_text]) ?>"
                        >
                        <?php if ($post['unread_count'] > 0): ?>
                            <i class="fa fa-comment-o font_brownRed"></i>&nbsp;<?=
                            __d('gl', "他%s件のコメントを見る",
                                $post['Post']['comment_count'] - 3) ?>
                            <?=
                            __d('gl', "(%s)",
                                $post['unread_count']) ?>

                        <?php else: ?>
                            <span class="font_gray">
                            <i class="fa fa-comment-o font_brownRed"></i>&nbsp;<?=
                                __d('gl', "他%s件のコメントを見る",
                                    $post['Post']['comment_count'] - 3) ?>
                            </span>
                        <?php endif; ?>
                    </a>
                <?php endif; ?>

                <?php foreach ($post['Comment'] as $comment): ?>
                    <?=
                    $this->element('Feed/comment',
                                   ['comment' => $comment, 'user' => $comment['User'], 'like' => $comment['MyCommentLike']]) ?>
                <?php endforeach ?>

                <a href="#" class="btn btn-link click-comment-new"
                   id="Comments_new_<?= $post['Post']['id'] ?>"
                   style="display:none"
                   post-id="<?= $post['Post']['id'] ?>"
                   get-url="<?= $this->Html->url(["controller" => "posts", 'action' => 'ajax_get_latest_comment', $post['Post']['id']]) ?>"
                    >
                    <div class="alert alert-danger new-comment-read">
                        <span class="num">0</span>
                        <?= __d('gl', "件の新しいコメントがあります") ?>
                    </div>
                </a>

                <div class="new-comment-error" id="comment_error_<?= $post['Post']['id'] ?>">
                    <i class="fa fa-exclamation-circle"></i><span class="message"></span>
                </div>
                <div class="col-xxs-12 box-align feed-contents comment-contents">
                    <?=
                    $this->Html->image('ajax-loader.gif',
                                       [
                                           'class'         => 'lazy comment-img',
                                           'data-original' => $this->Upload->uploadUrl($this->Session->read('Auth.User'),
                                                                                       'User.photo',
                                                                                       ['style' => 'small']),
                                       ]
                    )
                    ?>
                    <div class="comment-body" id="NewCommentForm_<?= $post['Post']['id'] ?>">
                        <form action="#" id="" method="post" accept-charset="utf-8">
                            <div class="form-group mlr_-1px">
                                <textarea
                                    class="form-control font_12px comment-post-form box-align not-autosize click-get-ajax-form-replace"
                                    replace-elm-parent-id="NewCommentForm_<?= $post['Post']['id'] ?>"
                                    click-target-id="CommentFormBody_<?= $post['Post']['id'] ?>"
                                    tmp-target-height="32"
                                    ajax-url="<?= $this->Html->url(['controller' => 'posts', 'action' => 'ajax_get_new_comment_form', $post['Post']['id']]) ?>"
                                    wrap="soft" rows="1"
                                    placeholder="<?= __d('gl', "コメントする") ?>"
                                    cols="30"
                                    init-height="15"></textarea>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach ?>
    <!-- END app/View/Elements/Feed/posts.ctp -->
<?php endif ?>
<?php $this->append('script') ?>
    <script type="text/javascript">
        var track_load = 20;
        $(document).ready(function () {

            var loading = false;

            $(window).scroll(function(){
                if($(window).scrollTop() + $(window).height() == $(document).height())
                {
                    if(track_load >= 20 && loading==false)
                    {
                        $('#FeedMoreReadLink').trigger('click');
                        loading = true;
                        track_load++;
                    }
                    else{
                        loading = false;
                    }

                }
            });
        });
    </script>
<?php $this->end() ?>