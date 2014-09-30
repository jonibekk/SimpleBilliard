<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 6/6/14
 * Time: 3:19 PM

 *
*@var CodeCompletionView $this
 * @var                    $skr
 */
?>
<!-- START app/View/Elements/modal_collabo.ctp -->
<div class="modal fade" tabindex="-1" id="ModalCollabo_<?= $skr['id'] ?>">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close font_33px close-design" data-dismiss="modal" aria-hidden="true">
                    <span class="close-icon">&times;</span></button>
                <h4 class="modal-title"><?= __d('gl', "コラボる") ?></h4>
            </div>
            <?=
            $this->Form->create('KeyResultUser', [
                'url'           => ['controller' => 'goals', 'action' => 'edit_collabo'],
                'inputDefaults' => [
                    'div'       => 'form-group',
                    'label'     => [
                        'class' => 'col col-sm-3 control-label'
                    ],
                    'wrapInput' => 'col col-sm-6',
                    'class' => 'form-control modal_input-design disable-change-warning'
                ],
                'class'         => 'form-horizontal',
                'novalidate'    => true,
                'id'            => 'CollaboForm_' . $skr['id'],
            ]); ?>
            <?= $this->Form->hidden('key_result_id', ['value' => $skr['id']]) ?>
            <div class="modal-body">
                <?=
                $this->Form->input('role',
                                   ['label'                    => __d('gl', "役割"),
                                    'placeholder'              => __d('gl', "例) ○○"),
                                    "data-bv-notempty-message" => __d('validate', "入力必須項目です。"),
                                    'required' => true,
                                   ]) ?>
                <hr>
                <?=
                $this->Form->input('description',
                                   ['label'                    => __d('gl', "詳細"),
                                    'placeholder'              => __d('gl', "例) ○○"),
                                    "data-bv-notempty-message" => __d('validate', "入力必須項目です。"),
                                    'required'                 => true,
                                   ]) ?>
            </div>
            <div class="modal-footer">
                <div class="row">
                    <div class="col-sm-9 col-sm-offset-3">
                        <button type="button" class="btn btn-link design-cancel bd-radius_4px"
                                data-dismiss="modal"><?= __d('gl',
                                                             "キャンセル") ?></button>
                        <?=
                        $this->Form->submit(__d('gl', "コラボる"),
                                            ['class' => 'btn btn-primary', 'div' => false, 'disabled' => 'disabled']) ?>

                    </div>
                </div>
            </div>
            <?= $this->Form->end(); ?>
        </div>
    </div>
</div>
<!-- END app/View/Elements/modal_collabo.ctp -->
