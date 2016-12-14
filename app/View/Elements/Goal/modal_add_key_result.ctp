<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 6/6/14
 * Time: 3:19 PM
 *
 * @var CodeCompletionView $this
 * @var                    $goalId
 * @var                    $goal
 * @var                    $goal_category_list
 * @var                    $priority_list
 * @var                    $krPriorityList
 * @var                    $krValueUnitList
 * @var                    $krStartDateFormat
 * @var                    $krEndDateFormat
 * @var                    $limitEndDate
 * @var                    $limitStartDate
 * @var                    $currentKrId
 * @var                    $goalTerm
 */
?>
<?= $this->App->viewStartComment() ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close font_33px close-design" data-dismiss="modal" aria-hidden="true"><span
                    class="close-icon">&times;</span></button>
            <h4 class="modal-title"><?= __("Add Key Result") ?></h4>
        </div>
        <?=
        $this->Form->create('KeyResult', [
            'inputDefaults' => [
                'div'       => 'form-group',
                'label'     => [
                    'class' => 'no-asterisk'
                ],
                'wrapInput' => 'goal-set-input',
                'class'     => 'form-control addteam_input-design'
            ],
            'class'         => 'form-horizontal',
            'url'           => [
                'controller'    => 'goals',
                'action'        => 'add_key_result',
                'goal_id'       => $goalId,
                'key_result_id' => $currentKrId
            ],
            'novalidate'    => true,
            'id'            => 'AddGoalFormKeyResult',
        ]); ?>
        <div class="modal-body modal-circle-body">
            <ul class="add-key-result-goal-info">
                <li>
                    <i class="fa fa-flag"></i><?= h($goal['Goal']['name']) ?>
                </li>
                <li>
                    <i class="fa fa-bullseye"></i>
                    <?= h($goal['KeyResult']['target_value']) ?>
                    (← <?= h($goal['KeyResult']['start_value']) ?>)<?= $krValueUnitList[$goal['KeyResult']['value_unit']] ?>
                </li>
                <li>
                    <i class="fa fa-calendar"></i>
                    <?= date('Y/m/d', $goal['Goal']['end_date'] + $goalTerm['timezone'] * HOUR) ?>
                    (← <?= date('Y/m/d', $goal['Goal']['start_date'] + $goalTerm['timezone'] * HOUR) ?> - )
                    <?php if ($this->Session->read('Auth.User.timezone') != $goalTerm['timezone']): ?>
                        <?= $this->TimeEx->getTimezoneText($goalTerm['timezone']); ?>
                    <?php endif ?>
                </li>
            </ul>
            <div class="row">
                <?=
                $this->Form->input('KeyResult.name',
                    [
                        'before'                       => '<div class="set-goal">' .
                            '<h5 class="modal-key-result-headings">' . __(
                                "Key Results") . '<span class="modal-key-result-headings-description">' . __(
                                "What is set as an indicator of achievement?") . '</span></h5></div>',
                        'label'                        => false,
                        'placeholder'                  => __("Write in details"),
                        "data-bv-notempty-message"     => __("Input is required."),
                        'data-bv-stringlength'         => 'true',
                        'data-bv-stringlength-max'     => 200,
                        'data-bv-stringlength-message' => __("It's over limit characters (%s).", 200),
                        'rows'                         => 1,
                        'afterInput'                   => '<span class="help-block font_12px">' . __(
                                "eg) Complete making the web site.") . '</span>'
                    ]) ?>
            </div>
            <div class="row">
                <div>
                    <h5 class="modal-key-result-headings"><?= __("Measurement") ?><span
                            class="modal-key-result-headings-description"><?= __("How much?") ?></span></h5>
                </div>
                <div class=" goal-set-input">
                    <div>

                        <?=
                        $this->Form->input('KeyResult.value_unit',
                            [
                                'label'               => __("Unit"),
                                'wrapInput'           => 'modal-add-kr-change-unit-wrap',
                                'type'                => 'select',
                                'class'               => 'change-select-target-hidden form-control addteam_input-design',
                                'target-id'           => 'KeyResult0ValueInputWrap_' . $goalId,
                                'required'            => true,
                                'hidden-option-value' => KeyResult::UNIT_BINARY,
                                'options'             => $krValueUnitList
                            ]) ?>
                    </div>
                    <div id="KeyResult0ValueInputWrap_<?= $goalId ?>" style="">
                        <div>
                            <?=
                            $this->Form->input('KeyResult.target_value',
                                [
                                    'label'                        => __("Achieve point"),
                                    'wrapInput'                    => 'fff',
                                    'type'                         => 'number',
                                    'step'                         => '0.1',
                                    'default'                      => 100,
                                    'required'                     => true,
                                    'data-bv-stringlength'         => 'true',
                                    'data-bv-stringlength-max'     => KeyResult::MAX_LENGTH_VALUE,
                                    'data-bv-stringlength-message' => __("It's over limit characters (%s).",
                                        KeyResult::MAX_LENGTH_VALUE),
                                    "data-bv-notempty-message"     => __("Input is required."),
                                    'data-bv-numeric-message'      => __("Please enter a number."),
                                ]) ?>
                        </div>
                        <div>
                            <?=
                            $this->Form->input('KeyResult.start_value',
                                [
                                    'label'                        => __("Initial point"),
                                    'wrapInput'                    => 'ggg',
                                    'type'                         => 'number',
                                    'step'                         => '0.1',
                                    'default'                      => 0,
                                    'required'                     => true,
                                    'data-bv-stringlength'         => 'true',
                                    'data-bv-stringlength-max'     => KeyResult::MAX_LENGTH_VALUE,
                                    'data-bv-stringlength-message' => __("It's over limit characters (%s).",
                                        KeyResult::MAX_LENGTH_VALUE),
                                    "data-bv-notempty-message"     => __("Input is required."),
                                    'data-bv-numeric-message'      => __("Please enter a number."),
                                ]) ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div>
                    <h5 class="modal-key-result-headings"><?= __("Description") ?><span
                            class="modal-key-result-headings-description"><?= __("") ?></span></h5>
                </div>
                <div class=" goal-set-input">
                    <div>
                        <?=
                        $this->Form->input('KeyResult.description',
                            [
                                'label'       => false,
                                'placeholder' => __("Optional"),
                                'rows'        => 3,
                            ]) ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <h5 class="modal-key-result-headings"><?= __("Term") ?>
                    <?php if ($this->Session->read('Auth.User.timezone') != $goalTerm['timezone']): ?>
                        <span class="modal-key-result-headings-description">
                            <?= $this->TimeEx->getTimezoneText($goalTerm['timezone']); ?>
                        </span>
                    <?php endif ?>
                </h5>

                <div class=" goal-set-input">
                    <div class="form-group" id="KeyResult0EndDateContainer">
                        <label for="KeyResult0EndDate" class="control-label text-align_left"><?=
                            __(
                                "Due Date") ?></label>

                        <div class="input-group date goal-set-date"
                             data-date-end-date="<?= $limitEndDate ?>"
                             data-date-start-date="<?= $limitStartDate ?>">
                            <?=
                            $this->Form->input('KeyResult.end_date',
                                [
                                    'value'                        => $krEndDateFormat,
                                    'default'                      => $krEndDateFormat,
                                    'label'                        => false,
                                    'div'                          => false,
                                    'class'                        => "form-control",
                                    'required'                     => true,
                                    "data-bv-notempty-message"     => __("Input is required."),
                                    'data-bv-stringlength'         => 'true',
                                    'data-bv-stringlength-max'     => 10,
                                    'data-bv-stringlength-message' => __(
                                        "It's over limit characters (%s).", 10),
                                    'type'                         => 'text',
                                    'wrapInput'                    => null,
                                ]) ?>
                            <span class="input-group-addon bd-r-radius_4px"><i class="fa fa-th"></i></span>
                        </div>
                    </div>
                    <div class="form-group" id="KeyResult0StartDateContainer">
                        <label for="KeyResult0StartDate" class="control-label text-align_left"><?=
                            __("Start") ?></label>

                        <p class="form-control-static"
                           id="KeyResult0StartDateDefault_<?= $goalId ?>">
                                <span
                                    class="pull-left"><?= $krStartDateFormat ?>
                                    <?= __("(Today)") ?>
                                    &nbsp;&nbsp;<a href="#" class="target-show-target-del pull-right"
                                                   show-target-id="KeyResult0StartDateInputWrap_<?= $goalId ?>"
                                                   delete-target-id="KeyResult0StartDateDefault_<?= $goalId ?>">
                                        <?= __("Change") ?></a>
                                </span>
                        </p>

                        <div class="input-group date plr_5px goal-set-date none"
                             data-date-end-date="<?= $limitEndDate ?>"
                             data-date-start-date="<?= $limitStartDate ?>"
                             id="KeyResult0StartDateInputWrap_<?= $goalId ?>">
                            <?=
                            $this->Form->input('KeyResult.start_date',
                                [
                                    'value'                        => $krStartDateFormat,
                                    'label'                        => false,
                                    'div'                          => false,
                                    'class'                        => "form-control",
                                    'required'                     => true,
                                    "data-bv-notempty-message"     => __("Input is required."),
                                    'data-bv-stringlength'         => 'true',
                                    'data-bv-stringlength-max'     => 10,
                                    'data-bv-stringlength-message' => __(
                                        "It's over limit characters (%s).", 10),
                                    'type'                         => 'text',
                                    'wrapInput'                    => null
                                ]) ?>
                            <span class="input-group-addon"><i class="fa fa-th"></i></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <?=
                $this->Form->input('priority', [
                    'before'    => '<h5 class="modal-key-result-headings">' . __(
                            "Weight") . '<span class="modal-key-result-headings-description">' . __(
                            "Weight of Key Result for the Goal") . '</span></h5>',
                    'label'     => false,
                    'type'      => 'select',
                    'default'   => 3,
                    'required'  => false,
                    'style'     => 'width:170px',
                    'options'   => $krPriorityList,
                    'wrapInput' => 'modal-add-kr-set-importance-wrap'
                ]) ?>
            </div>
        </div>
        <div class="modal-footer">
            <?=
            $this->Form->submit(__("Add Key Result"),
                ['class' => 'btn btn-primary', 'div' => false, 'disabled' => 'disabled']) ?>

            <button type="button" class="btn btn-default" data-dismiss="modal"><?= __("Close") ?></button>
        </div>
        <?= $this->Form->end() ?>
    </div>
</div>
<?= $this->App->viewEndComment() ?>
