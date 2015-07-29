<?php
/**
 * ドラッグ＆ドロップのファイルアップロード用フォーム
 *
 * element が呼ばれる場所によっては、$(obj).show() でフォームが表示されてしまう可能性があるので
 * style に width: 0px; height: 0px; も入れておく
 */
?>
<!-- START app/View/Elements/file_upload_form.ctp -->
<?=
$this->Form->create('AttachedFile', [
    'url'   => ['controller' => 'posts', 'action' => 'ajax_upload_file'],
    'id'    => 'UploadFileForm',
    'type'  => 'file',
    'class' => 'upload-file-form',
    'style' => 'display:none; height:0px; width:0px;',
]); ?>
<?= $this->Form->end() ?>
<div id="UploadFileAttachButton" style="display:none; height:0px; width:0px;"></div>
<?=
$this->Form->create('AttachedFile', [
    'url'   => ['controller' => 'posts', 'action' => 'ajax_remove_file'],
    'id'    => 'RemoveFileForm',
    'style' => 'display:none; height:0px; width:0px;',
]); ?>
<?= $this->Form->hidden('file_id') ?>
<?php $this->Form->unlockField('AttachedFile.file_id') ?>
<?= $this->Form->end() ?>
<!-- END app/View/Elements/file_upload_form.ctp -->
