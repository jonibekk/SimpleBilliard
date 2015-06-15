<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 6/6/14
 * Time: 3:19 PM
 *
 * @var CodeCompletionView $this
 * @var string             $email
 * @var string             $email_id
 */
?>
<!-- START app/View/Elements/User/modal_delete_email.ctp -->
<div class="modal fade" tabindex="-1" id="modal_delete_email">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close font_33px close-design" data-dismiss="modal" aria-hidden="true">
                    <span class="close-icon">&times;</span></button>
                <h4 class="modal-title"><?= __d('gl', "確認") ?></h4>
            </div>
            <div class="modal-body">
                <p><?= __d('gl', "%sへのメールアドレス変更をキャンセルしますか？", $email) ?></p>
            </div>
            <div class="modal-footer modal_pannel-footer">
                <?=
                $this->Form
                    ->postLink(__d('gl', "はい"),
                               [
                                   'controller' => 'emails',
                                   'action'     => 'delete',
                                   $email_id
                               ],
                               ['class' => 'btn btn-primary']); ?>
            </div>
        </div>
    </div>
</div>
<!-- END app/View/Elements/User/modal_delete_email.ctp -->
