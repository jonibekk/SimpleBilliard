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
                <h4 class="modal-title"><?= __("確認") ?></h4>
            </div>
            <div class="modal-body">
                <p><?= __("%sへのメールアドレス変更をキャンセルしますか？", $email) ?></p>
            </div>
            <div class="modal-footer modal_pannel-footer">
                <button type="button" class="btn btn-link design-cancel mr_8px bd-radius_4px" data-dismiss="modal">閉じる</button>
                <?=
                $this->Form
                    ->postLink(__("キャンセルする"),
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
