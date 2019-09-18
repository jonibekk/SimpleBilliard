<?php
$without_header = isset($without_header) ? $without_header : false;
$without_add_comment = isset($without_add_comment) ? $without_add_comment : false;
?>
<?= $this->element('Feed/post_drafts', compact('post_drafts')) ?>
<?php if (!empty($posts)): ?>
    <?= $this->App->viewStartComment() ?>
    <?php foreach ($posts as $post_key => $post): ?>
        <div class="panel panel-default">
            <?php if ($post['Post']['type'] != Post::TYPE_ACTION && !$without_header && (isset($post['Goal']['id']) && $post['Goal']['id']) || isset($post['Circle']['id'])): ?>
                <!--START Goal Post Header -->

                <?php if (isset($post['Goal']['id']) && $post['Goal']['id'] && $post['Post']['type'] != Post::TYPE_CREATE_GOAL): ?>
                    <div class="post-heading-goal-area panel-body pt_10px plr_11px pb_8px bd-b">
                        <div class="col">
                            <div class="post-heading-goal-wrapper pull-left">
                                <a href="<?= $this->Html->url([
                                    'controller' => 'goals',
                                    'action'     => 'view_krs',
                                    'goal_id'    => $post['Goal']['id']
                                ]) ?>"
                                   data-url="<?= $this->Html->url([
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
                                    'action'     => 'view_krs',
                                    'goal_id'    => $post['Goal']['id']
                                ]) ?>"
                                   data-url="<?= $this->Html->url([
                                       'controller' => 'goals',
                                       'action'     => 'ajax_get_goal_description_modal',
                                       'goal_id'    => $post['Goal']['id']
                                   ]) ?>"
                                   class="no-line font_verydark modal-ajax-get">
                                    <?=
                                    $this->Html->image('pre-load.svg',
                                        [
                                            'class'         => 'post-heading-goal-avatar  lazy media-object',
                                            'data-original' => $this->Upload->uploadUrl($post,
                                                "Goal.photo",
                                                ['style' => 'small']),
                                            'width'         => '32px',
                                            'height'        => '32px',
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
                        <div class="col">
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
                                    $this->Html->image('pre-load.svg',
                                        [
                                            'class'         => 'post-heading-circle-avatar lazy media-object',
                                            'data-original' => $this->Upload->uploadUrl($post,
                                                "Circle.photo",
                                                ['style' => 'small']),
                                            'width'         => '32px',
                                            'height'        => '32px',
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

            <?php if ($post['Post']['type'] == Post::TYPE_ACTION): ?>
                <?= $this->element('Feed/post_body_type_action', compact('post', 'without_add_comment')) ?>
            <?php else: ?>

                <div class="posts-panel-body panel-body">
                <div class="col feed-user">
                    <div class="pull-right">
                        <?php if (!empty($enable_translation) && in_array($post['Post']['type'], [Post::TYPE_NORMAL, Post::TYPE_ACTION])) { ?>
                            <?php $styleTranslationDisabled = !empty($post['Post']['translation_limit_reached']) ? " disabled" : "" ?>
                            <?php if (!empty($post['Post']['translation_limit_reached']) || !empty($post['Post']['translation_languages'])) { ?>
                                <i class="icon-translation material-icons md-16 click-translation<?=$styleTranslationDisabled?>" model_id="<?= $post['Post']['id'] ?>" content_type="1">g_translate</i>
                            <?php } ?>
                        <?php } ?>
                        <div class="dropdown inline-block">
                            <a href="#" class="font_lightGray-gray" data-toggle="dropdown" id="download">
                                <i class="fa fa-ellipsis-v feed-arrow"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="download">
                                <?php if ($post['User']['id'] === $this->Session->read('Auth.User.id')): ?>
                                    <?php if ($post['Post']['type'] == Post::TYPE_NORMAL): ?>
                                        <li>
                                            <a href="<?= UrlUtil::fqdnFrontEnd() ?>/posts/<?= $post['Post']['id'] ?>?edit=1"
                                            ><?= __("Edit post") ?></a>
                                        </li>
                                    <?php endif; ?>
                                <?php endif ?>
                                <?php if ($my_member_status['TeamMember']['admin_flg'] || $post['User']['id'] === $this->Session->read('Auth.User.id')): ?>
                                    <li><?=
                                        $this->Form->postLink(__("Delete post"),
                                            [
                                                'controller' => 'posts',
                                                'action'     => 'post_delete',
                                                'post_id'    => $post['Post']['id']
                                            ],
                                            null,
                                            __("Do you really want to delete this post?")) ?></li>
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
                                        <?= __("Display Link") ?></a>
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
                        $this->Html->image('pre-load.svg',
                            [
                                'class'         => 'lazy feed-img',
                                'data-original' => $this->Upload->uploadUrl($post['User'], 'User.photo',
                                    ['style' => 'medium_large']),
                            ]
                        )
                        ?>
                        <span class="font_14px font_bold font_verydark">
                            <?= h($post['User']['display_username']) ?>
                        </span>
                    </a>
                    <?= $this->element('Feed/display_share_range', compact('post')) ?>
                </div>
                <?= $this->element('Feed/post_content', compact('post')) ?>
                <div class="dropdown inline-block" id="TranslationDropDown_<?= $post['Post']['id'] ?>" style="display: none;">
                    <div href="#" class="drop-down-translation" data-toggle="dropdown">
                        <?= __("Change language") ?><i class="fa fa-sort-down drop-down-translation-icon"></i>
                    </div>
                    <ul class="dropdown-menu" aria-labelledby="download">
                    <?php foreach ($post['Post']['translation_languages'] ?? [] as $tl) { ?>
                        <li class="click-translation-other" model_id="<?= $post['Post']['id'] ?>" content_type="1" language="<?= $tl['language'] ?>"><a href="#"><?= $tl['intl_name'] ?> - <?= $tl['local_name'] ?></a></li>
                    <?php } ?>
                        <li><a href="/users/settings"><?= __("Change default") ?></a></li>
                    </ul>
                </div>
                <?php
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

                ?>
                <?php if (!empty($imgs) || !empty($post['PostResources'])): ?>
                    </div>
                    <?php if (
                        // Not going to show image if we have video posted
                        // https://confluence.goalous.com/display/GOAL/Video+post+technical+info#Videoposttechnicalinfo-Uploadinglimitation
                        !empty($imgs)
                        && !($post['hasVideoResource'])
                    ): ?>
                        <div
                                class="col pt_10px <?= count($imgs) !== 1 ? "none post_gallery" : 'feed_img_only_one mb_12px' ?>">
                            <?php foreach ($imgs as $v): ?>
                                <a href="<?= $v['l'] ?>" rel='lightbox'
                                   data-lightbox="FeedLightBox_<?= $post['Post']['id'] ?>">
                                    <?= $this->Html->image($v['s']) ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($post['PostResources'])): ?>
                        <?php foreach ($post['PostResources'] as $resource): ?>
                            <?php if ($resource['resource_type'] === 1): ?>
                                <div class="col pt_10px feed_img_only_one mb_12px">
                                    <?php
                                    // TODO: currently, we have only video resource https://jira.goalous.com/browse/GL-6601
                                    $videoStreamId = sprintf('video_stream_%d_%d_%d', $resource['id'], $post['Post']['id'],
                                        time());
                                    if ($resource['aspect_ratio'] > 0) {
                                        $paddingTop = 100 / $resource['aspect_ratio'];
                                    } else {
                                        $paddingTop = 100;
                                    }
                                    $paddingTop = ($paddingTop > 100) ? 100 : $paddingTop;
                                    ?>
                                    <div id="div<?= $videoStreamId ?>" class="video-responsive-container"
                                         style="padding-top: <?= $paddingTop ?>%">
                                        <video id="<?= $videoStreamId ?>"
                                               class="video-js vjs-default-skin vjs-big-play-centered video-responsive"
                                               controls playsinline preload="none"
                                               poster="<?= $resource["thumbnail"] ?>">
                                            <?php foreach ($resource['video_sources'] as $videoSource/** @var VideoSource $videoSource */): ?>
                                                <source src="/api/v1/video_streams/<?= $resource['id'] ?>/source?type=<?= $videoSource->getType()
                                                    ->getValue() ?>"
                                                        type="<?= $videoSource->getType()->getValue() ?>">
                                            <?php endforeach; ?>
                                        </video>
                                    </div>
                                    <script>feedVideoJs('<?= $videoStreamId ?>')</script>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <div class="panel-body posts-panel-body pt_10px plr_11px pb_8px">

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
                    <div class="col pt_10px">
                        <a href="<?= "/circles/" . $post['Circle']['id'] ."/posts"
                        ?>"
                           class="no-line font_verydark">
                            <div class="site-info bd-radius_4px">
                                <div class="media">
                                    <div class="pull-left">
                                        <?=
                                        $this->Html->image('pre-load.svg',
                                            [
                                                'class'         => 'lazy media-object',
                                                'data-original' => $this->Upload->uploadUrl($post,
                                                    "Circle.photo",
                                                    ['style' => 'medium_large']),
                                                'width'         => '80px',
                                                'height'        => '80px',
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
                <div class="col pt_10px">
                    <?php foreach ($post['PostFile'] as $file): ?>
                        <?php if ($file['AttachedFile']['file_type'] == AttachedFile::TYPE_FILE_IMG) {
                            // Showing image as attached file if post has video post resource
                            // https://confluence.goalous.com/display/GOAL/Video+post+technical+info#Videoposttechnicalinfo-Uploadinglimitation
                            if (!$post['hasVideoResource']) {
                                continue;
                            }
                        } ?>
                        <div class="panel panel-default file-wrap-on-post">
                            <div class="panel-body pt_10px plr_11px pb_8px">
                                <?= $this->element('Feed/attached_file_item',
                                    ['data' => $file, 'page_type' => 'feed', 'post_id' => $post['Post']['id']]) ?>
                            </div>
                        </div>
                    <?php endforeach ?>
                </div>
                <div class="col feeds-post-btns-area">
                    <div class="feeds-post-btns-wrap-left">
                        <a href="#"
                           class="click-like feeds-post-like-btn <?= empty($post['MyPostLike']) ? null : "liked" ?>"
                           like_count_id="PostLikeCount_<?= $post['Post']['id'] ?>"
                           model_id="<?= $post['Post']['id'] ?>"
                           like_type="post">
                            <i class="fa-thumbs-up fa"></i>
                            <?= __("Like!") ?></a>
                        <?php if (in_array(Hash::get($post, 'Post.type'), [Post::TYPE_NORMAL, Post::TYPE_ACTION])): ?>
                        <?php $isSavedItemClass = Hash::get($post, 'Post.is_saved_item') ? ' feeds-post-saved' : ''; ?>
                        <a href="#"
                           class="post-saveItem js-save-item<?= $isSavedItemClass ?>"
                           data-id="<?= Hash::get($post, 'Post.id') ?>"
                           data-is-saved-item="<?= Hash::get($post, 'Post.is_saved_item') ?>">
                            <i aria-hidden="true"></i>
                            <span><?= __("Save<!-- 0 -->") ?></span>
                        </a>
                        <?php endif; ?>
                    </div>
                    <div class="feeds-post-btns-wrap-right">
                        <a href="#"
                           data-url="<?= $this->Html->url([
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
                        <a href="#"
                           data-url="<?= $this->Html->url([
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
                                'like'         => $comment['MyCommentLike'],
                                'post_type'    => $post['Post']['type'],
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
                        <?= $this->element('Feed/comment_form', [
                            'post' => $post
                        ]) ?>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    <?php endforeach ?>
    <?= $this->element('file_upload_form') ?>
    <?= $this->App->viewEndComment() ?>
<?php endif ?>
