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
    'id' => "CommentEditForm_{$comment['id']}",
]); ?>
<?=
$this->Form->input('body', [
    'id'             => "CommentEditFormBody_{$comment['id']}",
    'label'          => false,
    'type'           => 'textarea',
    'rows'           => 1,
    'class'          => 'form-control tiny-form-text blank-disable',
    'target_show_id' => "CommentEdit_{$comment['id']}",
    'target-id'      => "CommentEditSubmit_{$comment['id']}",
    'value'          => $comment['body'],
])
?>
    <div class="" style="display: none" id="CommentEdit_<?= $comment['id'] ?>">
        <?=
        $this->Form->submit(__d('gl', "変更を保存する"),
                            ['class' => 'btn btn-primary pull-right', 'id' => "CommentEditSubmit_{$comment['id']}", 'disabled' => 'disabled']) ?>
        <div class="clearfix"></div>
    </div>
<?= $this->Form->end() ?>