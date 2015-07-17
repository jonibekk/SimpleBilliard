<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 6/6/14
 * Time: 3:19 PM
 *
 * @var CodeCompletionView $this
 * @var                    $goal_id
 * @var                    $goal
 * @var                    $kr_list
 */
?>
<!-- START app/View/Elements/Goal/modal_add_action.ctp -->
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close font_33px close-design" data-dismiss="modal" aria-hidden="true"><span
                    class="close-icon">&times;</span></button>
            <h4 class="modal-title"><?= __d('gl', "アクションを追加する") ?></h4>
            <ul class="add-key-result-goal-info">
                <li>
                    <i class="fa fa-flag"></i><?= h($goal['Goal']['name']) ?>
                </li>
                <li>
                    <i class="fa fa-bullseye"></i>
                    <?= h($goal['Goal']['target_value']) ?>
                    (← <?= h($goal['Goal']['start_value']) ?>)<?= $kr_value_unit_list[$goal['Goal']['value_unit']] ?>
                </li>
                <li>
                    <i class="fa fa-calendar"></i>
                    <?= date('Y/m/d', $goal['Goal']['end_date'] + ($this->Session->read('timezone') * 3600)) ?>
                    (← <?= date('Y/m/d', $goal['Goal']['start_date'] + ($this->Session->read('timezone') * 3600)) ?> - )
                </li>
            </ul>
        </div>
        <?=
        $this->Form->create('ActionResult', [
            'inputDefaults' => [
                'div'       => 'form-group',
                'label'     => [
                    'class' => 'no-asterisk'
                ],
                'wrapInput' => 'goal-set-input',
                'class'     => 'form-control addteam_input-design'
            ],
            'class'         => 'form-horizontal',
            'url'           => ['controller' => 'goals', 'action' => 'add_completed_action'],
            'novalidate'    => true,
            'id'            => 'AddGoalFormKeyResult',
        ]); ?>
        <?= $this->Form->hidden('goal_id', ['value' => $goal_id]) ?>
        <div class="modal-body modal-circle-body">
            <div class="row">
                <?=
                $this->Form->input('name',
                                   ['before'                   => '<div class="set-goal">' .
                                       '<h5 class="modal-key-result-headings">' .
                                       __d('gl', "アクション") .
                                       '<span class="modal-key-result-headings-description">' .
                                       __d('gl', "やった事を書こう") . '</span></h5></div>',
                                    'label'                    => false,
                                    'placeholder'              => __d('gl', "具体的に絞り込んで書く"),
                                    "data-bv-notempty-message" => __d('validate', "入力必須項目です。"),
                                    'rows'                     => 1,
                                    'afterInput'               =>
                                        '<span class="help-block font_12px">' . __d('gl', "例）Webサイトを完成させた") . '</span>'
                                   ]) ?>
            </div>
            <div class="row">
                <?=
                $this->Form->input('key_result_id', [
                    'before'   => '<h5 class="modal-key-result-headings">' .
                        __d('gl', "出したい成果") .
                        '<span class="modal-key-result-headings-description">' .
                        __d('gl', "紐付ける成果を選択しよう。選択しなくてもいいけどね。") . '</span></h5>',
                    'label'    => false,
                    'type'     => 'select',
                    'required' => false,
                    'style'    => 'width:170px',
                    'options'  => $kr_list,
                ]) ?>
            </div>
        </div>
        <div class="modal-footer">
            <?=
            $this->Form->submit(__d('gl', "アクションを登録"),
                                ['class' => 'btn btn-primary', 'div' => false, 'disabled' => 'disabled']) ?>

            <button type="button" class="btn btn-default" data-dismiss="modal"><?= __d('gl', "閉じる") ?></button>
        </div>
        <?= $this->Form->end() ?>
    </div>
</div>
<!-- END app/View/Elements/Goal/modal_add_action.ctp -->
