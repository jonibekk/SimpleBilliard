<?php
/**
 * Created by PhpStorm.
 * User: daikihirakata
 * Date: 2014/07/21
 * Time: 3:12
 *
 * @var $comment
 */
?>
<!-- START app/View/Elements/Feed/comment_edit_form.ctp -->
<?=
$this->Form->create('Comment', [
    'url'           => ['controller' => 'posts', 'action' => 'comment_edit', $comment['id']],
    'inputDefaults' => [
        'div'       => 'form-group',
        'label'     => false,
        'wrapInput' => '',
        'class'     => 'form-control'
    ],
    'class'         => '',
    'style'         => 'display: none',
    'novalidate'    => true,
    'type'          => 'file',
    'id'            => "CommentEditForm_{$comment['id']}",
]); ?>
<?=
$this->Form->input('body', [
    'id'             => "CommentEditFormBody_{$comment['id']}",
    'label'          => false,
    'type'           => 'textarea',
    'rows'           => 1,
    'class' => 'form-control tiny-form-text blank-disable font-size_12 edit-form comment-edit-form',
    'target_show_id' => "CommentEdit_{$comment['id']}",
    'target-id'      => "CommentEditSubmit_{$comment['id']}",
    'value'          => $comment['body'],
])
?>
<div class="form-group" id="CommentEditFormImage_<?= $comment['id'] ?>" style="display: none">
    <ul class="gl-input-images">
        <? for ($i = 1; $i <= 5; $i++): ?>
            <li>
                <?=
                $this->element('Feed/photo_upload',
                               ['data' => ['Comment' => $comment], 'type' => 'comment', 'index' => $i, 'submit_id' => "CommentEditSubmit_{$comment['id']}"]) ?>
            </li>
        <? endfor ?>
    </ul>
</div>

<div class="" style="display: none" id="CommentEdit_<?= $comment['id'] ?>">
    <a href="#" class="target-show-this-del" target-id="CommentEditFormImage_<?= $comment['id'] ?>">
        <button type="button" class="btn pull-left photo-up-btn" data-toggle="tooltip"
                data-placement="bottom"
                title="画像を追加する"><i class="fa fa-camera post-camera-icon"></i>
        </button>

    </a>
    <?=
    $this->Form->submit(__d('gl', "変更を保存する"),
                        ['class' => 'btn btn-primary pull-right submit-comment-edit', 'id' => "CommentEditSubmit_{$comment['id']}", 'disabled' => 'disabled']) ?>
    <div class="clearfix"></div>
</div>
<?= $this->Form->end() ?>
<!-- END app/View/Elements/Feed/comment_edit_form.ctp -->
