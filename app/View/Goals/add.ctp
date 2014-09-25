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
        <div class="panel-heading goal-set-heading clearfix">
            <div class="pull-left goal-set-title"><span class='font_bold'>1</span> <?= __d('gl', "目的を決める") ?>
            </div>
            <?=
            $this->Html->link(__d('gl', "変更する"), "#",
                              [
                                  'class' => 'btn btn-link btn-purewhite goal-add-edit-button pull-right',
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
        <div class="panel-heading goal-set-heading clearfix panel-closed-headding">
            <div class="pull-left goal-set-title"><span class='font_bold'>2</span> <?= __d('gl', "ゴールを定める") ?>
            </div>
            <?=
            $this->Html->link(__d('gl', "変更する"), "#",
                              [
                                  'class' => 'btn btn-link btn-purewhite goal-add-edit-button pull-right',
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
                        'class' => 'col col-sm-3 control-label no-asterisk goal-set-mid-label'
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
                <?
                if (isset($this->request->data['KeyResult'][0]['id'])) {
                    echo $this->Form->hidden('KeyResult.0.id', ['value' => $this->request->data['KeyResult'][0]['id']]);
                }
                ?>
                <?=
                $this->Form->input('KeyResult.0.name',
                                   ['before'      => '<div class="col col-sm-3 control-label set-goal">' .
                                       '<label class="no-asterisk">' . __d('gl', "ゴール") . '</label>' .
                                       '<div class="label-addiction">' . __d('gl',
                                                                             "達成の指標として<br>『なに』をどうするか？") . '</div></div>',
                                    'label'       => false,
                                    'placeholder' => __d('gl', "具体的に絞り込んで書く"),
                                    "data-bv-notempty-message" => __d('validate', "入力必須項目です。"),
                                    'rows'                     => 1,
                                    'afterInput'               => '<span class="help-block">' . __d('gl',
                                                                                                    "例）極度な貧困率を減少させる") . '</span>'
                                   ]) ?>
                <div class="row">
                    <div class="col col-sm-3">
                        <label class="control-label width100_per text-right"><?= __d('gl', "程度") ?></label>

                        <div class="label-addiction pull-right">どのくらい？</div>
                    </div>
                    <div class="col col-sm-7 line-vertical-sm goal-set-input">

                        <?=
                        $this->Form->input('KeyResult.0.value_unit',
                                           ['label'     => __d('gl', "単位"),
                                            'wrapInput' => 'col col-sm-9',
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
                                                'wrapInput' => 'col col-sm-9',
                                                'type'                    => 'number',
                                                'value'                   => 100,
                                                'data-bv-integer-message' => __d('validate', "数字のみで入力してください。"),
                                               ]) ?>
                            <?=
                            $this->Form->input('KeyResult.0.start_value',
                                               ['label'     => __d('gl', "開始時"),
                                                'wrapInput' => 'col col-sm-9',
                                                'type'                    => 'number',
                                                'value'                   => 0,
                                                'data-bv-integer-message' => __d('validate', "数字のみで入力してください。"),
                                                'data-bv-integer'         => "true",
                                               ]) ?>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <label class="col col-sm-3 control-label text-right">期間</label>

                    <div class="col col-sm-7 line-vertical-sm goal-set-input">
                        <div class="form-group" id="KeyResult0EndDateContainer">
                            <label for="KeyResult0EndDate" class="col col-sm-3 control-label goal-set-mid-label"><?=
                                __d('gl',
                                    "期限") ?></label>

                            <div class="input-group date padding-lr-5px goal-set-date">
                                <?=
                                $this->Form->input('KeyResult.0.end_date',
                                                   ['value' => $kr_end_date_format, 'label' => false, 'div' => false, 'class' => "form-control", 'type' => 'text', 'wrapInput' => null]) ?>
                                <span class="input-group-addon"><i class="fa fa-th"></i></span>
                            </div>
                        </div>
                        <div class="form-group" id="KeyResult0StartDateContainer">
                            <label for="KeyResult0StartDate" class="col col-sm-3 control-label goal-set-mid-label"><?=
                                __d('gl', "開始") ?></label>

                            <p class="form-control-static"
                               id="KeyResult0StartDateDefault">
                                    <span class="padding-lr-18px"><?= $kr_start_date_format ?><?= __d('gl', "（本日）") ?>
                                        &nbsp;&nbsp;<a href="#" class="target-show-target-del"
                                                       show-target-id="KeyResult0StartDateInputWrap"
                                                       delete-target-id="KeyResult0StartDateDefault"><?=
                                            __d('gl',
                                                "変更") ?></a></span>
                            </p>

                            <div class="input-group date padding-lr-5px goal-set-date" style="display: none"
                                 id="KeyResult0StartDateInputWrap">
                                <?=
                                $this->Form->input('KeyResult.0.start_date',
                                                   ['value' => $kr_start_date_format, 'label' => false, 'div' => false, 'class' => "form-control", 'type' => 'text', 'wrapInput' => null]) ?>
                                <span class="input-group-addon"><i class="fa fa-th"></i></span>
                            </div>
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
        <div class="panel-heading goal-set-heading clearfix panel-closed-headding">
            <div class="pull-left goal-set-title"><span class='font_bold'>3</span> <?= __d('gl', "他の情報を追加する") ?>
            </div>
            <?=
            $this->Html->link(__d('gl', "変更する"), "#",
                              [
                                  'class' => 'btn btn-link btn-purewhite goal-add-edit-button pull-right',
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
                'novalidate'    => true,
                'type'          => 'file',
                'id' => 'AddGoalFormOther',
            ]); ?>
            <div class="panel-body add-team-panel-body">
                <?=
                $this->Form->input('description',
                                   ['before'      => '<div class="col col-sm-3 control-label set-detail">' .
                                       '<label>' . __d('gl', "詳細") . '</label>' .
                                       '<div class="label-addiction">' . __d('gl', "内容を補足しましょう") . '</div></div>',
                                    'label'       => false,
                                    'placeholder' => __d('gl', "ゴールの内容を詳しく書く"),
                                    'rows'        => 1,
                                   ]) ?>
                <?=
                $this->Form->input('priority', [
                    'before' => '<div class="col col-sm-3 control-label set-importance">' .
                        '<label>' . __d('gl', "重要度") . '</label>' .
                        '<div class="label-addiction">' . __d('gl', "あなたにとっての<br>このゴールの重要度") . '</div></div>',
                    'label'  => false,
                    'type'     => 'select',
                    'default'  => 3,
                    'required' => false,
                    'style'    => 'width:50px',
                    'options'  => $priority_list,
                ]) ?>
                <div class="form-group">
                    <div class="col col-sm-3 control-label">
                        <label for=""><?= __d('gl', "ゴール画像") ?></label>

                        <div class="label-addiction pull-right">イメージに合った画像を追加しましょう</div>
                    </div>
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
                                <span class="help-block fileinput-limit_mb"><?= __d('gl', '10MB以下') ?></span>
                            </div>
                        </div>

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
                                            ['class' => 'btn btn-primary', 'div' => false]) ?>
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
    $(".panel-container", "#AddGoalFormPurposeWrap").addClass('panel-closed-container');
    //noinspection JSJQueryEfficiency
    $(".goal-add-edit-button", "#AddGoalFormPurposeWrap").show();
    //noinspection JSJQueryEfficiency
    $(".panel-footer", "#AddGoalFormPurposeWrap").hide();
    //noinspection JSJQueryEfficiency
    $(".panel-container", "#AddGoalFormKeyResultWrap").removeClass('hidden');
    //noinspection JSJQueryEfficiency
    $(".panel-heading", "#AddGoalFormKeyResultWrap").removeClass('panel-closed-headding');
    $(".panel-container", "#AddGoalFormKeyResultWrap").removeClass('panel-closed-container');
    <?elseif($this->request->params['named']['mode'] == 3):?>
    disabledAllInput("#AddGoalFormPurpose");
    disabledAllInput("#AddGoalFormKeyResult");
    //noinspection JSJQueryEfficiency
    $(".panel-heading", "#AddGoalFormPurposeWrap").addClass('panel-closed-headding');
    $(".panel-container", "#AddGoalFormPurposeWrap").addClass('panel-closed-container');
    //noinspection JSJQueryEfficiency
    $(".panel-footer", "#AddGoalFormPurposeWrap").hide();
    //noinspection JSJQueryEfficiency
    $(".goal-add-edit-button", "#AddGoalFormPurposeWrap").show();

    //noinspection JSJQueryEfficiency
    $(".panel-container", "#AddGoalFormKeyResultWrap").removeClass('hidden');
    //noinspection JSJQueryEfficiency
    $(".panel-heading", "#AddGoalFormKeyResultWrap").addClass('panel-closed-headding');
    $(".panel-container", "#AddGoalFormKeyResultWrap").addClass('panel-closed-container');
    //noinspection JSJQueryEfficiency
    $(".panel-footer", "#AddGoalFormKeyResultWrap").hide();
    //noinspection JSJQueryEfficiency
    $(".goal-add-edit-button", "#AddGoalFormKeyResultWrap").show();

    //noinspection JSJQueryEfficiency
    $(".panel-heading", "#AddGoalFormOtherWrap").removeClass('panel-closed-headding');
    $(".panel-container", "#AddGoalFormOtherWrap").removeClass('panel-closed-container');
    //noinspection JSJQueryEfficiency
    $(".panel-container", "#AddGoalFormOtherWrap").removeClass('hidden');
    <?endif;?>
    <?endif;?>
    $(".goal-add-edit-button").click(function () {
        attrUndefinedCheck(this, 'target-id');
        var target_id = $(this).attr('target-id');
        enabledAllInput("#" + target_id);
        var $obj = $("#" + target_id);
        //タイトルをアクティブ表示に変更
        $obj.find(".panel-heading").removeClass("panel-closed-headding");
        //formをアクティブ表示に変更
        $obj.find(".panel-container").removeClass("panel-closed-container");
        //noinspection JSJQueryEfficiency
        $(".panel-footer", "#" + target_id).show();
        return false;
    });
</script>
<? $this->end() ?>
<!-- END app/View/Goals/add.ctp -->
