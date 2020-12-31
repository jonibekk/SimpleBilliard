<?php
/**
 * Created by PhpStorm.
 * User: fujiharam
 * Date: 2018/03/09
 * Time: 17:05
 */ ?>

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
    <div class="comment-body" id="NewCommentForm_<?= $post['Post']['id'] ?>">
        <?=
        $this->Form->create('Comment', [
            'default'         => false,
            'url'             => '/api/v1/comments/',
            'inputDefaults'   => [
                'div'       => 'form-group mlr_-1px',
                'label'     => false,
                'wrapInput' => '',
                'class'     => 'form-control'
            ],
            'id'              => "CommentAjaxGetNewCommentForm_{$post['Post']['id']}",
            'class'           => 'form-feed-notify ajax-add-comment comment-form',
            'type'            => 'file',
            'post-id'         => $post['Post']['id'],
            'error-msg-id'    => 'CommentFormErrorMsg_' . $post['Post']['id'],
            'submit-id'       => 'CommentSubmit_' . $post['Post']['id'],
            'first-form-id'   => 'NewCommentForm_' . $post['Post']['id'],
            'refresh-link-id' => 'Comments_new_' . $post['Post']['id'],
        ]); ?>
        <div class="form-group mlr_-1px">
            <?php
            $toggleClass = isset($post['Post']['can_comment']) && '0' == $post['Post']['can_comment'] ? 'click-cannot-comment-modal-toggle' : 'click-get-ajax-form-toggle';
            echo $this->Form->input('body', [
                'id'          => "CommentFormBody_{$post['Post']['id']}",
                'label'       => false,
                'type'        => 'textarea',
                'wrap'        => 'soft',
                'rows'        => 1,
                'required'    => true,
                'has-mention' => true,
                'post-id'     => $post['Post']['id'],
                'placeholder' => __("Comment"),
                'class'       => 'form-control font_12px comment-post-form box-align '.$toggleClass,
                'target-id'   => "CommentSubmit_{$post['Post']['id']}",
                'maxlength'   => 5000,
            ])
            ?>
        </div>
        <?= $this->Form->hidden('site_info_url',
            ['id' => "CommentSiteInfoUrl_{$post['Post']['id']}"]) ?>
        <div id="CommentOgpSiteInfo_<?= $post['Post']['id'] ?>"
             class="comment-ogp-site-info"></div>
        <div id="CommentUploadFilePreview_<?= $post['Post']['id'] ?>"
             class="comment-upload-file-preview"></div>
        <?= $this->Form->hidden('post_id', ['value' => $post['Post']['id']]) ?>

        <div class="comment-btn" id="Comment_<?= $post['Post']['id'] ?>">
            <div>
                <a href="#" class="link-red new-comment-add-pic comment-file-attach-button"
                   id="CommentUploadFileButton_<?= $post['Post']['id'] ?>"
                   data-preview-container-id="CommentUploadFilePreview_<?= $post['Post']['id'] ?>"
                   data-form-id="CommentAjaxGetNewCommentForm_<?= $post['Post']['id'] ?>">
                    <button type="button" class="btn pull-left btn-photo-up"><i
                            class="fa fa-paperclip post-camera-icon"></i></button>
                </a>
            </div>
            <div class="pull-left mt_12px font_brownRed"><span
                    id="CommentFormErrorMsg_<?= $post['Post']['id'] ?>"></span>
            </div>
            <div class="pull-right">
                <?=
                $this->Form->submit(__("Comment"),
                    [
                        'class' => 'btn btn-primary submit-btn comment-submit-button',
                        'id'    => "CommentSubmit_{$post['Post']['id']}",
                    ]) ?>
            </div>
            <div class="clearfix"></div>
        </div>
        <?= $this->Form->end() ?>
    </div>
</div>
