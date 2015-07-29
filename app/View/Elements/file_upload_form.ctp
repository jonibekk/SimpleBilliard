<?php
/**
 */
?>
<!-- START app/View/Elements/file_upload_form.ctp -->
<?=
$this->Form->create('AttachedFile', [
    'url'   => ['controller' => 'posts', 'action' => 'ajax_upload_file'],
    'id'    => 'UploadFileForm',
    'type'  => 'file',
    'class' => 'upload-file-form',
    'style' => 'display:none',
]); ?>
<?= $this->Form->end() ?>
<button id="UploadFileAttachButton" style="display:none"></button>
<?=
$this->Form->create('AttachedFile', [
    'url'   => ['controller' => 'posts', 'action' => 'ajax_remove_file'],
    'id'    => 'RemoveFileForm',
    'style' => 'display:none',
]); ?>
<?= $this->Form->hidden('file_id') ?>
<?php $this->Form->unlockField('file_id') ?>
<?= $this->Form->end() ?>
<!-- END app/View/Elements/file_upload_form.ctp -->