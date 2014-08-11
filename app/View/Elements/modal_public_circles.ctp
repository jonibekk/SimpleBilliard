<?php
/**
 * Created by PhpStorm.
 * User: daikihirakata
 * Date: 2014/07/19
 * Time: 22:11
 *
 * @var                    $circles
 * @var CodeCompletionView $this
 */
?>
<!-- START app/View/Elements/modal_public_circles.ctp -->
<div class="modal-dialog">
    <div class="modal-content modalFeed-content">
        <div class="modal-header modalFeed-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h4 class="modal-title font-size_18 font-weight_bold"><?= __d('gl', "公開サークル") ?></h4>
        </div>
        <div class="modal-body modalFeed-body">
            <? if (!empty($circles)): ?>
                <div class="row borderBottom">
                    <? foreach ($circles as $circle): ?>
                        <?=
                        $this->element('public_circle_item', ['circle' => $circle]) ?>
                    <? endforeach ?>
                </div>
            <? else: ?>
                <?= __d('gl', "公開サークルはありません。") ?>
            <?endif ?>
        </div>
        <div class="modal-footer modalFeed-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?= __d('gl', "閉じる") ?></button>
        </div>
    </div>
</div>
<!-- END app/View/Elements/modal_public_circles.ctp -->
