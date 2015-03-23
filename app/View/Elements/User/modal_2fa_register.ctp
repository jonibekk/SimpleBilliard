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
        <?=
        $this->Form->create('User', [
            'url'           => ['controller' => 'users', 'action' => 'register_2fa'],
            'inputDefaults' => [
                'div'       => 'form-group',
                'label'     => [
                    'class' => 'col col-sm-3 modal-label pr_12px'
                ],
                'wrapInput' => 'col col-sm-6',
                'class'     => 'form-control'
            ],
            'class'         => 'form-horizontal',
            'novalidate'    => true,
        ]); ?>
        <div class="modal-body">
            <div class="form-group"><label for="" class="col col-sm-3 modal-label pr_12px"><?= __d('gl',
                                                                                                   "QRコード") ?></label>

                <div class="col col-sm-6">
                    <?= $this->Html->image($url_2fa) ?>
                </div>
            </div>
            <hr>
            <?=
            $this->Form->input('2fa_code',
                               ['label'                    => __d('gl', "確認用コード"),
                                'placeholder'              => __d('gl', "例) 012345"),
                                "data-bv-notempty-message" => __d('validate', "入力必須項目です。"),
                                'required'                 => true,
                               ]) ?>
        </div>
        <div class="modal-footer">
            <?=
            $this->Form->submit(__d('gl', "登録"),
                                ['class' => 'btn btn-primary pull-right', 'div' => false,]) ?>
            <?= $this->Form->end(); ?>
        </div>
    </div>
</div>
<!-- END app/View/Elements/User/modal_2fa_register.ctp -->
