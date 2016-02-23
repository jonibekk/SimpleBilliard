<?php
/**
 * Created by PhpStorm.
 * User: daikihirakata
 * Date: 2014/07/19
 * Time: 22:11
 *
 * @var                    $krs
 * @var CodeCompletionView $this
 */
?>
<!-- START app/View/Elements/Goal/modal_related_kr_list.ctp -->
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close font_33px close-design" data-dismiss="modal" aria-hidden="true"><span
                    class="close-icon">&times;</span></button>
            <h4 class="modal-title"><?= __("完了済のキーリザルト一覧") ?></h4>
        </div>
        <div class="modal-body modal-feed-body">
            <div class="row">
                <?php foreach ($krs as $kr): ?>
                    <div class="col col-xxs-12">
                        <div class="font_12px font_bold modalFeedTextPadding">
                            <?= h($kr['name']) ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="modal-footer modal-feed-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?= __("閉じる") ?></button>
        </div>
    </div>
</div>
<!-- END app/View/Elements/Goal/modal_related_kr_list.ctp -->

