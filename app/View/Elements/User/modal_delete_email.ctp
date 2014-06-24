<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 6/6/14
 * Time: 3:19 PM
 *
 * @var View   $this
 * @var string $email
 * @var string $email_id
 */
?>
<div class="modal fade" id="modal_delete_email">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title"><?= __d('gl', "確認") ?></h4>
            </div>
            <div class="modal-body">
                <p><?= __d('gl', "%sへのメールアドレス変更をキャンセルしますか？", $email) ?></p>
            </div>
            <div class="modal-footer">
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
