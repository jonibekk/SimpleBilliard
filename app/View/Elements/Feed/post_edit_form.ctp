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
    'type'          => 'file',
    'id'            => "PostEditForm_{$post['Post']['id']}",
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
    <div class="row form-group gl-no-margin" id="PostFormImage_<?= $post['Post']['id'] ?>" style="display: none">
        <ul class="col gl-input-images">
            <? for ($i = 1; $i <= 5; $i++): ?>
                <li>
                    <?=
                    $this->element('Feed/photo_upload',
                                   ['type' => 'post', 'index' => $i, 'data' => $post, 'submit_id' => "PostEditSubmit_{$post['Post']['id']}"]) ?>
                </li>
            <? endfor ?>
        </ul>
    </div>

    <div class="" style="display: none" id="PostEdit_<?= $post['Post']['id'] ?>">
        <a href="#" class="target-show-this-del" target-id="PostFormImage_<?= $post['Post']['id'] ?>">
            <i class="fa fa-file-o"></i>&nbsp;<?= __d('gl', "添付画像を変更する") ?>
        </a>

        <?=
        $this->Form->submit(__d('gl', "変更を保存する"),
                            ['class' => 'btn btn-primary pull-right', 'id' => "PostEditSubmit_{$post['Post']['id']}", 'disabled' => 'disabled']) ?>
        <div class="clearfix"></div>
    </div>
<?= $this->Form->end() ?>