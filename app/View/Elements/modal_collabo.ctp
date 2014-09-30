<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 6/6/14
 * Time: 3:19 PM
 *
 * @var CodeCompletionView $this
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
                <h4 class="modal-title"><?= empty($skr['MyCollabo']) ? __d('gl', "コラボる") : __d('gl', "コラボを編集") ?></h4>
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
            <?
            if (isset($skr['MyCollabo'][0]['id'])) {
                echo $this->Form->hidden('id', ['value' => $skr['MyCollabo'][0]['id']]);
            }
            ?>
            <div class="modal-body">
                <?=
                $this->Form->input('role',
                                   ['label'                    => __d('gl', "役割"),
                                    'placeholder'              => __d('gl', "例) ○○"),
                                    "data-bv-notempty-message" => __d('validate', "入力必須項目です。"),
                                    'required' => true,
                                    'value'    => isset($skr['MyCollabo'][0]['role']) ? $skr['MyCollabo'][0]['role'] : null,
                                   ]) ?>
                <hr>
                <?=
                $this->Form->input('description',
                                   ['label'                    => __d('gl', "詳細"),
                                    'placeholder'              => __d('gl', "例) ○○"),
                                    "data-bv-notempty-message" => __d('validate', "入力必須項目です。"),
                                    'required'                 => true,
                                    'value' => isset($skr['MyCollabo'][0]['description']) ? $skr['MyCollabo'][0]['description'] : null,
                                   ]) ?>
            </div>
            <div class="modal-footer">
                <div class="row">
                    <div class="col-sm-9 col-sm-offset-3">
                        <button type="button" class="btn btn-link design-cancel bd-radius_4px"
                                data-dismiss="modal"><?= __d('gl',
                                                             "キャンセル") ?></button>
                        <?=
                        $this->Form->submit(empty($skr['MyCollabo']) ? __d('gl', "コラボる") : __d('gl', "コラボを編集"),
                                            ['class' => 'btn btn-primary', 'div' => false, 'disabled' => 'disabled']) ?>
                        <?= $this->Form->end(); ?>
                        <? if (!empty($skr['MyCollabo'])): ?>
                            <?=
                            $this->Form->postLink(__d('gl', "コラボ抜ける"),
                                                  ['controller' => 'goals', 'action' => 'delete_collabo', $skr['MyCollabo'][0]['id']],
                                                  ['class' => 'pull-left btn btn-link'],
                                                  __d('gl', "本当にコラボレータから抜けますか？")) ?>
                        <? endif; ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- END app/View/Elements/modal_collabo.ctp -->
