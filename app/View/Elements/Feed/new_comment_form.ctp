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
<!-- START app/View/Elements/Feed/new_comment_form.ctp -->
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
    'class'           => 'form-feed-notify ajax-add-comment',
    'type'            => 'file',
    'novalidate'      => true,
    'error-msg-id'    => $prefix . 'CommentFormErrorMsg_' . $post_id,
    'submit-id'       => $prefix . 'CommentSubmit_' . $post_id,
    'first-form-id'   => $prefix . 'NewCommentForm_' . $post_id,
    'refresh-link-id' => $prefix . 'Comments_new_' . $post_id,
]); ?>
<? $this->Form->unlockField('socket_id') ?>
<?=
$this->Form->input('body', [
    'id'          => "{$prefix}CommentFormBody_{$post_id}",
    'label'       => false,
    'type'        => 'textarea',
    'wrap'        => 'soft',
    'rows'        => 1,
    'required'    => true,
    'placeholder' => __d('gl', "コメントする"),
    'class'       => 'form-control tiny-form-text blank-disable font_12px comment-post-form box-align',
    'target-id'   => "{$prefix}CommentSubmit_{$post_id}",
    'required'    => 'false'
])
?>
<div class="form-group" id="<?= $prefix ?>CommentFormImage_<?= $post_id ?>"
     style="display: none">
    <ul class="input-images">
        <? for ($i = 1; $i <= 5; $i++): ?>
            <li>
                <?=
                $this->element('Feed/photo_upload',
                               ['type' => 'comment', 'index' => $i, 'submit_id' => "{$prefix}CommentSubmit_{$post_id}", 'post_id' => $post_id]) ?>
            </li>
        <? endfor ?>
    </ul>
    <span class="help-block" id="Comment__Post_<?= $post_id ?>_Photo_ValidateMessage"></span>
</div>
<?= $this->Form->hidden('post_id', ['value' => $post_id]) ?>
<div class="comment-btn" id="<?= $prefix ?>Comment_<?= $post_id ?>">
    <div>
        <a href="#" class="target-show-target-click comment-add-pic new-comment-add-pic"
           target-id="<?= $prefix ?>CommentFormImage_<?= $post_id ?>"
           click-target-id="<?= $prefix ?>Comment__Post_<?= $post_id ?>_Photo_1">
            <button type="button" class="btn pull-left photo-up-btn">
                <i class="fa fa-camera post-camera-icon"></i>
            </button>
        </a>
    </div>
    <div class="pull-left mt_12px font_brownRed"><span id="<?= $prefix ?>CommentFormErrorMsg_<?= $post_id ?>"></span></div>
    <div class="pull-right">
        <?=
        $this->Form->submit(__d('gl', "コメントする"),
                            ['class' => 'btn btn-primary submit-btn', 'id' => "{$prefix}CommentSubmit_{$post_id}", 'disabled' => 'disabled']) ?>
    </div>
    <div class="clearfix"></div>
</div>
<?= $this->Form->end() ?>
<!-- END app/View/Elements/Feed/new_comment_form.ctp -->

