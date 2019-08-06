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
<?php if (!empty($posts)): ?>
    <?= $this->App->viewStartComment() ?>
    <?php foreach ($posts as $post_key => $post): ?>
        <div class="panel panel-default">
            <div class="panel-body pt_10px plr_11px pb_8px">
                <div class="col col-xxs-12 feed-user">
                    <div class="pull-right">
                        <div class="dropdown">
                            <a href="#" class="font_lightGray-gray font_11px" data-toggle="dropdown" id="download">
                                <i class="fa fa-ellipsis-v feed-arrow"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="download">
                                <?php if ($post['User']['id'] === $this->Session->read('Auth.User.id')
                                    && $post['Post']['type'] == Post::TYPE_ACTION
                                ): ?>
                                    <li>
                                        <a href="#"
                                           data-url="<?= $this->Html->url([
                                               'controller'       => 'goals',
                                               'action'           => 'ajax_get_edit_action_modal',
                                               'action_result_id' => $post['Post']['action_result_id']
                                           ]) ?>"
                                           class="modal-ajax-get"
                                        ><?= __("Edit Action") ?></a>
                                    </li>
                                <?php endif ?>
                                <li><a href="#" class="copy_me"
                                       data-clipboard-text="<?=
                                       $this->Html->url([
                                           'controller' => 'posts',
                                           'action'     => 'feed',
                                           'post_id'    => $post['Post']['id']
                                       ],
                                           true) ?>">
                                        <?= __("Display Link") ?></a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <?=
                    $this->Upload->uploadImage($post['User'], 'User.photo', ['style' => 'medium_large'],
                        ['class' => 'feed-img']) ?>
                    <div class="font_14px font_bold font_verydark"><?= h($post['User']['display_username']) ?></div>
                    <?= $this->element('Feed/display_share_range', compact('post')) ?>
                </div>
                <?php if ($post['User']['id'] === $this->Session->read('Auth.User.id')): ?>
                    <div class="col col-xxs-12 p_0px">
                        <?= $this->element('Feed/post_edit_form', compact('post')) ?>
                    </div>
                <?php endif; ?>
                <?= $this->element('Feed/post_body', compact('post')) ?>
                <?php $photo_count = 0;
                //タイプ別に切り分け
                if ($post['Post']['type'] == Post::TYPE_ACTION) {
                    $model_name = 'ActionResult';
                } else {
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
                        <div id="ActionCarouselPost_<?= $post['Post']['id'] ?>" class="carousel slide"
                             data-ride="carousel">
                            <!-- Indicators -->
                            <?php if ($photo_count >= 2): ?>
                                <ol class="carousel-indicators">
                                    <?php $index = 0 ?>
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <?php if ($post[$model_name]["photo{$i}_file_name"]): ?>
                                            <li data-target="#ActionCarouselPost_<?= $post[$model_name]['id'] ?>"
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
                                               rel="lightbox"
                                               data-lightbox="ActionLightBoxPost_<?= $post['Post']['id'] ?>">
                                                <?=
                                                $this->Html->image('pre-load.svg',
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
                                <a class="left carousel-control" href="#ActionCarouselPost_<?= $post['Post']['id'] ?>"
                                   data-slide="prev">
                                    <span class="glyphicon glyphicon-chevron-left"></span>
                                </a>
                                <a class="right carousel-control" href="#ActionCarouselPost_<?= $post['Post']['id'] ?>"
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
                        <a href="<?= isset($site_info['url']) ? $site_info['url'] : null ?>" target="blank"
                           onclick="window.open(this.href,'_system');return false;"
                           class="no-line font_verydark">
                            <div class="site-info bd-radius_4px">
                                <div class="media">
                                    <div class="pull-left">
                                        <?=
                                        $this->Html->image('pre-load.svg',
                                            [
                                                'class'         => 'lazy media-object',
                                                'data-original' => $this->Upload->uploadUrl($post,
                                                    "Post.site_photo",
                                                    ['style' => 'small']),
                                                'width'         => '80px',
                                                'height'        => '80px',
                                                'error-img'     => "/img/no-image-link.png",
                                            ]
                                        )
                                        ?>
                                    </div>
                                    <div class="media-body">
                                        <h4 class="media-heading font_18px"><?= isset($site_info['title']) ? mb_strimwidth(h($site_info['title']),
                                                0,
                                                145,
                                                "&hellip;") : null ?></h4>

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
                <?php endif; ?>
                <?php if ($post['Post']['type'] == Post::TYPE_ACTION && isset($post['ActionResult']['KeyResult']['name'])): ?>
                    <div class="col col-xxs-12 pt_6px">
                        <i class="fa fa-key disp_i"></i>&nbsp;<?= h($post['ActionResult']['KeyResult']['name']) ?>
                    </div>
                <?php endif; ?>
                <?php if ($post['User']['id'] === $this->Session->read('Auth.User.id')): ?>
                    <div class="col col-xxs-12 p_0px">
                        <?= $this->element('Feed/post_edit_form', compact('post')) ?>
                    </div>
                <?php endif; ?>

                <div class="col col-xxs-12 font_12px pt_8px">
                    <a href="#" class="click-like font_lightgray <?= empty($post['MyPostLike']) ? null : "liked" ?>"
                       like_count_id="ActionPostLikeCount_<?= $post['Post']['id'] ?>"
                       model_id="<?= $post['Post']['id'] ?>"
                       like_type="post">
                        <?= __("Like!") ?></a>
                    <span class="font_lightgray"> ･ </span>
                    <span>
                            <a href="#"
                               data-url="<?= $this->Html->url([
                                   'controller' => 'posts',
                                   'action'     => 'ajax_get_post_liked_users',
                                   'post_id'    => $post['Post']['id']
                               ]) ?>"
                               class="modal-ajax-get font_lightgray">
                                <i class="fa fa-thumbs-o-up"></i>&nbsp;<span
                                    id="ActionPostLikeCount_<?= $post['Post']['id'] ?>"><?= $post['Post']['post_like_count'] ?></span>
                            </a><span class="font_lightgray"> ･ </span>
            <a href="#"
               data-url="<?= $this->Html->url([
                   'controller' => 'posts',
                   'action'     => 'ajax_get_post_red_users',
                   'post_id'    => $post['Post']['id']
               ]) ?>"
               class="modal-ajax-get font_lightgray"><i
                    class="fa fa-check"></i>&nbsp;<span><?= $post['Post']['post_read_count'] ?></span>
            </a>
            </span>

                </div>
            </div>
            <div class="panel-body ptb_8px plr_11px comment-block">
                <?php if ($post['Post']['comment_count'] > 3 && count($post['Comment']) == 3): ?>
                    <a href="#" class="btn-link click-comment-all"
                       id="ActionComments_<?= $post['Post']['id'] ?>"
                       parent-id="ActionComments_<?= $post['Post']['id'] ?>"
                       get-url="<?= $this->Html->url([
                           "controller" => "posts",
                           'action'     => 'ajax_get_old_comment',
                           'post_id'    => $post['Post']['id'],
                           $post['Post']['comment_count'] - 3
                       ]) ?>"
                    >
                        <i class="fa fa-comment-o"></i>&nbsp;<?=
                        __("View %s more comments",
                            $post['Post']['comment_count'] - 3) ?></a>
                <?php endif; ?>
                <?php foreach ($post['Comment'] as $comment): ?>
                    <?=
                    $this->element('Feed/comment',
                        [
                            'comment'      => $comment,
                            'comment_file' => $comment['CommentFile'],
                            'user'         => $comment['User'],
                            'like'         => $comment['MyCommentLike'],
                            'id_prefix'    => 'Action_',
                            'post_type'    => $post['type'],
                        ]) ?>
                <?php endforeach ?>
                <a href="#" class="btn btn-link click-comment-new"
                   id="ActionComments_new_<?= $post['Post']['id'] ?>"
                   style="display:none"
                   post-id="<?= $post['Post']['id'] ?>"
                   get-url="<?= $this->Html->url([
                       "controller" => "posts",
                       'action'     => 'ajax_get_latest_comment',
                       'post_id'    => $post['Post']['id']
                   ]) ?>"
                >
                    <div class="alert alert-danger new-comment-read">
                        <span class="num">0</span>
                        <?= __(" New comments.") ?>
                    </div>
                </a>

                <div class="new-comment-error" id="comment_error_<?= $post['Post']['id'] ?>">
                    <i class="fa fa-exclamation-circle"></i><span class="message"></span>
                </div>
                <div class="col-xxs-12 box-align feed-contents comment-contents">
                    <?=
                    $this->Html->image('pre-load.svg',
                        [
                            'class'         => 'lazy comment-img',
                            'data-original' => $this->Upload->uploadUrl($my_prof,
                                'User.photo',
                                ['style' => 'medium_large']),
                        ]
                    )
                    ?>
                    <div class="comment-body" id="ActionNewCommentForm_<?= $post['Post']['id'] ?>">
                        <form action="#" id="" method="post" accept-charset="utf-8">
                            <div class="form-group mlr_-1px">
                                <textarea
                                    class="form-control font_12px comment-post-form box-align not-autosize click-get-ajax-form-replace"
                                    replace-elm-parent-id="ActionNewCommentForm_<?= $post['Post']['id'] ?>"
                                    click-target-id="ActionCommentFormBody_<?= $post['Post']['id'] ?>"
                                    tmp-target-height="32"
                                    ajax-url="<?= $this->Html->url([
                                        'controller' => 'posts',
                                        'action'     => 'ajax_get_new_comment_form',
                                        'post_id'    => $post['Post']['id'],
                                        'Action'
                                    ]) ?>"
                                    wrap="soft" rows="1"
                                    placeholder="<?= __("Comment") ?>"
                                    cols="30"
                                    init-height="15"></textarea>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach ?>
    <?= $this->App->viewEndComment() ?>
<?php endif ?>
