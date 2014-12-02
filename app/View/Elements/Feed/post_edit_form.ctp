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
<!-- START app/View/Elements/Feed/post_edit_form.ctp -->
<?=
$this->Form->create('Post', [
    'url'           => ['controller' => 'posts', 'action' => 'post_edit', $post['Post']['id']],
    'inputDefaults' => [
        'div'       => 'form-group',
        'label'     => false,
        'wrapInput' => '',
        'class'     => 'form-control'
    ],
    'class' => 'pt_10px',
    'style'         => 'display: none',
    'novalidate'    => true,
    'type'          => 'file',
    'id'            => "PostEditForm_{$post['Post']['id']}",
]); ?>
<div class="mlr_-1px">
<?=
$this->Form->input('body', [
    'id'             => "PostEditFormBody_{$post['Post']['id']}",
    'label'          => false,
    'type'           => 'textarea',
    'wrap'           => 'soft',
    'rows'           => 1,
    'class' => 'form-control tiny-form-text blank-disable edit-form post-edit-form box-align',
    'target_show_id' => "PostEdit_{$post['Post']['id']}",
    'target-id'      => "PostEditSubmit_{$post['Post']['id']}",
    'value'          => $post['Post']['body'],
])
?>
</div>
<div class="row form-group m_0px" id="PostFormImage_<?= $post['Post']['id'] ?>" style="display: none">
    <ul class="col input-images">
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
    <a href="#" class="target-show-this-del font_12px" target-id="PostFormImage_<?= $post['Post']['id'] ?>">
        <button type="button" class="btn pull-left photo-up-btn" data-toggle="tooltip"
                data-placement="bottom"
                title="画像を追加する"><i class="fa fa-camera post-camera-icon"></i>
        </button>

    </a>

    <?=
    $this->Form->submit(__d('gl', "変更を保存する"),
                        ['class' => 'btn btn-primary pull-right submit-post-edit', 'id' => "PostEditSubmit_{$post['Post']['id']}", 'disabled' => 'disabled']) ?>
    <div class="clearfix"></div>
</div>
<?= $this->Form->end() ?>
<!-- END app/View/Elements/Feed/post_edit_form.ctp -->
