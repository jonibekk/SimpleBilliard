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
 * @var                        $goal_start_date_format
 * @var                        $goal_end_date_format
 * @var                        $goal_start_date_limit_format
 * @var                        $goal_end_date_limit_format
 */
$url = isset($this->request->data['Goal']['id']) ? [$this->request->data['Goal']['id']] : [];

$url = isset($this->request->params['named']['purpose_id']) ? array_merge($url,
                                                                          ['purpose_id' => $this->request->params['named']['purpose_id']]) : $url;
?>
<!-- START app/View/Goals/add.ctp -->
<div class="row">
<!--GoalSet01-->
<div class="col-sm-8 col-sm-offset-2">
    <div class="page-title">
        <?= isset($this->request->data['Goal']['id']) ? __d('gl', "ゴールを編集") : __d('gl', "新しいゴールを作成") ?>
    </div>
    <div class="panel panel-default" id="AddGoalFormPurposeWrap">
        <div class="panel-heading goal-set-heading clearfix">
            <div class="pull-left goal-set-title"><span class='font_bold'>1</span> <?= __d('gl', "目的を決める") ?>
            </div>
            <?=
            $this->Html->link(__d('gl', "変更する"), "#",
                              [
                                  'class'     => 'btn btn-link btn_white goal-add-edit-button pull-right bd-radius_4px',
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
                    'class'     => 'form-control addteam_input-design disabled'
                ],
                'class'         => 'form-horizontal',
                'novalidate'    => true,
                'url'           => $url,
                'type'          => 'file',
                'id'            => 'AddGoalFormPurpose',
            ]); ?>
            <?
            if (isset($this->request->data['Purpose']['id'])) {
                echo $this->Form->hidden('Purpose.id', ['value' => $this->request->data['Purpose']['id']]);
            }
            ?>
            <div class="panel-body add-team-panel-body goal-set-body">
                <?=
                $this->Form->input('Purpose.name',
                                   ['before'                   => '<div class="col col-sm-3 control-label">' .
                                       '<label class="no-asterisk">' . __d('gl', "目的") . '</label>' .
                                       '<div class="label-addiction">' . __d('gl', "達成したいことは？") . '</div></div>',
                                    'label'                    => false,
                                    'placeholder'              => __d('gl', "達成したいことをざっくり書く"),
                                    'rows'                     => 1,
                                    "data-bv-notempty-message" => __d('validate', "入力必須項目です。"),
                                    'afterInput'               => '<span class="help-block goal-form-addiction">' . __d('gl',
                                                                                                                        "例）新しい価値を人々に提供する") . '</span>'
                                   ]) ?>
            </div>

            <div class="panel-footer addteam_pannel-footer goalset_pannel-footer">
                <div class="row">
                    <div class="pull-right">
                        <?=
                        $this->Html->link(__d('gl', "詳しくはこちら"),
                                          ['controller' => 'helps', 'action' => 'ajax_get_modal', HelpsController::TYPE_CREATE_GOAL_STEP01],
                                          ['class' => 'btn btn-link btn-lightGray bd-radius_4px modal-ajax-get', 'div' => false]) ?>
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
            <div class="pull-left goal-set-title"><span class='font_bold'>2</span> <?= __d('gl', "基準を定める") ?>
            </div>
            <?=
            $this->Html->link(__d('gl', "変更する"), "#",
                              [
                                  'class'     => 'btn btn-link btn_white goal-add-edit-button pull-right bd-radius_4px',
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
            <?if (isset($this->request->params['named']['purpose_id'])) {
                echo $this->Form->hidden('purpose_id', ['value' => $this->request->params['named']['purpose_id']]);
            }
            ?>
            <div class="panel-body add-team-panel-body">
                <?=
                $this->Form->input('goal_category_id', [
                    'label'   => [
                        'text'  => __d('gl', "カテゴリ"),
                        'class' => 'col col-sm-3 control-label'
                    ],
                    'type'    => 'select',
                    'options' => $goal_category_list,
                ]) ?>
                <?=
                $this->Form->input('name',
                                   ['before'                   => '<div class="col col-sm-3 control-label set-goal">' .
                                       '<label class="no-asterisk">' . __d('gl', "ゴール名") . '</label>' .
                                       '<div class="label-addiction">' . __d('gl',
                                                                             "達成の指標として<br>『なに』をどうするか？") . '</div></div>',
                                    'label'                    => false,
                                    'placeholder'              => __d('gl', "具体的に絞り込んで書く"),
                                    "data-bv-notempty-message" => __d('validate', "入力必須項目です。"),
                                    'required'                 => true,
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
                        $this->Form->input('value_unit',
                                           ['label'               => __d('gl', "単位"),
                                            'wrapInput'           => 'col col-sm-9 pl_5px',
                                            'type'                => 'select',
                                            'class'               => 'change-select-target-hidden form-control addteam_input-design',
                                            'target-id'           => 'KeyResult0ValueInputWrap',
                                            'required'            => true,
                                            'hidden-option-value' => KeyResult::UNIT_BINARY,
                                            'options'             => $kr_value_unit_list
                                           ]) ?>
                        <div id="KeyResult0ValueInputWrap"
                             style="<?=
                             isset($this->request->data['Goal']['value_unit'])
                             && $this->request->data['Goal']['value_unit'] == KeyResult::UNIT_BINARY ? 'display:none;' : null ?>">

                            <?=
                            $this->Form->input('target_value',
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
                            $this->Form->input('start_value',
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

                            <div class="input-group date pl_5px goal-set-date">
                                <?=
                                $this->Form->input('end_date',
                                                   [
                                                       'value'                    => $goal_end_date_format,
                                                       'default'                  => $goal_end_date_format,
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
                        <div class="form-group" id="KeyResult0StartDateContainer">
                            <label for="KeyResult0StartDate" class="col col-sm-3 control-label goal-set-mid-label"><?=
                                __d('gl', "開始") ?></label>

                            <p class="form-control-static"
                               id="KeyResult0StartDateDefault">
                                    <span
                                        class="plr_18px"><?=
                                        $goal_start_date_format ?><?=
                                        !isset($this->request->data['KeyResult'][0]) ? __d('gl',
                                                                                           "（本日）") : null ?>
                                        &nbsp;&nbsp;<a href="#" class="target-show-target-del"
                                                       show-target-id="KeyResult0StartDateInputWrap"
                                                       delete-target-id="KeyResult0StartDateDefault"><?=
                                            __d('gl',
                                                "変更") ?></a></span>
                            </p>

                            <div class="input-group date plr_5px goal-set-date" style="display: none"
                                 id="KeyResult0StartDateInputWrap">
                                <?=
                                $this->Form->input('start_date',
                                                   [
                                                       'value'                    => $goal_start_date_format,
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
            <div class="panel-footer addteam_pannel-footer goalset_pannel-footer">
                <div class="row">
                    <div class="pull-right">
                        <?=
                        $this->Html->link(__d('gl', "詳しくはこちら"),
                                          ['controller' => 'helps', 'action' => 'ajax_get_modal', HelpsController::TYPE_CREATE_GOAL_STEP02],
                                          ['class' => 'btn btn-link btn-lightGray bd-radius_4px modal-ajax-get', 'div' => false]) ?>
                        <?if (isset($this->request->data['KeyResult'][0])) {
                            $disabled = false;
                        }
                        else {
                            $disabled = true;
                        }
                        ?>
                        <?=
                        $this->Form->submit(__d('gl', "次のステップ"),
                                            array_merge(['class' => 'btn btn-primary', 'div' => false],
                                                        $disabled ? ['disabled' => 'disabled'] : [])) ?>
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
            <div class="pull-left goal-set-title"><span class='font_bold'>3</span> <?= __d('gl', "情報を追加する") ?>
            </div>
            <?=
            $this->Html->link(__d('gl', "変更する"), "#",
                              [
                                  'class'     => 'btn btn-link btn_white goal-add-edit-button pull-right bd-radius_4px',
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
                'id'            => 'AddGoalFormOther',
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
                <?
                if (isset($this->request->data['Collaborator'][0]['id'])) {
                    echo $this->Form->hidden('Collaborator.0.id',
                                             ['value' => $this->request->data['Collaborator'][0]['id']]);
                }
                ?>
                <?=
                $this->Form->input('Collaborator.0.priority', [
                    'before'   => '<div class="col col-sm-3 control-label set-importance">' .
                        '<label>' . __d('gl', "重要度") . '</label>' .
                        '<div class="label-addiction">' . __d('gl', "あなたにとっての<br>このゴールの重要度") . '</div></div>',
                    'label'    => false,
                    'type'     => 'select',
                    'default'  => 3,
                    'required' => false,
                    'style'    => 'width:130px',
                    'options'  => $priority_list,
                ]) ?>
                <div class="form-group">
                    <div class="col col-sm-3 control-label">
                        <label for=""><?= __d('gl', "ゴール画像") ?></label>

                        <div class="label-addiction pull-sm-right"><?= __d('gl', "イメージに合った画像を追加しましょう") ?></div>
                    </div>
                    <div class="col col-sm-6 line-vertical-sm goal-set-input">
                        <div class="fileinput_small fileinput-new" data-provides="fileinput">
                            <div class="fileinput-preview thumbnail nailthumb-container photo-design"
                                 data-trigger="fileinput"
                                 style="width: 96px; height: 96px;">
                                <?
                                if (isset($this->request->data['Goal']['photo_file_name']) && !empty($this->request->data['Goal']['photo_file_name'])) {
                                    echo $this->Upload->uploadImage($this->request->data, 'Goal.photo',
                                                                    ['style' => 'x_large']);
                                }
                                ?>
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
                                                       ]) ?>
                                </span>
                                <span class="help-block disp_ib font_11px"><?= __d('gl', '10MB以下') ?></span>
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
                    <div class="pull-right">
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
        },
        fields: {
            "data[Goal][start_date]": {
                validators: {
                    callback: {
                        message: "<?=__d('gl',"開始日が期限を過ぎています。")?>",
                        callback: function (value, validator) {
                            var m = new moment(value, 'YYYY/MM/DD', true);
                            return m.isBefore($('[name="data[Goal][end_date]"]').val());
                        }
                    }
                }
            },
            "data[Goal][end_date]": {
                validators: {
                    callback: {
                        message: "<?=__d('gl',"期限が開始日以前になっています。")?>",
                        callback: function (value, validator) {
                            var m = new moment(value, 'YYYY/MM/DD', true);
                            return m.isAfter($('[name="data[Goal][start_date]"]').val());
                        }
                    }
                }
            }
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
        todayHighlight: true,
        startDate: "<?=$goal_start_date_limit_format?>",
        endDate: "<?=$goal_end_date_limit_format?>"
    })
        .on('hide', function (e) {
            $("#AddGoalFormKeyResult").bootstrapValidator('revalidateField', "data[Goal][start_date]");
            $("#AddGoalFormKeyResult").bootstrapValidator('revalidateField', "data[Goal][end_date]");
        });
    //noinspection JSJQueryEfficiency
    $('#KeyResult0EndDateContainer .input-group.date').datepicker({
        format: "yyyy/mm/dd",
        todayBtn: 'linked',
        language: "ja",
        autoclose: true,
        todayHighlight: true,
        startDate: "<?=$goal_start_date_limit_format?>",
        endDate: "<?=$goal_end_date_limit_format?>"
    })
        .on('hide', function (e) {
            $("#AddGoalFormKeyResult").bootstrapValidator('revalidateField', "data[Goal][end_date]");
            $("#AddGoalFormKeyResult").bootstrapValidator('revalidateField', "data[Goal][start_date]");
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
