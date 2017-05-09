<?= $this->App->viewStartComment() ?>
<?php
$isTkr = Hash::get($this->request->data, 'KeyResult.tkr_flg');
?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close font_33px close-design" data-dismiss="modal" aria-hidden="true"><span
                    class="close-icon">&times;</span></button>
            <h4 class="modal-title"><?= $isTkr ? __("Update Top Key Result") : __("Update Key Result") ?></h4>
        </div>
        <?=
        $this->Form->create('KeyResult', [
            'inputDefaults' => [
                'div'       => 'form-group',
                'label'     => [
                    'class' => 'control-label no-asterisk text-align_left'
                ],
                'wrapInput' => 'goal-set-input',
                'class'     => 'form-control addteam_input-design'
            ],
            'class'         => 'foooo',
            'url'           => ['controller' => 'goals', 'action' => 'edit_key_result', 'key_result_id' => $kr_id],
            'novalidate'    => true,
            'id'            => 'KrEditForm',
        ]); ?>
        <?= $this->Form->hidden('KeyResult.id', ['value' => $kr_id]) ?>
        <?= $this->Form->hidden('KeyResult.goal_id', ['value' => $this->request->data['KeyResult']['goal_id']]) ?>
        <div class="modal-body modal-circle-body">
            <ul class="add-key-result-goal-info">
                <li>
                    <i class="fa fa-flag"></i><?= h($goal['Goal']['name']) ?>
                </li>
                <li>
                    <i class="fa fa-calendar"></i>
                    <?= AppUtil::dateYmdReformat($goal['Goal']['end_date'], "/") ?>
                    (← <?= AppUtil::dateYmdReformat($goal['Goal']['start_date'], "/") ?> - )
                    <?php if ($this->Session->read('Auth.User.timezone') != $goal_term['timezone']): ?>
                        <?= $this->TimeEx->getTimezoneText($goal_term['timezone']); ?>
                    <?php endif ?>
                </li>
            </ul>
            <div class="row">
                <?php
                $nameLabel = $isTkr ? __("Top Key Result name") : __("KR name");
                echo $this->Form->input('KeyResult.name',
                    [
                        'type'                         => 'text',
                        'before'                       => '<div class="set-goal">' .
                            '<h5 class="modal-key-result-headings">' . $nameLabel . '<span class="modal-key-result-headings-description">' . __(
                                "What is set as an indicator of achievement?") . '</span></label></div>',
                        'label'                        => false,
                        'placeholder'                  => __("Write in details"),
                        "data-bv-notempty-message"     => __("Input is required."),
                        'data-bv-stringlength'         => 'true',
                        'data-bv-stringlength-max'     => 200,
                        'maxlength'                    => 200,
                        'data-bv-stringlength-message' => __("It's over limit characters (%s).", 200),
                        'afterInput'                   => '<span class="help-block font_12px">' . __(
                                "eg) Increasing the internal market share of A") . '</span>'
                    ]) ?>
            </div>
            <div class="row">
                <h5 class="modal-key-result-headings"><?= __("Measurement type") ?><span
                        class="modal-key-result-headings-description"><?= __("How much?") ?></span></h5>
                <h6 class="modal-key-result-headings mod-small"><?= __("Unit,start/target value") ?></h6>
                <div class="warning js-show-warning-unit-change" style="display: none;">
                    <?= __("If you change the unit, all progress of KR will be reset.") ?>
                </div>
                <div class="goal-set-input js-progress-block">
                    <div id="KeyResult0ValueInputWrap_<?= $kr_id ?>">
                        <div class="goals-create-layout-flex">
                            <div class="relative">
                                <div class="goals-create-input-form-unit-box is-radius">
                                    <?php $unit = Hash::get($this->request->data, 'KeyResult.value_unit'); ?>
                                    <?=
                                    $this->Form->input('KeyResult.value_unit',
                                        [
                                            'label'            => false,
                                            'wrapInput'        => 'modal-add-kr-change-unit-wrap',
                                            'type'             => 'select',
                                            'required'         => true,
                                            'class'            => 'form-control goals-create-input-form mod-select-units js-select-value-unit',
                                            'options'          => $krValueUnitList,
                                            'data-short_units' => json_encode($krShortValueUnitList),
                                            'value'            => $unit,
                                        ]) ?>
                                </div>
                                <span
                                    class="goals-create-input-form-unit-label js-display-short-unit"><?= $krShortValueUnitList[KeyResult::UNIT_PERCENT] ?></span>
                            </div>
                            <div class="goals-create-layout-flex mod-child js-unit-values"
                                 style="<?= $this->request->data['KeyResult']['value_unit'] == KeyResult::UNIT_BINARY ? "display:none;" : null ?>">
                                <?=
                                $this->Form->input('KeyResult.start_value',
                                    [
                                        'label'       => false,
                                        'type'        => 'number',
                                        'default'     => 0,
                                        'required'    => true,
                                        'class'       => 'form-control goals-create-input-form goals-create-input-form-tkr-range js-start-value',
                                        'placeholder' => Hash::get($this->request->data, 'KeyResult.start_value'),
                                        'disabled'    => 'disabled'
                                    ]) ?>
                                <span class="goals-create-input-form-tkr-range-symbol">
                                <i class="fa fa-long-arrow-right" aria-hidden="true"></i>
                              </span>
                                <?=
                                $this->Form->input('KeyResult.target_value',
                                    [
                                        'label'       => false,
                                        'type'        => 'number',
                                        'step'        => '0.1',
                                        'default'     => 100,
                                        'required'    => true,
                                        'class'       => 'form-control goals-create-input-form goals-create-input-form-tkr-range',
                                        'placeholder' => Hash::get($this->request->data,
                                            'KeyResult.target_value'),
                                    ]) ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="js-unit-values"
                     <?php if ($unit == KeyResult::UNIT_BINARY): ?>style="display: none;"<?php endif; ?> >
                    <h6 class="modal-key-result-headings mod-small"><?= __("Current") ?></h6>
                    <?php
                    echo $this->Form->input('KeyResult.current_value',
                        [
                            'label'       => false,
                            'type'        => 'number',
                            'default'     => 0,
                            'required'    => true,
                            'class'       => 'form-control goals-create-input-form',
                            'placeholder' => Hash::get($this->request->data,
                                'KeyResult.current_value'),
                        ]) ?>
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
                                'maxlength'   => 2000,
                            ]) ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <h5 class="modal-key-result-headings"><?= __("Term") ?>
                    <?php if ($this->Session->read('Auth.User.timezone') != $goal_term['timezone']): ?>
                        <span class="modal-key-result-headings-description">
                            <?= $this->TimeEx->getTimezoneText($goal_term['timezone']); ?>
                            </span>
                    <?php endif ?>
                </h5>
                <div class="mb_4px">
                    <span
                        class="help-block font_12px"><?= __("Please input start date / end date of KR between start date / end date of the goal.") ?></span>
                </div>

                <div class="goal-set-input">
                    <div class="form-group" id="KeyResult0EndDateContainer">
                        <div>
                            <label for="KeyResult0EndDate"
                                   class="control-label goal-set-mid-label"><?= __("Due Date") ?></label>
                        </div>
                        <div class="input-group date goal-set-date"
                             data-date-end-date="<?= $limit_end_date ?>"
                             data-date-start-date="<?= $limit_start_date ?>">
                            <?=
                            $this->Form->input('KeyResult.end_date',
                                [
                                    'value'                        => $kr_end_date_format,
                                    'default'                      => $kr_end_date_format,
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
                        <label for="KeyResult0StartDate" class="control-label goal-set-mid-label"><?=
                            __("Start") ?></label>

                        <p class="form-control-static"
                           id="KeyResult0StartDateDefault_<?= $kr_id ?>">
                                    <span
                                        class="pull-left"><?= $kr_start_date_format ?>
                                        &nbsp;&nbsp;<a href="#" class="target-show-target-del"
                                                       show-target-id="KeyResult0StartDateInputWrap_<?= $kr_id ?>"
                                                       delete-target-id="KeyResult0StartDateDefault_<?= $kr_id ?>">
                                            <?= __("Change") ?></a>
                                    </span>
                        </p>

                        <div class="input-group date plr_5px goal-set-date none"
                             data-date-end-date="<?= $limit_end_date ?>"
                             data-date-start-date="<?= $limit_start_date ?>"
                             id="KeyResult0StartDateInputWrap_<?= $kr_id ?>">
                            <?=
                            $this->Form->input('KeyResult.start_date',
                                [
                                    'value'                        => $kr_start_date_format,
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
                            <span class="input-group-addon bd-r-radius_4px"><i class="fa fa-th"></i></span>
                        </div>
                    </div>
                </div>
            </div>
            <?php if (!$isTkr): ?>
                <div class="row">
                    <?=
                    $this->Form->input('priority', [
                        'before'                   => '<h5 class="modal-key-result-headings">' . __(
                                "Weight") . '<span class="modal-key-result-headings-description">' . __(
                                "Weight of Key Result for the Goal") . '</span></h5>',
                        'label'                    => false,
                        'type'                     => 'select',
                        'default'                  => 1,
                        'required'                 => true,
                        "data-bv-notempty-message" => __("Input is required."),
                        'style'                    => 'width:170px',
                        'options'                  => $kr_priority_list,
                        'wrapInput'                => 'modal-edit-kr-set-importance-wrap'
                    ]) ?>
                </div>
            <?php endif; ?>
        </div>
        <div class="modal-footer">
            <?php
            $button_value = __("Update Key Result");
            if ($isTkr) {
                if ($is_approvable) {
                    $button_value = __("Save & Reapply");
                } else {
                    $button_value = __("Update Top Key Result");
                }
            }
            ?>
            <?=
            $this->Form->submit($button_value,
                ['class' => 'btn btn-primary', 'div' => false]) ?>

            <button type="button" class="btn btn-default" data-dismiss="modal"><?= __("Close") ?></button>
        </div>
        <?= $this->Form->end() ?>
    </div>
</div>
<?= $this->App->viewEndComment() ?>
