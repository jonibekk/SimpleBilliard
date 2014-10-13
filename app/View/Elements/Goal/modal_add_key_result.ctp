<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 6/6/14
 * Time: 3:19 PM
 *
 * @var CodeCompletionView $this
 * @var                    $goal_id
 * @var                    $key_result_id
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
<!-- START app/View/Elements/Goal/modal_add_key_result.ctp -->
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close font_33px close-design" data-dismiss="modal" aria-hidden="true"><span
                    class="close-icon">&times;</span></button>
            <h4 class="modal-title"><?= __d('gl', "基準を追加") ?></h4>
        </div>
        <?=
        $this->Form->create('KeyResult', [
            'inputDefaults' => [
                'div'       => 'form-group',
                'label'     => [
                    'class' => 'col col-sm-3 control-label no-asterisk goal-set-mid-label'
                ],
                'wrapInput' => 'col col-sm-7 line-vertical-sm goal-set-input',
                'class'     => 'form-control addteam_input-design'
            ],
            'class'         => 'form-horizontal',
            'url'           => ['controller' => 'goals', 'action' => 'add_key_result', $key_result_id],
            'novalidate'    => true,
            'type'          => 'file',
            'id'            => 'AddGoalFormKeyResult',
        ]); ?>
        <div class="modal-body modal-circle-body">
            <div class="col col-xxs-12">
                <?=
                $this->Form->input('KeyResult.name',
                                   ['before'                   => '<div class="col col-sm-3 control-label set-goal">' .
                                       '<label class="no-asterisk">' . __d('gl', "ゴール名") . '</label>' .
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
                    <div class="col col-sm-3">
                        <label class="control-label width100_per text-right"><?= __d('gl', "程度") ?></label>

                        <div class="label-addiction pull-right"><?= __d('gl', "どのくらい？") ?></div>
                    </div>
                    <div class="col col-sm-7 line-vertical-sm goal-set-input">

                        <?=
                        $this->Form->input('KeyResult.value_unit',
                                           ['label'               => __d('gl', "単位"),
                                            'wrapInput'           => 'col col-sm-9 pl_5px',
                                            'type'                => 'select',
                                            'class'               => 'change-select-target-hidden form-control addteam_input-design',
                                            'target-id'           => 'KeyResult0ValueInputWrap',
                                            'required'            => true,
                                            'hidden-option-value' => KeyResult::UNIT_BINARY,
                                            'options'             => $kr_value_unit_list
                                           ]) ?>
                        <div id="KeyResult0ValueInputWrap" style="">
                            <?=
                            $this->Form->input('KeyResult.target_value',
                                               ['label'                        => __d('gl', "達成時"),
                                                'wrapInput'                    => 'col col-sm-9 pl_5px',
                                                'type'                         => 'number',
                                                'step'                         => '0.1',
                                                'default'                      => 100,
                                                'required'                     => true,
                                                'maxlength'                    => 14,
                                                'data-bv-stringlength-message' => __d('validate', "文字数がオーバーしています。"),
                                                "data-bv-notempty-message"     => __d('validate', "入力必須項目です。"),
                                                'data-bv-numeric-message'      => __d('validate', "数字を入力してください。"),
                                               ]) ?>
                            <?=
                            $this->Form->input('KeyResult.start_value',
                                               ['label'                        => __d('gl', "開始時"),
                                                'wrapInput'                    => 'col col-sm-9 pl_5px',
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
                <div class="row">
                    <label class="col col-sm-3 control-label text-right"><?= __d('gl', "期間") ?></label>

                    <div class="col col-sm-7 line-vertical-sm goal-set-input">
                        <div class="form-group" id="KeyResult0EndDateContainer">
                            <label for="KeyResult0EndDate" class="col col-sm-3 control-label goal-set-mid-label"><?=
                                __d('gl',
                                    "期限") ?></label>

                            <div class="input-group date pl_5px goal-set-date"
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
                                                       'wrapInput' => null,
                                                   ]) ?>
                                <span class="input-group-addon"><i class="fa fa-th"></i></span>
                            </div>
                        </div>
                        <div class="form-group" id="KeyResult0StartDateContainer">
                            <label for="KeyResult0StartDate" class="col col-sm-3 control-label goal-set-mid-label"><?=
                                __d('gl', "開始") ?></label>

                            <p class="form-control-static"
                               id="KeyResult0StartDateDefault_<?= $key_result_id ?>">
                                    <span
                                        class="plr_18px"><?= $kr_start_date_format ?>
                                        <?= __d('gl', "（本日）") ?>
                                        &nbsp;&nbsp;<a href="#" class="target-show-target-del"
                                                       show-target-id="KeyResult0StartDateInputWrap_<?= $key_result_id ?>"
                                                       delete-target-id="KeyResult0StartDateDefault_<?= $key_result_id ?>">
                                            <?= __d('gl', "変更") ?></a>
                                    </span>
                            </p>

                            <div class="input-group date plr_5px goal-set-date" style="display: none"
                                 data-date-end-date="<?= $limit_end_date ?>"
                                 data-date-start-date="<?= $limit_start_date ?>"
                                 id="KeyResult0StartDateInputWrap_<?= $key_result_id ?>">
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
                    'before'   => '<div class="col col-sm-3 control-label set-importance">' .
                        '<label>' . __d('gl', "重要度") . '</label>' .
                        '<div class="label-addiction">' . __d('gl', "あなたにとっての<br>この基準の重要度") . '</div></div>',
                    'label'    => false,
                    'type'     => 'select',
                    'default'  => 1,
                    'required' => false,
                    'style'    => 'width:50px',
                    'options'  => $priority_list,
                ]) ?>
            </div>
        </div>
        <div class="modal-footer">
            <?=
            $this->Form->submit(__d('gl', "基準を追加"),
                                ['class' => 'btn btn-primary', 'div' => false, 'disabled' => 'disabled']) ?>

            <button type="button" class="btn btn-default" data-dismiss="modal"><?= __d('gl', "閉じる") ?></button>
        </div>
    </div>
</div>
<!-- END app/View/Elements/Goal/modal_add_key_result.ctp -->
