<?php
/**
 * Created by PhpStorm.
 * User: daikihirakata
 * Date: 2014/07/21
 * Time: 3:12
 *
 * @var CodeCompletionView $this
 * @var                    $comment
 * @var                    $id_prefix
 */
?>
<?php if (!isset($id_prefix)) {
    $id_prefix = null;
}
?>
<?= $this->App->viewStartComment()?>
<?=
$this->Form->create('Comment', [
    'url'           => ['controller' => 'posts', 'action' => 'ajax_comment_edit', 'comment_id' => $comment['id']],
    'inputDefaults' => [
        'div'       => 'form-group',
        'label'     => false,
        'wrapInput' => '',
        'class'     => 'form-control'
    ],
    'class'         => 'col col-xxs-12',
    'style'         => 'display: none',
    'novalidate'    => true,
    'type'          => 'file',
    'id'            => $id_prefix . "CommentEditForm_{$comment['id']}",
]); ?>
<div class="m_-1px">
    <?=
    $this->Form->input('body', [
        'id'                           => $id_prefix . "CommentEditFormBody_{$comment['id']}",
        'label'                        => false,
        'type'                         => 'textarea',
        'wrap'                         => 'soft',
        'rows'                         => 1,
        'class'                        => 'form-control tiny-form-text font_12px edit-form comment-edit-form',
        'target_show_id'               => $id_prefix . "CommentEdit_{$comment['id']}",
        'target-id'                    => $id_prefix . "CommentEditSubmit_{$comment['id']}",
        'value'                        => $comment['body'],
        'data-bv-stringlength'         => 'true',
        'data-bv-stringlength-max'     => 5000,
        'data-bv-stringlength-message' => __("It's over limit characters (%s).", 5000),
    ])
    ?>
</div>
<div class="form-group none" id="<?= $id_prefix ?>CommentEditFormImage_<?= $comment['id'] ?>">
    <ul class="input-images">
        <?php for ($i = 1; $i <= 5; $i++): ?>
            <li>
                <?=
                $this->element('Feed/photo_upload',
                    ['data'      => ['Comment' => $comment],
                     'type'      => 'comment',
                     'index'     => $i,
                     'id_prefix' => $id_prefix,
                     'submit_id' => $id_prefix . "CommentEditSubmit_{$comment['id']}"
                    ]) ?>
            </li>
        <?php endfor ?>
    </ul>
    <span class="help-block" id="Comment_<?= $comment['id'] ?>_Photo_ValidateMessage"></span>
</div>

<div class="none" id="<?= $id_prefix ?>CommentEdit_<?= $comment['id'] ?>">
    <?=
    $this->Form->submit(__("Save changes"),
        ['class'    => 'btn btn-primary pull-right submit-comment-edit',
         'id'       => $id_prefix . "CommentEditSubmit_{$comment['id']}",
         'disabled' => 'disabled'
        ]) ?>
    <div class="clearfix"></div>
</div>
<?= $this->Form->end() ?>
<?= $this->App->viewEndComment()?>
