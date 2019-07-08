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
 * @var                    $id_prefix
 * @var CodeCompletionView $this
 * @var                    $long_text
 * @var                    $comment_file
 */
?>
<?php if (!isset($id_prefix)) {
    $id_prefix = null;
}
?>
<?= $this->App->viewStartComment() ?>
<div id="CommentOgpBackup_<?= $comment['id'] ?>" class="always-hidden" data-text=""></div>
<div class="font_12px comment-box" comment-id="<?= $comment['id'] ?>">
    <div class="col col-xxs-12 pt_8px pb_8px">
        <a href="<?= $this->Html->url([
            'controller' => 'users',
            'action'     => 'view_goals',
            'user_id'    => $user['id']
        ]) ?>">
            <?=
            $this->Html->image('pre-load.svg',
                [
                    'class'         => 'lazy comment-img',
                    'data-original' => $this->Upload->uploadUrl($user, 'User.photo',
                        ['style' => 'medium_large']),
                ]
            )
            ?>
        </a>

        <div class="comment-body">
            <div class="col comment-user">
                <?php if ($user['id'] === $this->Session->read('Auth.User.id')): ?>
                    <div id="dropdown_<?= $comment['id'] ?>" class="dropdown dropdown-comment pull-right">
                        <a href="#" class="font_lightGray-gray font_11px" data-toggle="dropdown" id="download">
                            <i class="fa fa-ellipsis-v fa-lg comment-arrow"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="download">
                            <li><a href="#" class="target-toggle-click"
                                   target-id="<?= $id_prefix ?>CommentEditForm_<?= $comment['id'] ?>"
                                   opend-text="<?= __("Stop editing") ?>"
                                   closed-text="<?= __("Edit comment") ?>"
                                   ajax-url="<?= $this->Html->url([
                                       'controller' => 'posts',
                                       'action'     => 'ajax_get_edit_comment_form',
                                       'comment_id' => $comment['id'],
                                       $id_prefix
                                   ]) ?>"
                                   click-target-id="<?= $id_prefix ?>CommentEditFormBody_<?= $comment['id'] ?>"
                                   hidden-target-id="<?= $id_prefix ?>CommentTextBody_<?= $comment['id'] ?>"
                                   id="CommentEditButton_<?= $comment['id'] ?>"
                                ><?= __("Edit comment") ?></a>
                            </li>
                            <li>
                                <a href="#" class="js-click-comment-delete" comment-id="<?= $comment['id'] ?>"><?= __("Delete comment") ?></a>
                            </li>
                        </ul>
                    </div>
                <?php elseif ($my_member_status['TeamMember']['admin_flg']): ?>
                    <div class="pull-right develop--link-gray">
                        <?=
                        $this->Form->postLink('<i class="fa fa-times comment-cross"></i>',
                            ['controller' => 'posts', 'action' => 'comment_delete', 'comment_id' => $comment['id']],
                            ['escape' => false], __("Do you really want to delete this comment?")) ?>
                    </div>
                <?php endif; ?>
                <div class="mb_2px lh_12px">
                    <a class="font_bold font_verydark"
                       href="<?= $this->Html->url([
                           'controller' => 'users',
                           'action'     => 'view_goals',
                           'user_id'    => $user['id']
                       ]) ?>">
                        <?= h($user['display_username']) ?>
                    </a>
                </div>
            </div>
            <div
                <?php $mentions = $this->Mention->getMyMentions($comment['body'], $my_id, $my_team_id) ?>
                class="col <?= h($long_text) ? "showmore-comment-circle" : "showmore-comment" ?> comment-text feed-contents comment-contents font_verydark box-align"
                id="<?= $id_prefix ?>CommentTextBody_<?= $comment['id'] ?>"><?= 
                    $this->Mention->replaceMention(nl2br($this->TextEx->autoLink($comment['body'])), $mentions)
                ?>
                </div>
                <div id="CommentTextBodyMemory_<?= $comment['id'] ?>" style="display: none;"></div>
                <div class="dropdown inline-block" id="TranslationCommentDropDown_<?= $comment['id'] ?>" style="display: none;">
                    <div href="#" class="drop-down-translation" data-toggle="dropdown">
                        <?= __("Change language") ?><i class="fa fa-sort-down drop-down-translation-icon"></i>
                    </div>
                    <ul class="dropdown-menu" aria-labelledby="download">
                    <?php foreach ($comment['translation_languages'] ?? [] as $tl) { ?>
                        <li class="click-translation-other" model_id="<?= $comment['id'] ?>" content_type="2" language="<?= $tl['language'] ?>"><a href="#"><?= $tl['intl_name'] ?> (<?= $tl['local_name'] ?>)</a></li>
                    <?php } ?>
                    </ul>
                </div>
            <?php
            /**
             * 画像のurlを集める
             */
            $imgs = [];
            if (!empty($comment_file)) {
                foreach ($comment_file as $post_file) {
                    if (isset($post_file['AttachedFile']['id']) && $post_file['AttachedFile']['file_type'] == AttachedFile::TYPE_FILE_IMG) {
                        $img = [];
                        $img['l'] = $this->Upload->uploadUrl($post_file['AttachedFile'], "AttachedFile.attached",
                            ['style' => 'large']);
                        $img['s'] = $this->Upload->uploadUrl($post_file['AttachedFile'], "AttachedFile.attached",
                            ['style' => 'small']);
                        $imgs[] = $img;
                    }
                }
            }
            for ($i = 1; $i <= 5; $i++) {
                if ($comment["photo{$i}_file_name"]) {
                    $img = [];
                    $img['l'] = $this->Upload->uploadUrl($comment, "Comment.photo" . $i,
                        ['style' => 'large']);
                    $img['s'] = $this->Upload->uploadUrl($comment, "Comment.photo" . $i,
                        ['style' => 'small']);
                    $imgs[] = $img;
                }
            }
            ?>
            <?php if (!empty($imgs)): ?>
                <div
                    class="col col-xxs-12 pt_10px <?= count($imgs) !== 1 ? "none comment_gallery" : 'feed_img_only_one mb_12px' ?>">
                    <?php foreach ($imgs as $v): ?>
                        <a href="<?= $v['l'] ?>" rel='lightbox'
                           data-lightbox="FeedCommentLightBox_<?= $comment['id'] ?>">
                            <?= $this->Html->image($v['s']) ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <?php if (!empty($comment['id'])): ?>
            <?php endif; ?>
            <?php if ($comment['site_info']): ?>
                <?php $site_info = json_decode($comment['site_info'], true) ?>
                <?= $this->element('Feed/site_info_block', [
                    'site_info'              => $site_info,
                    'title_max_length'       => 50,
                    'description_max_length' => 110,
                    'comment_id'             => $comment['id'],
                    'img_src'                => $this->Upload->uploadUrl($comment, "Comment.site_photo",
                        ['style' => 'small']),
                ]) ?>
            <?php endif; ?>
            <div class="col col-xxs-12 pt_10px">
                <?php foreach ($comment_file as $file): ?>
                    <?php if ($file['AttachedFile']['file_type'] == AttachedFile::TYPE_FILE_IMG) {
                        continue;
                    } ?>
                    <div class="panel panel-default file-wrap-on-post">
                        <div class="panel-body pt_10px plr_11px pb_8px">
                            <?= $this->element('Feed/attached_file_item',
                                [
                                    'data'       => $file,
                                    'page_type'  => 'feed',
                                    'post_id'    => $comment['post_id'],
                                    'comment_id' => $comment['id']
                                ]) ?>
                        </div>
                    </div>
                <?php endforeach ?>
            </div>
            <div class="lh_15px">
                <?= $this->TimeEx->elapsedTime(h($comment['created'])) ?><span class="font_lightgray"> ･ </span>
                <a href="#" class="click-like font_lightgray <?= empty($like) ? null : "liked" ?>"
                   like_count_id="<?= $id_prefix ?>CommentLikeCount_<?= $comment['id'] ?>"
                   model_id="<?= $comment['id'] ?>"
                   like_type="comment">
                    <?= __("Like!") ?></a><span
                    class="font_lightgray"> ･ </span>
                <span>
                            <a href="#"
                               data-url="<?= $this->Html->url([
                                   'controller' => 'posts',
                                   'action'     => 'ajax_get_comment_liked_users',
                                   'comment_id' => $comment['id']
                               ]) ?>"
                               class="modal-ajax-get font_lightgray">
                                <i class="fa fa-thumbs-o-up"></i>&nbsp;<span
                                    id="<?= $id_prefix ?>CommentLikeCount_<?= $comment['id'] ?>"><?= $comment['comment_like_count'] ?></span></a><span
                        class="font_lightgray"> ･ </span>
            <a href="#"
               data-url="<?= $this->Html->url([
                   'controller' => 'posts',
                   'action'     => 'ajax_get_comment_red_users',
                   'comment_id' => $comment['id']
               ]) ?>"
               class="modal-ajax-get font_lightgray"><i
                    class="fa fa-check"></i>&nbsp;<span><?= $comment['comment_read_count'] ?></span></a>
            <?php if (!empty($enable_translation) && in_array($post_type, [Post::TYPE_NORMAL, Post::TYPE_ACTION])) { ?>
                <?php if ($comment['translation_limit_reached'] || !empty($comment['translation_languages'])) { ?>
                <?php $styleTranslationDisabled = $comment['translation_limit_reached'] ? " disabled" : "" ?>
                ･ <i class="icon-translation material-icons md-12 click-translation<?=$styleTranslationDisabled?>" model_id="<?= $comment['id'] ?>" content_type="2">g_translate</i>
                <?php } ?>
            <?php } ?>
            </span>
            </div>
        </div>
    </div>
</div>
<?= $this->App->viewEndComment() ?>