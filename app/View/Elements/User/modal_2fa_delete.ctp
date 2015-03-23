<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 6/6/14
 * Time: 3:19 PM
 *
 * @var CodeCompletionView $this
 * @var                    $url_2fa
 */
?>
<!-- START app/View/Elements/User/modal_2fa_register.ctp -->
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close font_33px close-design" data-dismiss="modal" aria-hidden="true"><span
                    class="close-icon">&times;</span></button>
            <h4 class="modal-title"><?= __d('gl', "２要素認証設定") ?></h4>
        </div>
        <div class="modal-body">
            <div class="form-group"><label for="" class="col col-sm-3 modal-label pr_12px"></label>

                <div class="col col-sm-6">
                    <p class="form-control-static"><?= __d('gl', "現在、２要素認証が有効になっています。") ?></p>

                    <p class="form-control-static"><?= __d('gl', "２要素認証を解除しますか？") ?></p>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <?= $this->Form->postButton(__d('gl', "解除する"), ['controller' => 'users', 'action' => 'delete_2fa'],
                                        ['class' => 'btn btn-primary pull-right', 'div' => false,]) ?>
        </div>
    </div>
</div>
<!-- END app/View/Elements/User/modal_2fa_register.ctp -->
