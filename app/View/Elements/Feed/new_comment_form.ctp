<?php
/**
 * Created by PhpStorm.
 * User: daikihirakata
 * Date: 2014/07/19
 * Time: 22:11
 *
 * @var                    $post_id
 * @var                    $prefix
 * @var CodeCompletionView $this
 */
?>
<?= $this->App->viewStartComment()?>
<?=
$this->Form->create('Comment', [
    'default'         => false,
    'url'             => ['controller' => 'posts', 'action' => 'ajax_add_comment'],
    'inputDefaults'   => [
        'div'       => 'form-group mlr_-1px',
        'label'     => false,
        'wrapInput' => '',
        'class'     => 'form-control'
    ],
    'id'              => "{$prefix}CommentAjaxGetNewCommentForm_{$post_id}",
    'class'           => 'form-feed-notify ajax-add-comment comment-form',
    'type'            => 'file',
    'novalidate'      => true,
    'error-msg-id'    => $prefix . 'CommentFormErrorMsg_' . $post_id,
    'submit-id'       => $prefix . 'CommentSubmit_' . $post_id,
    'first-form-id'   => $prefix . 'NewCommentForm_' . $post_id,
    'refresh-link-id' => $prefix . 'Comments_new_' . $post_id,
]); ?>
<?php $this->Form->unlockField('socket_id') ?>
<?=
$this->Form->input('body', [
    'id'                           => "{$prefix}CommentFormBody_{$post_id}",
    'label'                        => false,
    'type'                         => 'textarea',
    'wrap'                         => 'soft',
    'rows'                         => 1,
    'required'                     => true,
    'placeholder'                  => __("Comment"),
    'class'                        => 'form-control tiny-form-text font_12px comment-post-form box-align change-warning no-border',
    'target-id'                    => "{$prefix}CommentSubmit_{$post_id}",
    'data-bv-notempty-message'     => __("Input is required."),
    'data-bv-stringlength'         => 'true',
    'data-bv-stringlength-max'     => 5000,
    'data-bv-stringlength-message' => __("It's over limit characters (%s).", 5000),
])
?>
<?= $this->Form->hidden('site_info_url', ['id' => "CommentSiteInfoUrl_{$post_id}"]) ?>
<?php $this->Form->unlockField('Comment.site_info_url') ?>

<div id="<?= $prefix ?>CommentOgpSiteInfo_<?= $post_id ?>" class="comment-ogp-site-info"></div>
<div id="<?= $prefix ?>CommentUploadFilePreview_<?= $post_id ?>" class="comment-upload-file-preview"></div>
<?php $this->Form->unlockField('file_id') ?>

<?= $this->Form->hidden('post_id', ['value' => $post_id]) ?>
<div class="comment-btn" id="<?= $prefix ?>Comment_<?= $post_id ?>">
    <div>
        <a href="#" class="link-red new-comment-add-pic comment-file-attach-button"
           id="CommentUploadFileButton_<?= $post_id ?>"
           data-preview-container-id="CommentUploadFilePreview_<?= $post_id ?>"
           data-form-id="CommentAjaxGetNewCommentForm_<?= $post_id ?>">
            <button type="button" class="btn pull-left photo-up-btn"><i
                    class="fa fa-paperclip post-camera-icon"></i>
            </button>
        </a>
    </div>
    <div class="pull-left mt_12px font_brownRed"><span id="<?= $prefix ?>CommentFormErrorMsg_<?= $post_id ?>"></span>
    </div>
    <div class="pull-right">
        <?=
        $this->Form->submit(__("Comment"),
            ['class'    => 'btn btn-primary submit-btn comment-submit-button',
             'id'       => "{$prefix}CommentSubmit_{$post_id}",
             'disabled' => 'disabled'
            ]) ?>
    </div>
    <div class="clearfix"></div>
</div>
<?= $this->Form->end() ?>
<?= $this->App->viewEndComment()?>

