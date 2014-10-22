<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 6/6/14
 * Time: 3:19 PM
 *
 * @var CodeCompletionView $this
 * @var                    $goal_id
 * @var                    $goal_id
 * @var                    $goal_category_list
 * @var                    $priority_list
 * @var                    $kr_priority_list
 * @var                    $kr_value_unit_list
 * @var                    $kr_start_date_format
 * @var                    $kr_end_date_format
 * @var                    $limit_end_date
 * @var                    $limit_start_date
 */
?>
<!-- START app/View/Elements/Goal/modal_edit_key_result.ctp -->
<div class="modal-dialog">
<div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close font_33px close-design" data-dismiss="modal" aria-hidden="true"><span
                class="close-icon">&times;</span></button>
        <h4 class="modal-title"><?= __d('gl', "成果を更新") ?></h4>
    </div>
    <?=
    $this->Form->create('KeyResult', [
        'inputDefaults' => [
            'div'       => 'form-group',
            'label'     => [
                'class' => 'col col-sm-12 control-label no-asterisk text-align_left'
            ],
            'wrapInput' => 'col col-sm-7 line-vertical-sm goal-set-input',
            'class'     => 'form-control addteam_input-design'
        ],
        'class'         => 'form-horizontal',
        'url'           => ['controller' => 'goals', 'action' => 'edit_key_result', $goal_id],
        'novalidate'    => true,
        'id'            => 'AddGoalFormKeyResult',
    ]); ?>
    <?= $this->Form->hidden('KeyResult.id', ['value' => $goal_id]) ?>
    <div class="modal-body modal-circle-body">
        <div class="col col-xxs-12">
            <?=
            $this->Form->input('KeyResult.name',
                               ['before'                   => '<div class="col col-sm-3 control-label set-goal">' .
                                   '<label class="no-asterisk">' . __d('gl', "成果名") . '</label>' .
                                   '<div class="label-addiction">' . __d('gl',
                                                                         "達成の指標として<br>『なに』をどうするか？") . '</div></div>',
                                'label'                    => false,
                                'placeholder'              => __d('gl', "具体的に絞り込んで書く"),
                                "data-bv-notempty-message" => __d('validate', "入力必須項目です。"),
                                'rows'                     => 1,
                                'afterInput'               => '<span class="help-block font_12px">' . __d('gl',
                                                                                                          "例）サービスAの国内市場シェアを増加させる") . '</span>'
                               ]) ?>
            <div class="row">
                <?=
                $this->Form->input('KeyResult.current_value',
                                   [
                                       'before'                       => '<div class="col col-sm-3 control-label set-importance">' .
                                           '<label>' . __d('gl', "現在値") . '</label>' .
                                           '<div class="label-addiction">' . '</div></div>',
                                       'label'                        => false,
                                       'wrapInput'                    => 'col col-sm-3 line-vertical-sm goal-set-input',
                                       'type'                         => 'number',
                                       'step'                         => '0.1',
                                       'default'                      => 100,
                                       'required'                     => true,
                                       'maxlength'                    => 14,
                                       'data-bv-stringlength-message' => __d('validate', "文字数がオーバーしています。"),
                                       "data-bv-notempty-message"     => __d('validate', "入力必須項目です。"),
                                       'data-bv-numeric-message'      => __d('validate', "数字を入力してください。"),
                                   ]) ?>
            </div>

            <div class="row">
                <div class="col col-sm-3">
                    <label class="control-label width100_per text-right"><?= __d('gl', "程度") ?></label>

                    <div class="label-addiction pull-right"><?= __d('gl', "どのくらい？") ?></div>
                </div>
                <div class="col col-sm-7 line-vertical-sm goal-set-input">
                    <div class="col col-sm-3">
                        <?=
                        $this->Form->input('KeyResult.value_unit',
                                           ['label'               => __d('gl', "単位"),
                                            'wrapInput'           => 'col col-sm-12',
                                            'type'                => 'select',
                                            'class'               => 'change-select-target-hidden form-control addteam_input-design',
                                            'target-id'           => 'KeyResult0ValueInputWrap_' . $goal_id,
                                            'required'            => true,
                                            'hidden-option-value' => KeyResult::UNIT_BINARY,
                                            'options'             => $kr_value_unit_list
                                           ]) ?>
                    </div>
                    <div id="KeyResult0ValueInputWrap_<?= $goal_id ?>" style="">
                        <div class="col col-sm-4  pl_12px">
                            <?=
                            $this->Form->input('KeyResult.target_value',
                                               ['label'                        => __d('gl', "達成時"),
                                                'wrapInput'                    => 'col col-sm-12',
                                                'type'                         => 'number',
                                                'step'                         => '0.1',
                                                'default'                      => 100,
                                                'required'                     => true,
                                                'maxlength'                    => 14,
                                                'data-bv-stringlength-message' => __d('validate', "文字数がオーバーしています。"),
                                                "data-bv-notempty-message"     => __d('validate', "入力必須項目です。"),
                                                'data-bv-numeric-message'      => __d('validate', "数字を入力してください。"),
                                               ]) ?>
                        </div>
                        <div class="col col-sm-4  pl_12px">
                            <?=
                            $this->Form->input('KeyResult.start_value',
                                               ['label'                        => __d('gl', "開始時"),
                                                'wrapInput'                    => 'col col-sm-12',
                                                'type'                         => 'number',
                                                'step'                         => '0.1',
                                                'default'                      => 0,
                                                'required'                     => true,
                                                'maxlength'                    => 14,
                                                'data-bv-stringlength-message' => __d('validate', "文字数がオーバーしています。"),
                                                "data-bv-notempty-message"     => __d('validate', "入力必須項目です。"),
                                                'data-bv-numeric-message'      => __d('validate', "数字を入力してください。"),
                                               ]) ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <label class="col col-sm-3 control-label text-right"><?= __d('gl', "期間") ?></label>

                <div class="col col-sm-7 line-vertical-sm goal-set-input">
                    <div class="form-group col col-sm-6" id="KeyResult0EndDateContainer">
                        <label for="KeyResult0EndDate" class="col col-sm-12 control-label goal-set-mid-label"><?=
                            __d('gl',
                                "期限") ?></label>

                        <div class="input-group date col col-sm-12 goal-set-date"
                             data-date-end-date="<?= $limit_end_date ?>"
                             data-date-start-date="<?= $limit_start_date ?>">
                            <?=
                            $this->Form->input('KeyResult.end_date',
                                               [
                                                   'value'                    => $kr_end_date_format,
                                                   'default'                  => $kr_end_date_format,
                                                   'label'                    => false,
                                                   'div'                      => false,
                                                   'class'                    => "form-control",
                                                   'required'                 => true,
                                                   "data-bv-notempty-message" => __d('validate', "入力必須項目です。"),
                                                   'type'                     => 'text',
                                                   'wrapInput'                => null,
                                               ]) ?>
                            <span class="input-group-addon bd-r-radius_4px"><i class="fa fa-th"></i></span>
                        </div>
                    </div>
                    <div class="form-group col col-sm-6" id="KeyResult0StartDateContainer">
                        <label for="KeyResult0StartDate" class="col col-sm-12 control-label goal-set-mid-label"><?=
                            __d('gl', "開始") ?></label>

                        <p class="form-control-static col col-sm-12"
                           id="KeyResult0StartDateDefault_<?= $goal_id ?>">
                                    <span
                                        class="pull-left"><?= $kr_start_date_format ?>
                                        &nbsp;&nbsp;<a href="#" class="target-show-target-del"
                                                       show-target-id="KeyResult0StartDateInputWrap_<?= $goal_id ?>"
                                                       delete-target-id="KeyResult0StartDateDefault_<?= $goal_id ?>">
                                            <?= __d('gl', "変更") ?></a>
                                    </span>
                        </p>

                        <div class="input-group date plr_5px goal-set-date" style="display: none"
                             data-date-end-date="<?= $limit_end_date ?>"
                             data-date-start-date="<?= $limit_start_date ?>"
                             id="KeyResult0StartDateInputWrap_<?= $goal_id ?>">
                            <?=
                            $this->Form->input('KeyResult.start_date',
                                               [
                                                   'value'                    => $kr_start_date_format,
                                                   'label'                    => false,
                                                   'div'                      => false,
                                                   'class'                    => "form-control",
                                                   'required'                 => true,
                                                   "data-bv-notempty-message" => __d('validate', "入力必須項目です。"),
                                                   'type'                     => 'text',
                                                   'wrapInput'                => null
                                               ]) ?>
                            <span class="input-group-addon"><i class="fa fa-th"></i></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <?=
            $this->Form->input('priority', [
                'before'                   => '<div class="col col-sm-3 control-label set-importance">' .
                    '<label>' . __d('gl', "重要度") . '</label>' .
                    '<div class="label-addiction">' . __d('gl', "あなたにとっての<br>この成果の重要度") . '</div></div>',
                'label'                    => false,
                'type'                     => 'select',
                'default'                  => 1,
                'required'                 => true,
                "data-bv-notempty-message" => __d('validate', "入力必須項目です。"),
                'style'                    => 'width:50px',
                'options'                  => $kr_priority_list,
            ]) ?>
        </div>
    </div>
    <div class="modal-footer">
        <?=
        $this->Form->submit(__d('gl', "成果を更新"),
                            ['class' => 'btn btn-primary', 'div' => false]) ?>

        <button type="button" class="btn btn-default" data-dismiss="modal"><?= __d('gl', "閉じる") ?></button>
    </div>
</div>
</div>
<!-- END app/View/Elements/Goal/modal_edit_key_result.ctp -->
