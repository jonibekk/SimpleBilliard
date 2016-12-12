<?php
/**
 * TODO:全体的にHTML構成に問題があるので修正
 */
?>
<div class="posts-panel-body panel-body">
    <div class="col col-xxs-12 feed-user mb_8px">
        <div class="pull-right">
            <div class="dropdown">
                <a href="#" class="font_lightGray-gray font_11px" data-toggle="dropdown" id="download">
                    <i class="fa fa-chevron-down feed-arrow"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="download">
                    <?php if ($post['User']['id'] === $this->Session->read('Auth.User.id')): ?>
                        <li>
                            <a href="<?= $this->Html->url([
                                'controller'       => 'goals',
                                'action'           => 'edit_action',
                                'action_result_id' => $post['Post']['action_result_id']
                            ]) ?>"
                            ><?= __("Edit Action") ?></a>
                        </li>
                    <?php endif ?>
                    <?php if ($my_member_status['TeamMember']['admin_flg'] || $post['User']['id'] === $this->Session->read('Auth.User.id')): ?>
                        <li><?=
                            $this->Form->postLink(__("Delete the action"),
                                [
                                    'controller'       => 'goals',
                                    'action'           => 'delete_action',
                                    'action_result_id' => $post['Post']['action_result_id']
                                ],
                                null,
                                __("Do you really want to delete this action?")) ?></li>
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
    <?php
    /**
     * 画像のurlを集める
     * アクションの場合は１画像
     * 投稿の場合は全画像を集める
     */
    $imgs = [];
    $model_name = 'ActionResult';

    //アクションの場合は、ActionResultFileと旧ファイルの両方を集める
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
    ?>
    <div class="col col-xxs-12 feed-contents font_bold mb_4px">
        <i class="fa fa-key disp_i"></i>&nbsp;<?= h($post['ActionResult']['KeyResult']['name']) ?>
    </div>
</div>
<?php //TODO:.colが.rowの中になく単体で存在することはBootstrapの仕様としてありえない ?>
<div
    class="col col-xxs-12 <?= count($imgs) !== 1 ? "none post_gallery" : 'feed_img_only_one mb_4px' ?>">
    <?php foreach ($imgs as $v): ?>
        <a href="<?= $v['l'] ?>" rel='lightbox' data-lightbox="FeedLightBox_<?= $post['Post']['id'] ?>">
            <?= $this->Html->image($v['s']) ?>
        </a>
    <?php endforeach; ?>
</div>
<div class="panel-body">
    <?php if (!is_null($post['ActionResult']['key_result_target_value'])): ?>
        <div class="feed-progress">
            <i class="fa fa-tachometer"></i>
            <?php
            $changValue = $post['ActionResult']['key_result_change_value'];
            $displayChangeValue = "";
            if ($changValue > 0) {
                $displayChangeValue .= '+';
            } elseif ($changValue < 0) {
                $displayChangeValue .= '-';
            }
            $displayChangeValue .= AppUtil::formatBigFloat($post['ActionResult']['key_result_change_value']);
            $currentValue = (int)$post['ActionResult']['key_result_before_value'] + (int)$post['ActionResult']['key_result_change_value'];
            $currentValue = AppUtil::formatBigFloat($currentValue);
            ?>
            <?= $currentValue ?>
            / <span
                class="feed-progress-target"><?= AppUtil::formatBigFloat($post['ActionResult']['key_result_target_value']) ?></span>
            ( <span class="feed-progress-change"><?= $displayChangeValue ?></span> )
        </div>
    <?php endif; ?>
    <div
        class="col col-xxs-12 feed-contents post-contents mod-action showmore-action font_14px font_verydark box-align"
        id="PostTextBody_<?= $post['Post']['id'] ?>">
        <?php //チェックアイコンはすぐに戻す可能性があるのでコメントアウト ?>
        <!--        <i class="fa fa-check-circle disp_i"></i>&nbsp;-->
        <?= nl2br($this->TextEx->autoLink($post['ActionResult']['name'])) ?>
    </div>
    <?php if ($post['Post']['site_info']): ?>
        <?php $site_info = json_decode($post['Post']['site_info'], true) ?>
        <?= $this->element('Feed/site_info_block', [
            'site_info' => $site_info,
            'img_src'   => $this->Upload->uploadUrl($post, "Post.site_photo", ['style' => 'small']),
        ]) ?>
    <?php endif; ?>
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
