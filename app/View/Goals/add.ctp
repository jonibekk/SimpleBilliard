<?
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 6/11/14
 * Time: 11:40 AM
 *
 * @var View                   $this
 * @var                        $this CodeCompletionView
 * @var                        $goal_category_list
 * @var                        $priority_list
 * @var                        $kr_value_unit_list
 * @var                        $kr_start_date_format
 * @var                        $kr_end_date_format
 */
?>
<!-- START app/View/Goals/add.ctp -->
<div class="row">
    <div class="col-sm-8 col-sm-offset-2">
        <div class="panel panel-default">
            <div class="panel-heading"><?= __d('gl', "ゴールを作成してください") ?></div>
            <?=
            $this->Form->create('Goal', [
                'inputDefaults' => [
                    'div'       => 'form-group',
                    'label'     => [
                        'class' => 'col col-sm-3 control-label'
                    ],
                    'wrapInput' => 'col col-sm-7',
                    'class'     => 'form-control addteam_input-design'
                ],
                'class'         => 'form-horizontal',
                'novalidate'    => true,
                'type'          => 'file',
                'id'            => 'AddGoalForm',
            ]); ?>
            <div class="panel-body add-team-panel-body">
                <?=
                $this->Form->input('goal_category_id', [
                    'label'   => __d('gl', "カテゴリ"),
                    'type'    => 'select',
                    'options' => $goal_category_list,
                ])?>
                <hr>
                <?=
                $this->Form->input('purpose',
                                   ['label'                    => __d('gl', "達成したいことは？"),
                                    'placeholder'              => __d('gl', "本気で達成したいことをぼんやりと書く"),
                                    'rows'                     => 1,
                                    "data-bv-notempty-message" => __d('validate', "入力必須項目です。"),
                                    'afterInput'               => '<span class="help-block">' . __d('gl',
                                                                                                    "例）世界から貧困を減らすこと") . '</span>'
                                   ]) ?>
                <hr>
                <?=
                $this->Form->input('KeyResult.0.name',
                                   ['label'                    => __d('gl', "指標として何をどうする？"),
                                    'placeholder'              => __d('gl', "具体的に何をどうするかを絞り込んで書く"),
                                    'rows'                     => 1,
                                    "data-bv-notempty-message" => __d('validate', "入力必須項目です。"),
                                    'afterInput'               => '<span class="help-block">' . __d('gl',
                                                                                                    "例）極度な貧困率を減少させる") . '</span>'
                                   ]) ?>
                <?=
                $this->Form->input('KeyResult.0.desired_value',
                                   ['label'     => __d('gl', "数値の単位"),
                                    'before'    => '<label class="col col-sm-3 control-label">' . __d('gl',
                                                                                                      "どのくらい？") . '</label>',
                                    'wrapInput' => 'col col-sm-4',
                                    'type'      => 'select',
                                    'options'   => $kr_value_unit_list
                                   ]) ?>
                <?=
                $this->Form->input('KeyResult.0.target_value',
                                   ['label'                   => __d('gl', "達成時"),
                                    'before'                  => '<label class="col col-sm-3 control-label"></label>',
                                    'wrapInput'               => 'col col-sm-4',
                                    'type'                    => 'number',
                                    'data-bv-integer-message' => __d('validate', "数字のみで入力してください。"),
                                   ]) ?>
                <?=
                $this->Form->input('KeyResult.0.start_value',
                                   ['label'                   => __d('gl', "現在"),
                                    'before'                  => '<label class="col col-sm-3 control-label"></label>',
                                    'wrapInput'               => 'col col-sm-4',
                                    'type'                    => 'number',
                                    'data-bv-integer-message' => __d('validate', "数字のみで入力してください。"),
                                    'data-bv-integer'         => "true",
                                   ]) ?>
                <div class="form-group" id="KeyResult0StartDateContainer">
                    <label for="KeyResult0StartDate" class="col col-sm-3 control-label"><?=
                        __d('gl', "開始日") ?></label>

                    <div class="col col-sm-7">
                        <p class="form-control-static"
                           id="KeyResult0StartDateDefault"><?= $kr_start_date_format ?><?= __d('gl', "（本日）") ?>
                            &nbsp;&nbsp;<a href="#" class="target-show-target-del"
                                           show-target-id="KeyResult0StartDateInputWrap"
                                           delete-target-id="KeyResult0StartDateDefault"><?= __d('gl', "変更") ?></a></p>

                        <div class="input-group date" style="display: none" id="KeyResult0StartDateInputWrap">
                            <?=
                            $this->Form->input('KeyResult.0.start_date',
                                               ['value' => $kr_start_date_format, 'label' => false, 'div' => false, 'class' => "form-control", 'type' => 'text', 'wrapInput' => null]) ?>
                            <span class="input-group-addon"><i class="fa fa-th"></i></span>
                        </div>
                    </div>
                </div>
                <div class="form-group" id="KeyResult0EndDateContainer">
                    <label for="KeyResult0EndDate" class="col col-sm-3 control-label"><?=
                        __d('gl',
                            "いつまでに？") ?></label>

                    <div class="col col-sm-7">
                        <div class="input-group date">
                            <?=
                            $this->Form->input('KeyResult.0.end_date',
                                               ['value' => $kr_end_date_format, 'label' => false, 'div' => false, 'class' => "form-control", 'type' => 'text', 'wrapInput' => null]) ?>
                            <span class="input-group-addon"><i class="fa fa-th"></i></span>
                        </div>
                    </div>
                </div>
                <hr>
                <?=
                $this->Form->input('priority', [
                    'label'    => __d('gl', "重要度"),
                    'type'     => 'select',
                    'default'  => 3,
                    'required' => false,
                    'style'    => 'width:50px',
                    'options'  => $priority_list,
                ])?>
                <hr>
                <?=
                $this->Form->input('description',
                                   ['label'       => __d('gl', "詳細"),
                                    'placeholder' => __d('gl', "詳細を書く"),
                                    'rows'        => 1,
                                   ]) ?>
                <hr>
                <div class="form-group">
                    <label for="" class="col col-sm-3 control-label"><?= __d('gl', "ゴール画像") ?></label>

                    <div class="col col-sm-6">
                        <div class="fileinput_small fileinput-new" data-provides="fileinput">
                            <div class="fileinput-preview thumbnail nailthumb-container photo-design"
                                 data-trigger="fileinput"
                                 style="width: 96px; height: 96px;">
                                <i class="fa fa-plus photo-plus-large"></i>
                            </div>
                            <div>
                        <span class="btn btn-default btn-file">
                            <span class="fileinput-new">
                                <?=
                                __d('gl',
                                    "画像を選択") ?>
                            </span>
                            <span class="fileinput-exists"><?= __d('gl', "画像を再選択") ?></span>
                            <?=
                            $this->Form->input('photo',
                                               ['type'         => 'file',
                                                'label'        => false,
                                                'div'          => false,
                                                'css'          => false,
                                                'wrapInput'    => false,
                                                'errorMessage' => false,
                                                ''
                                               ]) ?>
                        </span>
                            </div>
                        </div>
                        <span class="help-block"><?= __d('gl', '10MB以下') ?></span>

                        <div class="has-error">
                            <?=
                            $this->Form->error('photo', null,
                                               ['class' => 'help-block text-danger',
                                                'wrap'  => 'span'
                                               ]) ?>
                        </div>
                    </div>

                </div>
            </div>

            <div class="panel-footer addteam_pannel-footer">
                <div class="row">
                    <div class="col-sm-9 col-sm-offset-3">
                        <?=
                        $this->Form->submit(__d('gl', "この内容で作成"),
                                            ['class' => 'btn btn-primary', 'div' => false, 'disabled' => 'disabled']) ?>
                    </div>
                </div>
            </div>
            <?= $this->Form->end(); ?>
        </div>
    </div>
</div>
<? $this->append('script') ?>
<script type="text/javascript">
    $(document).ready(function () {
    });
    $('#AddGoalForm').bootstrapValidator({
        live: 'enabled',
        feedbackIcons: {
            valid: 'fa fa-check',
            invalid: 'fa fa-times',
            validating: 'fa fa-refresh'
        },
        fields: {
            "data[Goal][photo]": {
                enabled: false
            }
        }
    });
    //noinspection JSJQueryEfficiency
    $('#KeyResult0StartDateContainer .input-group.date').datepicker({
        format: "yyyy/mm/dd",
        todayBtn: 'linked',
        language: "ja",
        autoclose: true,
        todayHighlight: true
    });
    //noinspection JSJQueryEfficiency
    $('#KeyResult0EndDateContainer .input-group.date').datepicker({
        format: "yyyy/mm/dd",
        todayBtn: 'linked',
        language: "ja",
        autoclose: true,
        todayHighlight: true
    });
</script>
<? $this->end() ?>
<!-- END app/View/Goals/add.ctp -->
