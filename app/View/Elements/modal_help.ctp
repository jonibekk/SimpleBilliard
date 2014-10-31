<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 6/6/14
 * Time: 3:19 PM
 *
 * @var View $this
 * @var      $help_item
 */
?>
<!-- START app/View/Elements/modal_help.ctp -->
<div class="modal-dialog modal-mini">
    <div class="modal-content parent-p_0px">
        <div class="modal-header">
            <button type="button" class="close font_33px close-design" data-dismiss="modal" aria-hidden="true">
                <span class="close-icon">&times;</span></button>
            <h4 class="modal-title"><?= $help_item['title'] ?></h4>
        </div>
        <div class="modal-body">
            <? if ($help_item['picture_name']): ?>
                <div class="col col-xxs-12">
                    <?= $this->Html->image($help_item['picture_name'],
                                           ['width' => '280', 'height' => '', 'class' => 'm_pic']) ?>
                </div>
            <? endif; ?>
            <? if ($help_item['body']): ?>
                <div class="col col-xxs-12">
                    <?= $help_item['body'] ?>
                </div>
            <? endif; ?>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?= __d('gl', "閉じる") ?></button>
        </div>
    </div>
</div>
<!-- END app/View/Elements/modal_help.ctp -->
