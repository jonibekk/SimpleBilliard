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
 * @var                    $kr_id
 * @var                    $goal_category_list
 * @var                    $priority_list
 * @var                    $kr_priority_list
 * @var                    $kr_value_unit_list
 * @var                    $kr_start_date_format
 * @var                    $kr_end_date_format
 * @var                    $limit_end_date
 * @var                    $limit_start_date
 * @var                    $goal_term
 */
?>
<?= $this->App->viewStartComment()?>
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
            'id'            => 'AddGoalFormKeyResult',
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
                    <?= date('Y/m/d', $goal['Goal']['end_date'] + $goal_term['timezone'] * HOUR) ?>
                    (← <?= date('Y/m/d', $goal['Goal']['start_date'] + $goal_term['timezone'] * HOUR) ?> - )
                    <?php if ($this->Session->read('Auth.User.timezone') != $goal_term['timezone']): ?>
                        <?= $this->TimeEx->getTimezoneText($goal_term['timezone']); ?>
                    <?php endif ?>
                </li>
            </ul>
            <div class="aaa">
                <div class="row">
                    <?php
                    $nameLabel = $isTkr ? __("Top Key Result name") : __("KR name");
                    echo $this->Form->input('KeyResult.name',
                        [
                            'type' => 'text',
                            'before'                       => '<div class="set-goal">' .
                                '<h5 class="modal-key-result-headings">' . $nameLabel . '<span class="modal-key-result-headings-description">' . __(
                                    "What is set as an indicator of achievement?") . '</span></label></div>',
                            'label'                        => false,
                            'placeholder'                  => __("Write in details"),
                            "data-bv-notempty-message"     => __("Input is required."),
                            'data-bv-stringlength'         => 'true',
                            'data-bv-stringlength-max'     => 200,
                            'maxlength'     => 200,
                            'data-bv-stringlength-message' => __("It's over limit characters (%s).", 200),
                            'afterInput'                   => '<span class="help-block font_12px">' . __(
                                    "eg) Increasing the internal market share of A") . '</span>'
                        ]) ?>
                </div>
                <div class="row">
                    <div class="ddd">
                        <h5 class="modal-key-result-headings"><?= __("Measurement type") ?><span
                                class="modal-key-result-headings-description"><?= __("How much?") ?></span></h5>
                    </div>
                    <div class="goal-set-input">
                        <div id="KeyResult0ValueInputWrap_<?= $kr_id ?>"
                             style="<?= $this->request->data['KeyResult']['value_unit'] == KeyResult::UNIT_BINARY ? "display:none;" : null ?>">
                            <div class="goals-create-layout-flex">
                                <div class="relative">
                                    <div class="goals-create-input-form-unit-box">
                                        <?=
                                        $this->Form->input('KeyResult.value_unit',
                                            [
                                                'label'               => false,
                                                'wrapInput'           => 'modal-edit-kr-change-unit',
                                                'type'                => 'select',
                                                'class'               => 'form-control goals-create-input-form mod-select-units',
                                                'target-id'           => 'KeyResult0ValueInputWrap_' . $kr_id,
                                                'required'            => true,
                                                'hidden-option-value' => KeyResult::UNIT_BINARY,
                                                'options'             => $kr_value_unit_list
                                            ]) ?>
                                    </div>
                                    <span class="goals-create-input-form-unit-label"></span>
                                </div>
                                <div class="goals-create-layout-flex mod-child">
                                    <?=
                                    $this->Form->input(null,
                                        [
                                            'name'     => null,
                                            'label'    => false,
                                            'class'    => 'form-control goals-create-input-form goals-create-input-form-tkr-range disabled',
                                            'value'    => Hash::get($this->request->data, 'KeyResult.start_value'),
                                            'disabled' => 'disabled'
                                        ]) ?>
                                  <span class="goals-create-input-form-tkr-range-symbol">
                                    <i class="fa fa-long-arrow-right" aria-hidden="true"></i>
                                  </span>
                                  <?=
                                  $this->Form->input('KeyResult.target_value',
                                      [
                                          'label'                        => false,
                                          'wrapInput'                    => 'hhh',
                                          'type'                         => 'number',
                                          'step'                         => '0.1',
                                          'default'                      => 100,
                                          'required'                     => true,
                                          'class'                        => 'form-control goals-create-input-form goals-create-input-form-tkr-range',
                                          'data-bv-stringlength'         => 'true',
                                          'data-bv-stringlength-max'     => KeyResult::MAX_LENGTH_VALUE,
                                          'data-bv-stringlength-message' => __(
                                              "It's over limit characters (%s).", KeyResult::MAX_LENGTH_VALUE),
                                          "data-bv-notempty-message"     => __("Input is required."),
                                          'data-bv-numeric-message'      => __("Please enter a number."),
                                      ]) ?>
                                </div>
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
                                    'label'                        => false,
                                    'placeholder'                  => __("Optional"),
                                    'rows'                         => 3,
                                    'maxlength'     => 2000,
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

                    <div class="goal-set-input">
                        <div class="form-group" id="KeyResult0EndDateContainer">
                            <label for="KeyResult0EndDate" class="control-label goal-set-mid-label"><?=
                                __("Due Date") ?></label>

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
            </div>
            <?php if(!$isTkr): ?>
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
            if($isTkr) {
                if($is_approvable) {
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
<?= $this->App->viewEndComment()?>
