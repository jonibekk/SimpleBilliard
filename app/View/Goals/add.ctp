<?php /**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 6/11/14
 * Time: 11:40 AM
 *
 * @var CodeCompletionView     $this
 * @var                        $goal_category_list
 * @var                        $priority_list
 * @var                        $kr_value_unit_list
 * @var                        $goal_start_date_format
 * @var                        $goal_end_date_format
 * @var                        $current_term_start_date_format
 * @var                        $current_term_end_date_format
 * @var                        $next_term_start_date_format
 * @var                        $next_term_end_date_format
 * @var                        $today_format
 * @var                        $current_term
 * @var                        $next_term
 * @var                        $purpose_count
 */
$url = isset($this->request->data['Goal']['id']) ? ['goal_id' => $this->request->data['Goal']['id']] : [];

$url = isset($this->request->params['named']['purpose_id']) ? array_merge($url,
    ['purpose_id' => $this->request->params['named']['purpose_id']]) : $url;
?>
<?= $this->App->viewStartComment()?>
<div class="row">
    <!--GoalSet01-->
    <div class="col-sm-8 col-sm-offset-2">
        <div class="page-title">
            <?= isset($this->request->data['Goal']['id']) ? __("Edit goal") : __("Create a new goal") ?>
        </div>
        <div class="panel panel-default" id="AddGoalFormPurposeWrap">
            <div class="panel-heading goal-set-heading clearfix">
                <div class="pull-left goal-set-title"><span class='font_bold'>1</span> <?= __("Decide Your Purpose") ?>
                </div>
                <?=
                $this->Html->link(__("Change"), "#",
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
                        'wrapInput' => 'col col-sm-7  goal-set-input',
                        'class'     => 'form-control addteam_input-design disabled'
                    ],
                    'class'         => 'form-horizontal',
                    'novalidate'    => true,
                    'url'           => $url,
                    'type'          => 'file',
                    'id'            => 'AddGoalFormPurpose',
                ]); ?>
                <div class="panel-body add-team-panel-body goal-set-body">
                    <?=
                    $this->Form->input('Purpose.name',
                        [
                            'before'                       => '<div class="col col-sm-3 control-label goal-edit-labels">' .
                                '<label class="no-asterisk">' . __("Purpose") . '</label>' .
                                '<div class="label-addiction">' . __("What do you want to achieve?") . '</div></div>',
                            'label'                        => false,
                            'placeholder'                  => __("Describe it roughly."),
                            'rows'                         => 1,
                            "data-bv-notempty-message"     => __("Input is required."),
                            'afterInput'                   => '<span class="help-block goal-form-addiction">' . __(
                                    "eg. Provide a new value to people.") . '</span>',
                            'data-bv-stringlength'         => 'true',
                            'data-bv-stringlength-max'     => 200,
                            'data-bv-stringlength-message' => __("It's over limit characters (%s).", 200),
                        ]) ?>
                </div>

                <div class="panel-footer addteam_pannel-footer goalset_pannel-footer">
                    <div class="row">
                        <div class="pull-right">
                            <?=
                            $this->Form->submit(__("Next Step"),
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
                <div class="pull-left goal-set-title"><span class='font_bold'>2</span> <?= __("Set Reference Values") ?>
                </div>
                <?=
                $this->Html->link(__("Change"), "#",
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
                        'wrapInput' => 'col col-sm-7  goal-set-input',
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
                    $this->Form->input('goal_category_id', [
                        'label'    => [
                            'text'  => __("Category"),
                            'class' => 'col col-sm-3 control-label goal-edit-labels'
                        ],
                        'required' => false,
                        'class'    => 'goal-add-category-select form-control',
                        'type'     => 'select',
                        'options'  => $goal_category_list,
                    ]) ?>
                    <?=
                    $this->Form->input('name',
                        [
                            'before'                       => '<div class="col col-sm-3 control-label set-goal goal-edit-labels">' .
                                '<label class="no-asterisk">' . __("Goal Name") . '</label>' .
                                '<div class="label-addiction">' . __(
                                    "What is the measurable point?") . '</div></div>',
                            'label'                        => false,
                            'placeholder'                  => __("Write in details."),
                            "data-bv-notempty-message"     => __("Input is required."),
                            'required'                     => true,
                            'rows'                         => 1,
                            'afterInput'                   => '<span class="help-block font_12px">' . __(
                                    "eg) Increasing the internal market share of A") . '</span>',
                            'data-bv-stringlength'         => 'true',
                            'data-bv-stringlength-max'     => 200,
                            'data-bv-stringlength-message' => __("It's over limit characters (%s).", 200),
                        ]) ?>
                    <div class="row goal-edit-labels">
                        <div class="col col-sm-3 goal-edit-labels">
                            <label class="control-label  width100_per text-right"><?= __("Term") ?></label>

                            <div id="SelectTermTimezone" class="label-addiction pull-right"></div>
                        </div>

                        <div class="col col-sm-7  goal-set-input">
                            <div class="form-group" id="KeyResult0EndDateContainer">
                                <label for="KeyResult0EndDate" class="col col-sm-3 control-label goal-set-mid-label"><?=
                                    __(
                                        "Due Date") ?></label>

                                <div class="input-group date pl_5px goal-set-date">
                                    <?=
                                    $this->Form->input('end_date',
                                        [
                                            'value'                        => $goal_end_date_format,
                                            'default'                      => $goal_end_date_format,
                                            'label'                        => false,
                                            'div'                          => false,
                                            'class'                        => "form-control",
                                            'required'                     => true,
                                            "data-bv-notempty-message"     => __(
                                                "Input is required."),
                                            'data-bv-stringlength'         => 'true',
                                            'data-bv-stringlength-max'     => 10,
                                            'data-bv-stringlength-message' => __(
                                                "It's over limit characters (%s).",
                                                10),
                                            'type'                         => 'text',
                                            'wrapInput'                    => null
                                        ]) ?>
                                    <span class="input-group-addon"><i class="fa fa-th"></i></span>
                                </div>
                            </div>
                            <div class="form-group" id="KeyResult0StartDateContainer">
                                <label for="KeyResult0StartDate"
                                       class="col col-sm-3 control-label goal-set-mid-label"><?=
                                    __("Start") ?></label>

                                <p class="form-control-static"
                                   id="KeyResult0StartDateDefault">
                                    <span class="plr_18px">
                                        <span class="goal-edit-limit-date-label">
                                            <?= $goal_start_date_format ?>
                                            <?= !isset($this->request->data['Goal']['start_date']) ? __("(Today)") : null ?>
                                        </span>
                                        <a href="#" class="target-show-target-del"
                                           show-target-id="KeyResult0StartDateInputWrap"
                                           delete-target-id="KeyResult0StartDateDefault">
                                            <?= __("Change") ?>
                                        </a>
                                    </span>
                                </p>

                                <div class="input-group date plr_5px goal-set-date none"
                                     id="KeyResult0StartDateInputWrap">
                                    <?=
                                    $this->Form->input('start_date',
                                        [
                                            'value'                        => $goal_start_date_format,
                                            'label'                        => false,
                                            'div'                          => false,
                                            'class'                        => "form-control",
                                            'required'                     => true,
                                            "data-bv-notempty-message"     => __(
                                                "Input is required."),
                                            'data-bv-stringlength'         => 'true',
                                            'data-bv-stringlength-max'     => 10,
                                            'data-bv-stringlength-message' => __(
                                                "It's over limit characters (%s).",
                                                10),
                                            'type'                         => 'text',
                                            'wrapInput'                    => null
                                        ]) ?>
                                    <span class="input-group-addon"><i class="fa fa-th"></i></span>
                                </div>
                            </div>
                            <div class="form-group" id="KeyResult0EvaluateTermContainer">
                                <label for="KeyResult0EvaluateTerm"
                                       class="col col-sm-3 control-label goal-set-mid-label">
                                    <?= __("Evaluation terms") ?>
                                </label>

                                <div class="col col-sm-9">
                                    <p class="form-control-static"
                                       id="KeyResult0EvaluateTermDefault">
                                        <span class="plr_18px">
                                            <span class="goal-edit-limit-date-label">
                                            <?php if (isset($this->request->data['Goal'])): ?>
                                                <?= h($this->request->data['Goal']['term_text']) ?>
                                            <?php else: ?>
                                                <?= __('Current Term') ?>
                                            <?php endif; ?>
                                            </span>
                                            <?php if (!isset($this->request->data['Goal'])): ?>
                                                <a href="#" class="target-show-target-del"
                                                   show-target-id="KeyResult0EvaluateTermInputWrap"
                                                   delete-target-id="KeyResult0EvaluateTermDefault">
                                                    <?= __("Change") ?>
                                                </a>
                                            <?php endif; ?>
                                        </span>
                                    </p>

                                    <div class="plr_5px none" id="KeyResult0EvaluateTermInputWrap">
                                        <?php
                                        if (!isset($this->request->data['Goal'])) {
                                            $input_option = [
                                                'label'     => false,
                                                'wrapInput' => null,
                                                'type'      => 'select',
                                                'class'     => 'form-control',
                                                'required'  => true,
                                                'options'   => [
                                                    'current' => __('Current Term'),
                                                    'next'    => __('Next Term'),
                                                ],
                                                'id'        => 'KeyResult0EvaluateTermSelect',
                                            ];
                                            echo $this->Form->input('term_type', $input_option);
                                        } ?>
                                        <?php
                                        // For editing next term goal
                                        if (viaIsSet($is_next_term_goal)) {
                                            echo $this->Form->hidden('term_type', ['value' => 'next']);
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel-footer addteam_pannel-footer goalset_pannel-footer">
                    <div class="row">
                        <div class="pull-right">
                            <?php if (isset($this->request->data['KeyResult'][0])) {
                                $disabled = false;
                            } else {
                                $disabled = true;
                            }
                            ?>
                            <?=
                            $this->Form->submit(__("Next Step"),
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
                <div class="pull-left goal-set-title">
                    <span class='font_bold'>3</span> <?= __("Add more information") ?>
                </div>
                <?=
                $this->Html->link(__("Change"), "#",
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
                        'wrapInput' => 'col col-sm-7  goal-set-input',
                        'class'     => 'form-control addteam_input-design'
                    ],
                    'class'         => 'form-horizontal form-feed-notify',
                    'novalidate'    => true,
                    'type'          => 'file',
                    'id'            => 'AddGoalFormOther',
                ]); ?>

                <div class="panel-body add-team-panel-body">
                    <?=
                    $this->Form->input('description',
                        [
                            'before'                       => '<div class="col col-sm-3 control-label set-detail goal-edit-labels">' .
                                '<label>' . __("Description") . '</label>' .
                                '<div class="label-addiction">' . __("Add complements") . '</div></div>',
                            'label'                        => false,
                            'placeholder'                  => __("Explain this goal in detail."),
                            'rows'                         => 1,
                            'required'                     => false,
                            'data-bv-stringlength'         => 'true',
                            'data-bv-stringlength-max'     => 2000,
                            'data-bv-stringlength-message' => __("It's over limit characters (%s).", 2000),
                        ]) ?>
                    <?php if (isset($this->request->data['Collaborator'][0]['id'])) {
                        echo $this->Form->hidden('Collaborator.0.id',
                            ['value' => $this->request->data['Collaborator'][0]['id']]);
                    }
                    ?>
                    <?=
                    $this->Form->input('Collaborator.0.priority', [
                        'before'   => '<div class="col col-sm-3 control-label set-importance goal-edit-labels">' .
                            '<label>' . __("Weight") . '</label>' .
                            '<div class="label-addiction">' . __("Weight of this goal") . '</div></div>',
                        'label'    => false,
                        'type'     => 'select',
                        'default'  => 3,
                        'required' => false,
                        'style'    => 'width:130px',
                        'options'  => $priority_list
                    ]) ?>
                    <?php $this->Form->unlockField('socket_id') ?>
                    <div class="form-group">
                        <div class="col col-sm-3 control-label goal-edit-labels">
                            <label for=""><?= __("Goal Image") ?></label>

                            <div
                                class="label-addiction pull-sm-right"><?= __("Let's add an image that motivated yourself.") ?></div>
                        </div>
                        <div class="col col-sm-6  goal-set-input">
                            <div class="fileinput_small fileinput-new" data-provides="fileinput">
                                <div class="fileinput-preview thumbnail nailthumb-container photo-design"
                                     data-trigger="fileinput"
                                     style="width: 96px; height: 96px;">
                                    <?php if (isset($this->request->data['Goal']['photo_file_name']) && !empty($this->request->data['Goal']['photo_file_name'])) {
                                        echo $this->Upload->uploadImage($this->request->data, 'Goal.photo',
                                            ['style' => 'x_large']);
                                    }
                                    ?>
                                    <i class="fa fa-plus photo-plus-large"></i>
                                </div>
                                <div>
                                <span class="btn btn-default btn-file">
                                    <span class="fileinput-new">
                                        <?= __("Select an image") ?>
                                    </span>
                                    <span class="fileinput-exists"><?= __("Reselect an image") ?></span>
                                    <?=
                                    $this->Form->input('photo',
                                        [
                                            'type'         => 'file',
                                            'label'        => false,
                                            'div'          => false,
                                            'css'          => false,
                                            'wrapInput'    => false,
                                            'errorMessage' => false,
                                            'required'     => false
                                        ]) ?>
                                </span>
                                    <span
                                        class="help-block inline-block font_11px"><?= __('Smaller than 10MB') ?></span>
                                </div>
                            </div>

                            <div class="has-error">
                                <?=
                                $this->Form->error('photo', null,
                                    [
                                        'class' => 'help-block text-danger',
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
                            $this->Form->submit(__("Save Goal"),
                                ['class' => 'btn btn-primary', 'div' => false]) ?>
                        </div>
                    </div>
                </div>
                <?= $this->Form->end(); ?>
            </div>
        </div>
    </div>
</div>
<?php $this->append('script') ?>
<script type="text/javascript">
    $(document).ready(function () {
        <?php if(viaIsSet($purpose_count) === 0):?>
        $("#ExplainGoal").trigger('click');
        <?php endif;?>
    });
    $('#AddGoalFormPurpose').bootstrapValidator({
        live: 'enabled'
    });
    $('#AddGoalFormKeyResult').bootstrapValidator({
        live: 'enabled',
        fields: {
            "data[Goal][start_date]": {
                validators: {
                    callback: {
                        message: "<?=__("Start date has expired.")?>",
                        callback: function (value, validator) {
                            var m = new moment(value, 'YYYY/MM/DD', true);
                            return m.isBefore($('[name="data[Goal][end_date]"]').val());
                        }
                    },
                    date: {
                        format: 'YYYY/MM/DD',
                        message: '<?=__("Enter such date as YYYY/MM/DD.")?>'
                    }
                }
            },
            "data[Goal][end_date]": {
                validators: {
                    callback: {
                        message: "<?=__("The limit date must be after start date.")?>",
                        callback: function (value, validator) {
                            var m = new moment(value, 'YYYY/MM/DD', true);
                            return m.isAfter($('[name="data[Goal][start_date]"]').val());
                        }
                    },
                    date: {
                        format: 'YYYY/MM/DD',
                        message: '<?=__("Enter such date as YYYY/MM/DD.")?>'
                    }
                }
            }
        }
    });
    $('#AddGoalFormOther').bootstrapValidator({
        live: 'enabled',

        fields: {
            "data[Goal][photo]": {

                validators: {
                    file: {
                        extension: 'jpeg,jpg,png,gif',
                        type: 'image/jpeg,image/png,image/gif',
                        maxSize: 10485760,   // 10mb
                        message: "<?=__("10MB or less, and Please select one of the formats of JPG or PNG and GIF.")?>"
                    }
                }
            }
        }
    });

    //noinspection JSJQueryEfficiency
    var $startDate = $('#KeyResult0StartDateContainer .input-group.date');
    $startDate.datepicker({
        format: "yyyy/mm/dd",
        todayBtn: 'linked',
        language: "ja",
        autoclose: true,
        todayHighlight: true,
        startDate: "<?=$is_next_term_goal ? $next_term_start_date_format : $current_term_start_date_format?>",
        endDate: "<?=$is_next_term_goal ? $next_term_end_date_format : $current_term_end_date_format?>"
    })
        .on('hide', function (e) {
            $("#AddGoalFormKeyResult").bootstrapValidator('revalidateField', "data[Goal][start_date]");
            $("#AddGoalFormKeyResult").bootstrapValidator('revalidateField', "data[Goal][end_date]");
        });

    //noinspection JSJQueryEfficiency
    var $endDate = $('#KeyResult0EndDateContainer .input-group.date');
    $endDate.datepicker({
        format: "yyyy/mm/dd",
        todayBtn: 'linked',
        language: "ja",
        autoclose: true,
        todayHighlight: true,
        startDate: "<?=$is_next_term_goal ? $next_term_start_date_format : $current_term_start_date_format?>",
        endDate: "<?=$is_next_term_goal ? $next_term_end_date_format : $current_term_end_date_format?>"
    })
        .on('hide', function (e) {
            $("#AddGoalFormKeyResult").bootstrapValidator('revalidateField', "data[Goal][end_date]");
            $("#AddGoalFormKeyResult").bootstrapValidator('revalidateField', "data[Goal][start_date]");
        });

    // 評価期間プルダウン表示前のテキスト表示
    var $evaluateTermSelect = $('#KeyResult0EvaluateTermSelect');

    // 評価期間のプルダウン変更時
    $evaluateTermSelect.on('change', function (e, onInit) {
        var $select = $(this);
        var $selectTermTimezone = $('#SelectTermTimezone');
        $selectTermTimezone.text('');

        // 今期を選択した場合
        if ($select.val() == 'current') {
            // カレンダーで選択可能な日付範囲をセットし直し
            $startDate
                .datepicker('setStartDate', '<?= $current_term_start_date_format ?>')
                .datepicker('setEndDate', '<?= $current_term_end_date_format ?>');
            $endDate
                .datepicker('setStartDate', '<?= $current_term_start_date_format ?>')
                .datepicker('setEndDate', '<?= $current_term_end_date_format ?>');

            // ユーザーのタイムゾーンが期間のタイムゾーンと違っていればオフセット表示
            <?php if ($this->Session->read('Auth.User.timezone') != $current_term['timezone']): ?>
            $selectTermTimezone.text('<?= $this->TimeEx->getTimezoneText($current_term['timezone']) ?>');
            <?php endif ?>

            // 入力日付を範囲に収まるように変更
            // ページ表示直後の trigger() で呼び出した時は処理しない
            if (!onInit) {
                $startDate.datepicker('setDate', '<?= $today_format ?>');
                $endDate.datepicker('setDate', '<?= $current_term_end_date_format ?>');
                $('#KeyResult0StartDateDefault').find('.goal-edit-limit-date-label').text('<?= $today_format ?>');
            }
        }
        // 来期を選択した場合
        else {
            // カレンダーで選択可能な日付範囲をセットし直し
            $startDate
                .datepicker('setStartDate', '<?= $next_term_start_date_format ?>')
                .datepicker('setEndDate', '<?= $next_term_end_date_format ?>');
            $endDate
                .datepicker('setStartDate', '<?= $next_term_start_date_format ?>')
                .datepicker('setEndDate', '<?= $next_term_end_date_format ?>');

            // ユーザーのタイムゾーンが期間のタイムゾーンと違っていればオフセット表示
            <?php if ($this->Session->read('Auth.User.timezone') != $next_term['timezone']): ?>
            $selectTermTimezone.text('<?= $this->TimeEx->getTimezoneText($next_term['timezone']) ?>');
            <?php endif ?>

            // 入力日付を範囲に収まるように変更
            // ページ表示直後の trigger() で呼び出した時は処理しない
            if (!onInit) {
                $startDate.datepicker('setDate', '<?= $next_term_start_date_format ?>');
                $endDate.datepicker('setDate', '<?= $next_term_end_date_format ?>');
                $('#KeyResult0StartDateDefault').find('.goal-edit-limit-date-label')
                    .text('<?= $next_term_start_date_format ?>');
            }
        }
    }).trigger('change', true);

    //modeによってdisableにする
    <?php if(isset($this->request->params['named']['mode'])):?>
    <?php if($this->request->params['named']['mode'] == 2):?>
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
    <?php elseif($this->request->params['named']['mode'] == 3):?>
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
    <?php endif;?>
    <?php endif;?>
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
        $(this).hide();
        return false;
    });
</script>
<?php $this->end() ?>
<?= $this->App->viewEndComment()?>
