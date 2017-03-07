<?php
/**
 * ドラッグ＆ドロップのファイルアップロード用フォーム
 * element が呼ばれる場所によっては、$(obj).show() でフォームが表示されてしまう可能性があるので
 * style に width: 0; height: 0; も入れておく
 */
?>
<?= $this->App->viewStartComment()?>
<?=
$this->Form->create('AttachedFile', [
    'url'   => '/api/v1/files/upload_image',
    'id'    => 'UploadFileForm',
    'type'  => 'file',
    'class' => 'upload-file-form',
    'style' => 'display:none; height:0px; width:0px;',
]); ?>
<span class="upload-file-form-message upload-file-form-content none"><i
        class="fa fa-cloud-upload upload-file-form-content none"></i></span>
<?= $this->Form->end() ?>
<div id="UploadFileAttachButton" style="display:none; height:0; width:0;"></div>
<?=
$this->Form->create('AttachedFile', [
    'url'   => ['controller' => 'posts', 'action' => 'ajax_remove_file'],
    'id'    => 'RemoveFileForm',
    'style' => 'display:none; height:0px; width:0px;',
]); ?>
<?= $this->Form->hidden('file_id') ?>
<?php $this->Form->unlockField('AttachedFile.file_id') ?>
<?= $this->Form->end() ?>
<?= $this->App->viewEndComment()?>
