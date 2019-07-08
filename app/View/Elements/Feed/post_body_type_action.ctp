<?php
/**
 * TODO:全体的にHTML構成に問題があるので修正
 */
$kr = Hash::get($post, 'ActionResult.KeyResult');
?>
<?= $this->App->viewStartComment() ?>
<div class="posts-panel-body panel-body">
    <div class="col col-xxs-12 feed-user mb_8px">
        <div class="pull-right">
            <?php if (!empty($enable_translation) && in_array($post['Post']['type'], [1, 3])) { ?>
                <?php $styleTranslationDisabled = $post['Post']['translation_limit_reached'] ? " disabled" : "" ?>
                <?php if ($post['Post']['translation_limit_reached'] || !empty($post['Post']['translation_languages'])) { ?>
                    <i class="icon-translation material-icons md-16 click-translation<?=$styleTranslationDisabled?>" model_id="<?= $post['Post']['id'] ?>" content_type="3">g_translate</i>
                <?php } ?>
            <?php } ?>
            <div class="dropdown inline-block">
                <a href="#" class="font_lightGray-gray font_14px" data-toggle="dropdown" id="download">
                    <i class="fa fa-ellipsis-v feed-arrow"></i>
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
</div>
<?php //TODO:.colが.rowの中になく単体で存在することはBootstrapの仕様としてありえない ?>
<div
    class="col col-xxs-12 <?= count($imgs) !== 1 ? "none post_gallery" : 'feed_img_only_one mb_16px' ?>">
    <?php foreach ($imgs as $v): ?>
        <a href="<?= $v['l'] ?>" rel='lightbox' data-lightbox="FeedLightBox_<?= $post['Post']['id'] ?>">
            <?= $this->Html->image($v['s']) ?>
        </a>
    <?php endforeach; ?>
</div>
<div class="panel-body posts-panel-body">
    <?php if (!empty($kr)): ?>
        <div class="col col-xxs-12 feed-contents font_bold">
            <i class="fa fa-key disp_i"></i>&nbsp;<?= h($kr['name']) ?>
        </div>
        <?php if (!is_null(Hash::get($post, 'ActionResult.KrProgressLog.target_value'))): ?>
            <div class="feed-progress">
                <i class="fa fa-tachometer"></i>
                <?php
                $changeValue = $post['ActionResult']['KrProgressLog']['change_value'];
                $displayChangeValue = "";
                if ($changeValue >= 0) {
                    $displayChangeValue .= '+';
                }
                $unitId = $post['ActionResult']['KrProgressLog']['value_unit'];
                $displayChangeValue .= AppUtil::formatThousand($changeValue);

                $currentValue = bcadd($post['ActionResult']['KrProgressLog']['before_value'], $changeValue, 3);

                $currentValue = $this->NumberEx->addUnit(AppUtil::formatThousand($currentValue), $unitId);
                $targetValue = $this->NumberEx->addUnit(AppUtil::formatThousand($post['ActionResult']['KrProgressLog']['target_value']),
                    $unitId);
                ?>
                <?php if ($unitId == KeyResult::UNIT_BINARY): ?>
                    <?php if ($changeValue == 0): ?>
                        <?= __('Incomplete') ?>
                    <?php else: ?>
                        <span class="feed-progress-strong">
                            <?= __('Complete') ?>
                        </span>
                    <?php endif; ?>
                <?php else: ?>
                    <span class="feed-progress-strong">
                    <?= $currentValue ?>
                    &nbsp;(
                    <?php if ($displayChangeValue === '+0'): ?>
                        </span><?= $displayChangeValue ?><span class="feed-progress-strong">
                    <?php else: ?>
                        <span class="feed-progress-change"><?= $displayChangeValue ?></span>
                    <?php endif; ?>
                    )&nbsp;
                    </span>
                    / <?= $targetValue ?>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
    <div
        class="<?= viaIsSet($long_text) ? null : "showmore-action" ?> col col-xxs-12 feed-contents post-contents mod-action font_14px font_verydark box-align"
        id="PostTextBody_<?= $post['Post']['id'] ?>">
        <?php //チェックアイコンはすぐに戻す可能性があるのでコメントアウト ?>
        <!--        <i class="fa fa-check-circle disp_i"></i>&nbsp;-->
        <?= nl2br($this->TextEx->autoLink($post['ActionResult']['name'])) ?>
    </div>
    <div id="PostTextBodyMemory_<?= $post['Post']['id'] ?>" style="display: none;"></div>
    <div class="dropdown inline-block" id="TranslationDropDown_<?= $post['Post']['id'] ?>" style="display: none;">
        <div href="#" class="drop-down-translation" data-toggle="dropdown">
            <?= __("Change language") ?><i class="fa fa-sort-down drop-down-translation-icon"></i>
        </div>
        <ul class="dropdown-menu" aria-labelledby="download">
            <?php foreach ($post['Post']['translation_languages'] ?? [] as $tl) { ?>
            <li class="click-translation-other" model_id="<?= $post['Post']['id'] ?>" content_type="3" language="<?= $tl['language'] ?>"><a href="#"><?= $tl['intl_name'] ?> (<?= $tl['local_name'] ?>)</a></li>
            <?php } ?>
        </ul>
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
            <?php if (in_array(Hash::get($post, 'Post.type'), [Post::TYPE_NORMAL, Post::TYPE_ACTION])): ?>
            <?php $isSavedItemClass = Hash::get($post, 'Post.is_saved_item') ? 'mod-on' : 'mod-off'; ?>
            <i class="post-saveItem <?= $isSavedItemClass ?> js-save-item" aria-hidden="true"
            data-id="<?= Hash::get($post, 'Post.id') ?>"
            data-is-saved-item="<?= Hash::get($post, 'Post.is_saved_item') ?>">
                <span><?= __("Save<!-- 0 -->") ?></span>
            </i>
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
            'post'  => $post
        ]) ?>
    <?php endif; ?>
</div>
<?= $this->App->viewEndComment() ?>
