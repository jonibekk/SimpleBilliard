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
$url = isset($this->request->data['Goal']['id']) ? [$this->request->data['Goal']['id']] : [];
?>
<!-- START app/View/Goals/add.ctp -->
<div class="row">
<!--GoalSet01-->
<div class="col-sm-8 col-sm-offset-2">
    <div class="page-title"><?= __d('gl', "新しいゴールを作成") ?></div>
    <div class="panel panel-default" id="AddGoalFormPurposeWrap">
        <div class="panel-heading goal-set-heading">
            <span class='font-weight_bold'>1</span> <?= __d('gl', "目的を決める") ?>
            <?=
            $this->Html->link(__d('gl', "変更する"), "#",
                              [
                                  'class'     => 'btn btn-link btn-white goal-add-edit-button',
                                  'div'       => false,
                                  'style'     => 'display:none',
                                  'target-id' => "AddGoalFormPurposeWrap",
                              ]) ?>
        </div>
        <div class="panel-container">
            <?=
            $this->Form->create('Goal', [
                'inputDefaults' => [
                    'div'       => 'form-group',
                    'label'     => [
                        'class' => 'col col-sm-3 control-label'
                    ],
                    'wrapInput' => 'col col-sm-7 line-vertical-sm goal-set-input',
                    'class' => 'form-control addteam_input-design disabled'
                ],
                'class'         => 'form-horizontal',
                'novalidate'    => true,
                'url'           => $url,
                'type'          => 'file',
                'id'            => 'AddGoalFormPurpose',
            ]); ?>
            <div class="panel-body add-team-panel-body goal-set-body">
                <?=
                $this->Form->input('goal_category_id', [
                    'label' => [
                        'text'  => __d('gl', "カテゴリ"),
                        'class' => 'col col-sm-3 control-label'
                    ],
                    'type'    => 'select',
                    'options' => $goal_category_list,
                ]) ?>
                <?=
                $this->Form->input('purpose',
                                   ['before'      => '<div class="col col-sm-3 control-label">' .
                                       '<label class="no-asterisk">' . __d('gl', "目的") . '</label>' .
                                       '<div class="label-addiction">' . __d('gl', "達成したいことは？") . '</div></div>',
                                    'label'       => false,
                                    'placeholder' => __d('gl', "達成したいことをざっくり書く"),
                                    'rows'                     => 1,
                                    "data-bv-notempty-message" => __d('validate', "入力必須項目です。"),
                                    'afterInput'  => '<span class="help-block goal-form-addiction">' . __d('gl',
                                                                                                           "例）世界から貧困を減らすこと") . '</span>'
                                   ]) ?>
            </div>

            <div class="panel-footer addteam_pannel-footer goalset_pannel-footer">
                <div class="row">
                    <div class="col-sm-7 col-sm-offset-5 goal-set-buttons">
                        <?=
                        $this->Html->link(__d('gl', "詳しくはこちら"), "#",
                                          ['class' => 'btn btn-link btn-white', 'div' => false]) ?>
                        <?=
                        $this->Form->submit(__d('gl', "次のステップ"),
                                            ['class' => 'btn btn-primary', 'div' => false, 'disabled' => 'disabled']) ?>
                    </div>
                </div>
            </div>
            <?= $this->Form->end(); ?>
        </div>
    </div>
</div>
<!--GoalSet02-->
<div class="col-sm-8 col-sm-offset-2">
    <div class="panel panel-default" id="AddGoalFormKeyResultWrap">
        <div class="panel-heading goal-set-heading panel-closed-headding">
            <span class='font-weight_bold'>2</span> <?= __d('gl', "ゴールを定める") ?>
            <?=
            $this->Html->link(__d('gl', "変更する"), "#",
                              [
                                  'class'     => 'btn btn-link btn-white goal-add-edit-button',
                                  'div'       => false,
                                  'style'     => 'display:none',
                                  'target-id' => "AddGoalFormKeyResultWrap",
                              ]) ?>
        </div>
        <div class="panel-container hidden">
            <?=
            $this->Form->create('Goal', [
                'inputDefaults' => [
                    'div'       => 'form-group',
                    'label'     => [
                        'class' => 'col col-sm-3 control-label'
                    ],
                    'wrapInput' => 'col col-sm-7 line-vertical-sm goal-set-input',
                    'class'     => 'form-control addteam_input-design'
                ],
                'class'         => 'form-horizontal',
                'url' => array_merge($url, ['mode' => 2]),
                'novalidate'    => true,
                'type'          => 'file',
                'id'  => 'AddGoalFormKeyResult',
            ]); ?>
            <div class="panel-body add-team-panel-body">
                <?
                if (isset($this->request->data['KeyResult'][0]['id'])) {
                    echo $this->Form->hidden('KeyResult.0.id', ['value' => $this->request->data['KeyResult'][0]['id']]);
                }
                ?>
                <?=
                $this->Form->input('KeyResult.0.name',
                                   ['label'       => __d('gl', "ゴール？"),
                                    'placeholder' => __d('gl', "具体的に絞り込んで書く"),
                                    "data-bv-notempty-message" => __d('validate', "入力必須項目です。"),
                                    'rows'                     => 1,
                                    'afterInput'               => '<span class="help-block">' . __d('gl',
                                                                                                    "例）極度な貧困率を減少させる") . '</span>'
                                   ]) ?>
                <div class="row">
                    <div class="col col-sm-3">
                        <label class="control-label width100_per text-right"><?= __d('gl', "程度") ?></label>
                    </div>
                    <div class="col col-sm-7 line-vertical-sm goal-set-input">

                        <?=
                        $this->Form->input('KeyResult.0.value_unit',
                                           ['label'               => __d('gl', "数値の単位"),
                                            'wrapInput'           => 'col col-sm-4',
                                            'type'                => 'select',
                                            'class'               => 'change-select-target-hidden form-control addteam_input-design',
                                            'target-id'           => 'KeyResult0ValueInputWrap',
                                            'hidden-option-value' => KeyResult::UNIT_BINARY,
                                            'options'             => $kr_value_unit_list
                                           ]) ?>
                        <div id="KeyResult0ValueInputWrap">

                            <?=
                            $this->Form->input('KeyResult.0.target_value',
                                               ['label'                   => __d('gl', "達成時"),
                                                'wrapInput'               => 'col col-sm-4',
                                                'type'                    => 'number',
                                                'value'                   => 100,
                                                'data-bv-integer-message' => __d('validate', "数字のみで入力してください。"),
                                               ]) ?>
                            <?=
                            $this->Form->input('KeyResult.0.start_value',
                                               ['label'                   => __d('gl', "現在"),
                                                'wrapInput'               => 'col col-sm-4',
                                                'type'                    => 'number',
                                                'value'                   => 0,
                                                'data-bv-integer-message' => __d('validate', "数字のみで入力してください。"),
                                                'data-bv-integer'         => "true",
                                               ]) ?>
                        </div>
                    </div>
                </div>

                <div class="form-group" id="KeyResult0StartDateContainer">
                    <label for="KeyResult0StartDate" class="col col-sm-3 control-label"><?=
                        __d('gl', "期間") ?></label>

                    <div class="col col-sm-7 line-vertical-sm goal-set-input">
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

                    <div class="col col-sm-7 line-vertical-sm goal-set-input">
                        <div class="input-group date">
                            <?=
                            $this->Form->input('KeyResult.0.end_date',
                                               ['value' => $kr_end_date_format, 'label' => false, 'div' => false, 'class' => "form-control", 'type' => 'text', 'wrapInput' => null]) ?>
                            <span class="input-group-addon"><i class="fa fa-th"></i></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel-footer addteam_pannel-footer goalset_pannel-footer">
                <div class="row">
                    <div class="col-sm-7 col-sm-offset-5 goal-set-buttons">
                        <?=
                        $this->Html->link(__d('gl', "詳しくはこちら"), "#",
                                          ['class' => 'btn btn-link btn-white', 'div' => false]) ?>
                        <?=
                        $this->Form->submit(__d('gl', "次のステップ"),
                                            ['class' => 'btn btn-primary', 'div' => false, 'disabled' => 'disabled']) ?>
                    </div>
                </div>
            </div>
            <?= $this->Form->end(); ?>
        </div>
    </div>
</div>

<!--GoalSet03-->
<div class="col-sm-8 col-sm-offset-2">
    <div class="panel panel-default" id="AddGoalFormOtherWrap">
        <div class="panel-heading goal-set-heading panel-closed-headding">
            <span class='font-weight_bold'>3</span> <?= __d('gl', "他の情報を追加する") ?>
            <?=
            $this->Html->link(__d('gl', "変更する"), "#",
                              [
                                  'class'     => 'btn btn-link btn-white goal-add-edit-button',
                                  'div'       => false,
                                  'style'     => 'display:none',
                                  'target-id' => "AddGoalFormOtherWrap",
                              ]) ?>
        </div>
        <div class="panel-container hidden">
            <?=
            $this->Form->create('Goal', [
                'inputDefaults' => [
                    'div'       => 'form-group',
                    'label'     => [
                        'class' => 'col col-sm-3 control-label'
                    ],
                    'wrapInput' => 'col col-sm-7 line-vertical-sm goal-set-input',
                    'class'     => 'form-control addteam_input-design'
                ],
                'class'         => 'form-horizontal',
                'url'           => array_merge($url, ['mode' => 2]),
                'novalidate'    => true,
                'type'          => 'file',
                'id'            => 'AddGoalFormKeyResult',
            ]); ?>
            <div class="panel-body add-team-panel-body">
                <?=
                $this->Form->input('priority', [
                    'label'    => __d('gl', "重要度"),
                    'type'     => 'select',
                    'default'  => 3,
                    'required' => false,
                    'style'    => 'width:50px',
                    'options'  => $priority_list,
                ]) ?>
                <?=
                $this->Form->input('description',
                                   ['label'       => __d('gl', "詳細"),
                                    'placeholder' => __d('gl', "詳細を書く"),
                                    'rows'        => 1,
                                   ]) ?>
            </div>
            <?=
            $this->Form->create('Goal', [
                'inputDefaults' => [
                    'div'       => 'form-group',
                    'label'     => [
                        'class' => 'col col-sm-3 control-label'
                    ],
                    'wrapInput' => 'col col-sm-7 line-vertical-sm goal-set-input',
                    'class'     => 'form-control addteam_input-design'
                ],
                'class'         => 'form-horizontal',
                'novalidate'    => true,
                'type'          => 'file',
                'id' => 'AddGoalFormOther',
            ]); ?>
            <div class="panel-body add-team-panel-body">
                <div class="form-group">
                    <label for="" class="col col-sm-3 control-label"><?= __d('gl', "ゴール画像") ?></label>

                    <div class="col col-sm-6 line-vertical-sm goal-set-input">
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
            <div class="panel-footer addteam_pannel-footer goalset_pannel-footer">
                <div class="row">
                    <div class="col-sm-3 col-sm-offset-9">
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
</div>
<? $this->append('script') ?>
<script type="text/javascript">
    $(document).ready(function () {
    });
    $('#AddGoalFormPurpose').bootstrapValidator({
        live: 'enabled',
        feedbackIcons: {
            valid: 'fa fa-check',
            invalid: 'fa fa-times',
            validating: 'fa fa-refresh'
        }
    });
    $('#AddGoalFormKeyResult').bootstrapValidator({
        live: 'enabled',
        feedbackIcons: {
            valid: 'fa fa-check',
            invalid: 'fa fa-times',
            validating: 'fa fa-refresh'
        }
    });
    $('#AddGoalFormOther').bootstrapValidator({
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
    //modeによってdisableにする
    <?if(isset($this->request->params['named']['mode'])):?>
    <?if($this->request->params['named']['mode'] == 2):?>
    disabledAllInput("#AddGoalFormPurpose");
    //noinspection JSJQueryEfficiency
    $(".panel-heading", "#AddGoalFormPurposeWrap").addClass('panel-closed-headding');
    //noinspection JSJQueryEfficiency
    $(".goal-add-edit-button", "#AddGoalFormPurposeWrap").show();
    //noinspection JSJQueryEfficiency
    $(".panel-footer", "#AddGoalFormPurposeWrap").hide();
    //noinspection JSJQueryEfficiency
    $(".panel-container", "#AddGoalFormKeyResultWrap").removeClass('hidden');
    //noinspection JSJQueryEfficiency
    $(".panel-heading", "#AddGoalFormKeyResultWrap").removeClass('panel-closed-headding');
    <?elseif($this->request->params['named']['mode'] == 3):?>
    disabledAllInput("#AddGoalFormPurpose");
    disabledAllInput("#AddGoalFormKeyResult");
    //noinspection JSJQueryEfficiency
    $(".panel-heading", "#AddGoalFormPurposeWrap").addClass('panel-closed-headding');
    //noinspection JSJQueryEfficiency
    $(".panel-footer", "#AddGoalFormPurposeWrap").hide();
    //noinspection JSJQueryEfficiency
    $(".goal-add-edit-button", "#AddGoalFormPurposeWrap").show();

    //noinspection JSJQueryEfficiency
    $(".panel-container", "#AddGoalFormKeyResultWrap").removeClass('hidden');
    //noinspection JSJQueryEfficiency
    $(".panel-heading", "#AddGoalFormKeyResultWrap").addClass('panel-closed-headding');
    //noinspection JSJQueryEfficiency
    $(".panel-footer", "#AddGoalFormKeyResultWrap").hide();
    //noinspection JSJQueryEfficiency
    $(".goal-add-edit-button", "#AddGoalFormKeyResultWrap").show();

    //noinspection JSJQueryEfficiency
    $(".panel-heading", "#AddGoalFormOtherWrap").removeClass('panel-closed-headding');
    //noinspection JSJQueryEfficiency
    $(".panel-container", "#AddGoalFormOtherWrap").removeClass('hidden');
    <?endif;?>
    <?endif;?>
    $(".goal-add-edit-button").click(function () {
        attrUndefinedCheck(this, 'target-id');
        var target_id = $(this).attr('target-id');
        enabledAllInput("#" + target_id);
        //noinspection JSJQueryEfficiency
        $(".panel-footer", "#" + target_id).show();
        return false;
    });
</script>
<? $this->end() ?>
<!-- END app/View/Goals/add.ctp -->
