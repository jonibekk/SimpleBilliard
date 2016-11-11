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
 * @var                    $goal
 * @var                    $long_text
 * @var                    $without_header
 * @var                    $without_add_comment
 * @var                    $current_term
 */
$without_header = isset($without_header) ? $without_header : false;
$without_add_comment = isset($without_add_comment) ? $without_add_comment : false;
?>
<?php if (!empty($posts)): ?>
    <?= $this->App->viewStartComment() ?>
    <?php foreach ($posts as $post_key => $post): ?>
        <div class="panel panel-default">
            <?php if (!$without_header && (isset($post['Goal']['id']) && $post['Goal']['id']) || isset($post['Circle']['id'])): ?>
                <!--START Goal Post Header -->

                <?php if (isset($post['Goal']['id']) && $post['Goal']['id']): ?>
                    <div class="post-heading-goal-area panel-body pt_10px plr_11px pb_8px bd-b">
                        <div class="col col-xxs-12">
                            <div class="post-heading-goal-wrapper pull-left">
                                <a href="<?= $this->Html->url([
                                    'controller' => 'goals',
                                    'action'     => 'ajax_get_goal_description_modal',
                                    'goal_id'    => $post['Goal']['id']
                                ]) ?>"
                                   class="post-heading-goal
                                    no-line font_verydark modal-ajax-get">
                                    <p class="post-heading-goal-title">
                                        <i class="fa fa-flag font_gray"></i>
                                        <span><?= h($post['Goal']['name']) ?></span>
                                    </p>
                                </a>
                            </div>

                            <div class="pull-right">
                                <a href="<?= $this->Html->url([
                                    'controller' => 'goals',
                                    'action'     => 'ajax_get_goal_description_modal',
                                    'goal_id'    => $post['Goal']['id']
                                ]) ?>"
                                   class="no-line font_verydark modal-ajax-get">
                                    <?=
                                    $this->Html->image('ajax-loader.gif',
                                        [
                                            'class'         => 'post-heading-goal-avatar  lazy media-object',
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
                        </div>

                    </div>

                <?php elseif (isset($post['Circle']['id'])): ?>
                    <div class="post-heading-circle-area panel-body pt_10px plr_11px pb_8px bd-b">
                        <div class="col col-xxs-12">
                            <div class="post-heading-circle-wrapper pull-left">
                                <a href="<?= $this->Html->url([
                                    'controller' => 'posts',
                                    'action'     => 'feed',
                                    'circle_id'  => $post['Circle']['id']
                                ]) ?>"
                                   class="post-heading-cirlce no-line font_verydark">
                                    <p class="post-heading-circle-title">
                                        <i class="fa fa-circle-o font_gray"></i>
                                        <span><?= h($post['Circle']['name']) ?></span>
                                    </p>
                                </a>
                            </div>
                            <div class="pull-right">
                                <a href="<?= $this->Html->url([
                                    'controller' => 'posts',
                                    'action'     => 'feed',
                                    'circle_id'  => $post['Circle']['id']
                                ]) ?>"
                                   class="no-line font_verydark">
                                    <?=
                                    $this->Html->image('ajax-loader.gif',
                                        [
                                            'class'         => 'post-heading-circle-avatar lazy media-object',
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
                        </div>
                    </div>
                <?php endif; ?>


                <!--END Goal Post Header -->

            <?php endif; ?>
            <div class="posts-panel-body panel-body">
                <div class="col col-xxs-12 feed-user">
                    <div class="pull-right">
                        <div class="dropdown">
                            <a href="#" class="font_lightGray-gray font_11px" data-toggle="dropdown" id="download">
                                <i class="fa fa-chevron-down feed-arrow"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="download">
                                <?php if ($post['User']['id'] === $this->Session->read('Auth.User.id')): ?>
                                    <?php if ($post['Post']['type'] == Post::TYPE_NORMAL): ?>
                                        <li>
                                            <a href="<?= $this->Html->url([
                                                'controller' => 'posts',
                                                'action'     => 'post_edit',
                                                'post_id'    => $post['Post']['id']
                                            ]) ?>"
                                            ><?= __("Edit post") ?></a>
                                        </li>
                                    <?php elseif ($post['Post']['type'] == Post::TYPE_ACTION): ?>
                                        <li>
                                            <a href="<?= $this->Html->url([
                                                'controller'       => 'goals',
                                                'action'           => 'edit_action',
                                                'action_result_id' => $post['Post']['action_result_id']
                                            ]) ?>"
                                            ><?= __("Edit Action") ?></a>
                                        </li>
                                    <?php endif; ?>
                                <?php endif ?>
                                <?php if ($my_member_status['TeamMember']['admin_flg'] || $post['User']['id'] === $this->Session->read('Auth.User.id')): ?>
                                    <?php if ($post['Post']['type'] == Post::TYPE_ACTION): ?>
                                        <li><?=
                                            $this->Form->postLink(__("Delete the action"),
                                                [
                                                    'controller'       => 'goals',
                                                    'action'           => 'delete_action',
                                                    'action_result_id' => $post['Post']['action_result_id']
                                                ],
                                                null,
                                                __("Do you really want to delete this action?")) ?></li>
                                    <?php else: ?>
                                        <li><?=
                                            $this->Form->postLink(__("Delete post"),
                                                [
                                                    'controller' => 'posts',
                                                    'action'     => 'post_delete',
                                                    'post_id'    => $post['Post']['id']
                                                ],
                                                null,
                                                __("Do you really want to delete this post?")) ?></li>
                                    <?php endif; ?>
                                <?php endif ?>
                                <li>
                                    <a href="#" class="copy_me"
                                       onclick="copyToClipboard('<?=
                                       $this->Html->url([
                                           'controller' => 'posts',
                                           'action'     => 'feed',
                                           'post_id'    => $post['Post']['id']
                                       ],
                                           true) ?>'); return false;">
                                        <?= __("Copy Link") ?></a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <a href="<?= $this->Html->url([
                        'controller' => 'users',
                        'action'     => 'view_goals',
                        'user_id'    => $post['User']['id']
                    ]) ?>">
                        <?=
                        $this->Html->image('ajax-loader.gif',
                            [
                                'class'         => 'lazy feed-img',
                                'data-original' => $this->Upload->uploadUrl($post['User'], 'User.photo',
                                    ['style' => 'medium']),
                            ]
                        )
                        ?>
                        <span class="font_14px font_bold font_verydark">
                            <?= h($post['User']['display_username']) ?>
                        </span>
                    </a>
                    <?= $this->element('Feed/display_share_range', compact('post')) ?>
                </div>
                <?= $this->element('Feed/post_body', compact('post')) ?>
                <?
                /**
                 * 画像のurlを集める
                 * アクションの場合は１画像
                 * 投稿の場合は全画像を集める
                 */
                $imgs = [];
                //タイプ別に切り分け
                if ($post['Post']['type'] == Post::TYPE_ACTION) {
                    $model_name = 'ActionResult';
                } else {
                    $model_name = 'Post';
                }

                //アクションの場合は、ActionResultFileと旧ファイルの両方を集める
                if ($post['Post']['type'] == Post::TYPE_ACTION) {
                    //新ファイルが存在するか確認
                    if ($ar_img = Hash::get($post, 'ActionResult.ActionResultFile.0')) {
                        $img = [];
                        $img['l'] = $this->Upload->attachedFileUrl($ar_img, "preview");
                        $img['s'] = $this->Upload->uploadUrl($ar_img, "AttachedFile.attached",
                            ['style' => 'small']);
                        $imgs[] = $img;
                    } else {
                        //新ファイルが無ければ旧ファイルを確認
                        for ($i = 1; $i <= 5; $i++) {
                            if ($post[$model_name]["photo{$i}_file_name"]) {
                                $img = [];
                                $img['l'] = $this->Upload->uploadUrl($post, "{$model_name}.photo" . $i,
                                    ['style' => 'large']);
                                $img['s'] = $this->Upload->uploadUrl($post, "{$model_name}.photo" . $i,
                                    ['style' => 'small']);
                                $imgs[] = $img;
                            }
                        }
                    }
                } //アクション以外の場合は、新ファイル、旧ファイルの両方から集める
                else {
                    if (!empty($post['PostFile'])) {
                        foreach ($post['PostFile'] as $post_file) {
                            if (isset($post_file['AttachedFile']['id']) && $post_file['AttachedFile']['file_type'] == AttachedFile::TYPE_FILE_IMG) {
                                $img = [];
                                $img['l'] = $this->Upload->uploadUrl($post_file['AttachedFile'],
                                    "AttachedFile.attached",
                                    ['style' => 'large']);
                                $img['s'] = $this->Upload->uploadUrl($post_file['AttachedFile'],
                                    "AttachedFile.attached",
                                    ['style' => 'small']);
                                $imgs[] = $img;
                            }
                        }
                    }
                    for ($i = 1; $i <= 5; $i++) {
                        if ($post[$model_name]["photo{$i}_file_name"]) {
                            $img = [];
                            $img['l'] = $this->Upload->uploadUrl($post, "{$model_name}.photo" . $i,
                                ['style' => 'large']);
                            $img['s'] = $this->Upload->uploadUrl($post, "{$model_name}.photo" . $i,
                                ['style' => 'small']);
                            $imgs[] = $img;
                        }
                    }

                }

                ?>
                <?php if (!empty($imgs)): ?>
            </div>
            <div
                class="col col-xxs-12 pt_10px <?= count($imgs) !== 1 ? "none post_gallery" : 'feed_img_only_one mb_12px' ?>">
                <?php foreach ($imgs as $v): ?>
                    <a href="<?= $v['l'] ?>" rel='lightbox' data-lightbox="FeedLightBox_<?= $post['Post']['id'] ?>">
                        <?= $this->Html->image($v['s']) ?>
                    </a>
                <?php endforeach; ?>
            </div>
            <div class="panel-body pt_10px plr_11px pb_8px">

                <?php endif; ?>


                <?php if ($post['Post']['site_info']): ?>
                    <?php $site_info = json_decode($post['Post']['site_info'], true) ?>
                    <?= $this->element('Feed/site_info_block', [
                        'site_info' => $site_info,
                        'img_src'   => $this->Upload->uploadUrl($post, "Post.site_photo", ['style' => 'small']),
                    ]) ?>
                <?php elseif ($post['Post']['type'] == Post::TYPE_CREATE_GOAL && isset($post['Goal']['id']) && $post['Goal']['id']): ?>
                    <?= $this->element('Feed/goal_sharing_block', compact('post')) ?>
                <?php elseif ($post['Post']['type'] == Post::TYPE_CREATE_CIRCLE && isset($post['Circle']['id']) && $post['Circle']['id']): ?>
                    <div class="col col-xxs-12 pt_10px">
                        <a href="<?= $this->Html->url([
                            'controller' => 'posts',
                            'action'     => 'feed',
                            'circle_id'  => $post['Circle']['id']
                        ]) ?>"
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
                <?php if ($post['Post']['type'] == Post::TYPE_ACTION): ?>
                    <div class="col col-xxs-12 pt_10px">
                        <?php foreach ($post['ActionResult']['ActionResultFile'] as $k => $file): ?>
                            <?php if ($k === 0) {
                                continue;
                            } ?>
                            <div class="panel panel-default file-wrap-on-post">
                                <div class="panel-body pt_10px plr_11px pb_8px">
                                    <?= $this->element('Feed/attached_file_item',
                                        ['data' => $file, 'page_type' => 'feed', 'post_id' => $post['Post']['id']]) ?>
                                </div>
                            </div>
                        <?php endforeach ?>
                    </div>
                <?php else: ?>
                    <div class="col col-xxs-12 pt_10px">
                        <?php foreach ($post['PostFile'] as $file): ?>
                            <?php if ($file['AttachedFile']['file_type'] == AttachedFile::TYPE_FILE_IMG) {
                                continue;
                            } ?>
                            <div class="panel panel-default file-wrap-on-post">
                                <div class="panel-body pt_10px plr_11px pb_8px">
                                    <?= $this->element('Feed/attached_file_item',
                                        ['data' => $file, 'page_type' => 'feed', 'post_id' => $post['Post']['id']]) ?>
                                </div>
                            </div>
                        <?php endforeach ?>
                    </div>
                    <?php if ($this->Post->isDisplayableGoalButtons($post['Post'], $post['Goal'], $current_term)) : ?>
                        <?php $follow_opt = $this->Goal->getFollowOption($post['Goal']) ?>
                        <?php $collabo_opt = $this->Goal->getCollaboOption($post['Goal']) ?>
                        <div class="col col-xxs-12 mt_5px p_5px">
                            <div class="col col-xxs-6 col-xs-4 mr_5px">
                                <a goal-id="<?= $post['Goal']['id'] ?>" data-class="toggle-follow" href="#"
                                   class="btn btn-white font_verydark bd-circle_22px toggle-follow p_8px <?= h($follow_opt['class']) ?>"
                                <?= h($follow_opt['disabled']) ?>="<?= h($follow_opt['disabled']) ?>">
                                <i class="fa fa-heart font_rougeOrange" style="<?= h($follow_opt['style']) ?>"></i>
                                <span class="ml_5px"><?= h($follow_opt['text']) ?></span>
                                </a>
                            </div>
                            <div class="col col-xxs-5 col-xs-4">
                                <a href="<?= $this->Html->url([
                                    'controller' => 'goals',
                                    'action'     => 'ajax_get_collabo_change_modal',
                                    'goal_id'    => $post['Goal']['id']
                                ]) ?>"
                                   data-target="#ModalCollabo_<?= $post['Goal']['id'] ?>" data-toggle="modal"
                                   class="btn btn-white bd-circle_22px font_verydark collaborate-button modal-ajax-get-collabo p_8px <?= h($collabo_opt['class']) ?>">
                                    <i style="" class="fa fa-child font_rougeOrange font_18px"></i>
                                    <span class="ml_5px font_14px"><?= h($collabo_opt['text']) ?></span>
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
                <div class="col col-xxs-12 feeds-post-btns-area">
                    <div class="feeds-post-btns-wrap-left">
                        <a href="#"
                           class="click-like feeds-post-like-btn <?= empty($post['MyPostLike']) ? null : "liked" ?>"
                           like_count_id="PostLikeCount_<?= $post['Post']['id'] ?>"
                           model_id="<?= $post['Post']['id'] ?>"
                           like_type="post">
                            <i class="fa-thumbs-up fa"></i>
                            <?= __("Like!") ?></a>
                        <?php if (!$without_add_comment): ?>
                            <a href="#" class="feeds-post-comment-btn trigger-click"
                               target-id="NewCommentDummyForm_<?= $post['Post']['id'] ?>"
                               after-replace-target-id="CommentFormBody_<?= $post['Post']['id'] ?>"
                            >
                                <i class="fa-comments-o fa"></i>
                                <?= __("Comments") ?>
                            </a>
                        <?php endif; ?>
                    </div>
                    <div class="feeds-post-btns-wrap-right">
                        <a href="<?= $this->Html->url([
                            'controller' => 'posts',
                            'action'     => 'ajax_get_post_liked_users',
                            'post_id'    => $post['Post']['id']
                        ]) ?>"
                           class="modal-ajax-get feeds-post-btn-numbers-like">
                            <i class="fa fa-thumbs-o-up"></i>&nbsp;
                            <span id="PostLikeCount_<?= $post['Post']['id'] ?>">
                                <?= $post['Post']['post_like_count'] ?>
                            </span>
                        </a>
                        <a href="<?= $this->Html->url([
                            'controller' => 'posts',
                            'action'     => 'ajax_get_post_red_users',
                            'post_id'    => $post['Post']['id']
                        ]) ?>"
                           class="modal-ajax-get feeds-post-btn-numbers-read">
                            <i class="fa fa-check"></i>
                            <span>
                               <?= $post['Post']['post_read_count'] ?>
                           </span>
                        </a>
                    </div>
                </div>
            </div>
            <div class="panel-body ptb_8px plr_11px comment-block"
                 id="CommentBlock_<?= $post['Post']['id'] ?>"
                 data-preview-container-id="CommentUploadFilePreview_<?= $post['Post']['id'] ?>"
                 data-form-id="CommentAjaxGetNewCommentForm_<?= $post['Post']['id'] ?>">
                <?php if ($post['Post']['comment_count'] > 3 && count($post['Comment']) == 3): ?>
                    <a href="#" class="btn-link click-comment-all"
                       id="Comments_<?= $post['Post']['id'] ?>"
                       parent-id="Comments_<?= $post['Post']['id'] ?>"
                       get-url="<?= $this->Html->url([
                           "controller" => "posts",
                           'action'     => 'ajax_get_old_comment',
                           'post_id'    => $post['Post']['id'],
                           $post['Post']['comment_count'] - 3,
                           'long_text'  => $long_text
                       ]) ?>"
                    >
                        <?php if ($post['unread_count'] > 0): ?>
                            <i class="fa fa-comment-o font_brownRed"></i>&nbsp;<?=
                            __("View %s more comments",
                                $post['Post']['comment_count'] - 3) ?>
                            <?=
                            __("(%s)",
                                $post['unread_count']) ?>

                        <?php else: ?>
                            <span class="font_gray">
                            <i class="fa fa-comment-o font_brownRed"></i>&nbsp;<?=
                                __("View %s comments",
                                    $post['Post']['comment_count'] - 3) ?>
                            </span>
                        <?php endif; ?>
                    </a>
                <?php endif; ?>

                <?php foreach ($post['Comment'] as $comment): ?>
                    <?=
                    $this->element('Feed/comment',
                        [
                            'comment'      => $comment,
                            'comment_file' => $comment['CommentFile'],
                            'user'         => $comment['User'],
                            'like'         => $comment['MyCommentLike']
                        ]) ?>
                <?php endforeach ?>

                <a href="#" class="btn-link click-comment-new"
                   id="Comments_new_<?= $post['Post']['id'] ?>"
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
                <?php if (!$without_add_comment): ?>
                    <div class="col-xxs-12 box-align feed-contents comment-contents">
                        <?=
                        $this->Html->image('ajax-loader.gif',
                            [
                                'class'         => 'lazy comment-img',
                                'data-original' => $this->Upload->uploadUrl($my_prof,
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
                                    post-id="<?= $post['Post']['id'] ?>"
                                    tmp-target-height="32"
                                    ajax-url="<?= $this->Html->url([
                                        'controller' => 'posts',
                                        'action'     => 'ajax_get_new_comment_form',
                                        'post_id'    => $post['Post']['id']
                                    ]) ?>"
                                    wrap="soft" rows="1"
                                    placeholder="<?= __("Comment") ?>"
                                    cols="30"
                                    id="NewCommentDummyForm_<?= $post['Post']['id'] ?>"
                                    init-height="15"></textarea>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach ?>
    <?= $this->element('file_upload_form') ?>
    <?= $this->App->viewEndComment() ?>
<?php endif ?>
