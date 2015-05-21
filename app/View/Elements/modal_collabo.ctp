<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 6/6/14
 * Time: 3:19 PM
 *
 * @var CodeCompletionView $this
 * @var                    $goal
 * @var                    $priority_list
 */
?>
<!-- START app/View/Elements/modal_collabo.ctp -->
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close font_33px close-design" data-dismiss="modal" aria-hidden="true">
                <span class="close-icon">&times;</span></button>
            <h4 class="modal-title"><?= empty($goal['MyCollabo']) ? __d('gl', "コラボる") : __d('gl', "コラボを編集") ?></h4>
        </div>
        <?php $collabo_id = isset($goal['MyCollabo'][0]['id']) ? $goal['MyCollabo'][0]['id'] : null ?>
        <?=
        $this->Form->create('Collaborator', [
            'url'           => ['controller' => 'goals', 'action' => 'edit_collabo', $collabo_id],
            'inputDefaults' => [
                'div'       => 'form-group',
                'label'     => [
                    'class' => 'col col-sm-3 control-label pr_5px'
                ],
                'wrapInput' => 'col col-sm-6',
                'class'     => 'form-control modal_input-design disable-change-warning'
            ],
            'class'         => 'form-horizontal',
            'novalidate'    => true,
            'id'            => 'CollaboEditForm',
        ]); ?>
        <?= $this->Form->hidden('goal_id', ['value' => $goal['Goal']['id']]) ?>
        <?php if (isset($goal['MyCollabo'][0]['id'])) {
            echo $this->Form->hidden('id', ['value' => $goal['MyCollabo'][0]['id']]);
        }
        ?>
        <div class="modal-body">
            <?=
            $this->Form->input('role',
                               ['label'                    => __d('gl', "役割"),
                                'placeholder'              => __d('gl', "例) ○○"),
                                "data-bv-notempty-message" => __d('validate', "入力必須項目です。"),
                                'required'                 => true,
                                'rows'                     => 1,
                                'value'                    => isset($goal['MyCollabo'][0]['role']) ? $goal['MyCollabo'][0]['role'] : null,
                               ]) ?>
            <hr>
            <?=
            $this->Form->input('description',
                               ['label'                    => __d('gl', "詳細"),
                                'placeholder'              => __d('gl', "例) ○○"),
                                "data-bv-notempty-message" => __d('validate', "入力必須項目です。"),
                                'required'                 => true,
                                'rows'                     => 1,
                                'value'                    => isset($goal['MyCollabo'][0]['description']) ? $goal['MyCollabo'][0]['description'] : null,
                               ]) ?>
            <hr>
            <?=
            $this->Form->input('priority',
                               ['label'                    => __d('gl', "重要度"),
                                "data-bv-notempty-message" => __d('validate', "入力必須項目です。"),
                                'required'                 => true,
                                'type'                     => 'select',
                                'default'                  => 3,
                                'options'                  => $priority_list,
                                'value'                    => isset($goal['MyCollabo'][0]['priority']) ? $goal['MyCollabo'][0]['priority'] : null,
                               ]) ?>

        </div>
        <div class="modal-footer">
            <div class="row">
                <div class="col-sm-9 col-sm-offset-3">
                    <button type="button" class="btn btn-link design-cancel bd-radius_4px"
                            data-dismiss="modal"><?= __d('gl',
                                                         "キャンセル") ?></button>
                    <?=
                    $this->Form->submit(empty($goal['MyCollabo']) ? __d('gl', "コラボる") : __d('gl', "コラボを編集"),
                                        ['class' => 'btn btn-primary', 'div' => false, 'disabled' => 'disabled']) ?>
                    <?= $this->Form->end(); ?>
                    <?php if (!empty($goal['MyCollabo'])): ?>
                        <?=
                        $this->Form->postLink(__d('gl', "コラボ抜ける"),
                                              ['controller' => 'goals', 'action' => 'delete_collabo', $goal['MyCollabo'][0]['id']],
                                              ['class' => 'pull-left btn btn-link'],
                                              __d('gl', "本当にコラボレータから抜けますか？")) ?>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>
</div>
<!-- END app/View/Elements/modal_collabo.ctp -->
