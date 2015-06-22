<?php
/**
 * @var CodeCompletionView $this
 */
?>
<!-- START app/View/Elements/modal_privacy_policy.ctp -->
<div class="modal fade" id="modal-pp">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close font_33px close-design" data-dismiss="modal" aria-hidden="true">
                    <span class="close-icon">&times;</span></button>
                <h4 class="modal-title"><?= __d('gl', "プライバシーポリシー") ?></h4>
            </div>
            <div class="modal-body">
                <?= $this->element('privacy_policy', ['no_title' => true]) ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= __d('gl', "閉じる") ?></button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- END app/View/Elements/modal_privacy_policy.ctp -->
