<?
/**
 * @var $this View
 * @var $no_title
 */
?>
<!-- START app/View/Elements/modal_tos.ctp -->
<!-- modal tos -->
<div class="modal fade" id="modal-tos">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close font-size_33 close-design" data-dismiss="modal" aria-hidden="true">
                    <span class="close-icon">&times;</span></button>
                <h4 class="modal-title"><?= __d('gl', "利用規約") ?></h4>
            </div>
            <div class="modal-body">
                <?= $this->element('tos', ['no_title' => true]) ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= __d('gl', "閉じる") ?></button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- /modal tos-->
<!-- END app/View/Elements/modal_tos.ctp -->
