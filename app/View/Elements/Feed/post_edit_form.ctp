<?php
/**
 * Created by PhpStorm.
 * User: daikihirakata
 * Date: 2014/07/21
 * Time: 3:12
 *
 * @var $post
 */
?>
<?=
$this->Form->create('Post', [
    'url'           => ['controller' => 'posts', 'action' => 'post_edit', $post['Post']['id']],
    'inputDefaults' => [
        'div'       => 'form-group',
        'label'     => false,
        'wrapInput' => '',
        'class'     => 'form-control'
    ],
    'class'         => '',
    'style'         => 'display: none',
    'novalidate'    => true,
    'id' => "PostEditForm_{$post['Post']['id']}",
]); ?>
<?=
$this->Form->input('body', [
    'id'             => "PostEditFormBody_{$post['Post']['id']}",
    'label'          => false,
    'type'           => 'textarea',
    'rows'           => 1,
    'class'          => 'form-control tiny-form-text blank-disable',
    'target_show_id' => "PostEdit_{$post['Post']['id']}",
    'target-id'      => "PostEditSubmit_{$post['Post']['id']}",
    'value'          => $post['Post']['body'],
])
?>
    <div class="" style="display: none" id="PostEdit_<?= $post['Post']['id'] ?>">
        <?=
        $this->Form->submit(__d('gl', "変更を保存する"),
                            ['class' => 'btn btn-primary pull-right', 'id' => "PostEditSubmit_{$post['Post']['id']}", 'disabled' => 'disabled']) ?>
        <div class="clearfix"></div>
    </div>
<?= $this->Form->end() ?>