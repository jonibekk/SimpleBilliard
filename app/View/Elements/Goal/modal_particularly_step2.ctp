<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 6/6/14
 * Time: 3:19 PM
 *
 * @var View $this
 */
?>
<!-- START app/View/Elements/Goal/modal_particularly_step2.ctp -->
<div class="modal-dialog">
    <div class="modal-content gl-modal-no-margin">
        <div class="modal-header">
            <button type="button" class="close font_33px close-design" data-dismiss="modal" aria-hidden="true">
                <span class="close-icon">&times;</span></button>
            <h4 class="modal-title"><?= __d('gl', "2 基準を定める") ?></h4>
        </div>
        <div class="modal-body">
            <div class="col col-xxs-12">
                <?= $this->Html->image('no-image.jpg', ['width' => '', 'height' => '', 'class' => '']) ?>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?= __d('gl', "閉じる") ?></button>
        </div>
    </div>
</div>
<!-- END app/View/Elements/Goal/modal_particularly_step2.ctp -->
