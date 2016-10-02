<?php /**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 6/11/14
 * Time: 11:40 AM
 *
 * @var CodeCompletionView $this
 * @var                    $border_months_options
 * @var                    $start_term_month_options
 * @var                    $current_eval_is_started
 * @var                    $previous_term_start_date
 * @var                    $previous_term_end_date
 * @var                    $previous_term_timezone
 * @var                    $current_term_start_date
 * @var                    $current_term_end_date
 * @var                    $current_term_timezone
 * @var                    $next_term_start_date
 * @var                    $next_term_end_date
 * @var                    $next_term_timezone
 * @var                    $timezones
 */
?>
<?= $this->App->viewStartComment()?>
<div class="panel panel-default">
    <div class="panel-heading"><?= __("Term settings") ?></div>
    <?=
    $this->Form->create('Team', [
        'inputDefaults' => [
            'div'       => 'form-group',
            'label'     => [
                'class' => 'col col-sm-3 control-label form-label'
            ],
            'wrapInput' => 'col col-sm-6',
            'class'     => 'form-control addteam_input-design'
        ],
        'class'         => 'form-horizontal',
        'novalidate'    => true,
        'type'          => 'file',
        'id'            => 'EditTermForm',
        'url'           => ['action' => 'edit_term']
    ]); ?>
    <div class="panel-body add-team-panel-body">
        <?php
        $disabled = null;
        if ($current_eval_is_started) {
            $disabled = 'disabled';
        }
        ?>
        <div class="form-group">
            <label class="col col-sm-3 control-label form-label"><?= __('Items for change') ?></label>

            <div class="col col-sm-6">
                <?=
                $this->Form->input('change_from',
                    [
                        'id'      => "EditTermChangeFrom",
                        'type'    => 'radio',
                        'legend'  => false,
                        'options' => Team::$OPTION_CHANGE_TERM,
                        'default' => Team::OPTION_CHANGE_TERM_FROM_NEXT,
                        'class'   => 'radio-inline',
                        $disabled => $disabled,
                    ])
                ?>
                <?php if ($disabled): ?>
                    <span
                        class="help-block font_11px"><?= __("Current term can't be changed during the evaluation period.") ?></span>
                <?php endif; ?>
            </div>
        </div>
        <?=
        $this->Form->input('timezone', [
            'id'                       => 'EditTermTimezone',
            'label'                    => __("Timezone"),
            'type'                     => 'select',
            'options'                  => $timezones,
            'required'                 => true,
            "data-bv-notempty-message" => __("Please select"),
        ])
        ?>
        <?=
        $this->Form->input('start_term_month', [
            'id'                       => "EditTermStartTerm",
            'label'                    => __("Start date"),
            'type'                     => 'select',
            "data-bv-notempty-message" => __("Please select"),
            'options'                  => $start_term_month_options,
            'wrapInput'                => 'team-setting-term-begining',
            'afterInput'               => '<span class="help-block font_11px">'
                . __("Please select the standard start month.")
                . '</span>'
        ]) ?>
        <?=
        $this->Form->input('border_months', [
            'id'                       => "EditTermBorderMonths",
            'label'                    => __("Term"),
            'type'                     => 'select',
            "data-bv-notempty-message" => __("Please select"),
            'options'                  => $border_months_options,
            'wrapInput'                => 'team-setting-term-span',
        ]) ?>
        <?php if ($previous_term_start_date && $previous_term_end_date): ?>
            <div class="form-group">
                <label class="col col-sm-3 control-label form-label"><?= __("Previous term before changed") ?></label>

                <div class="col col-sm-6">
                    <p class="form-control-static" id="">
                        <?= $this->TimeEx->date($previous_term_start_date, $previous_term_timezone) ?>
                        - <?= $this->TimeEx->date($previous_term_end_date, $previous_term_timezone) ?>
                        <?= $this->TimeEx->getTimezoneText($previous_term_timezone) ?>
                    </p>
                </div>
            </div>
        <?php endif; ?>
        <?php if ($current_term_start_date && $current_term_end_date): ?>
            <div class="form-group">
                <label class="col col-sm-3 control-label form-label"><?= __("Current term before changed") ?></label>

                <div class="col col-sm-6">
                    <p class="form-control-static" id="">
                        <?= $this->TimeEx->date($current_term_start_date, $current_term_timezone) ?>
                        - <?= $this->TimeEx->date($current_term_end_date, $current_term_timezone) ?>
                        <?= $this->TimeEx->getTimezoneText($current_term_timezone) ?>
                    </p>
                </div>
            </div>
        <?php endif; ?>
        <?php if ($next_term_start_date && $next_term_end_date): ?>
            <div class="form-group">
                <label class="col col-sm-3 control-label form-label"><?= __("Next term before changed") ?></label>

                <div class="col col-sm-6">
                    <p class="form-control-static" id="">
                        <?= $this->TimeEx->date($next_term_start_date, $next_term_timezone) ?>
                        - <?= $this->TimeEx->date($next_term_end_date,
                            $next_term_timezone) ?> <?= $this->TimeEx->getTimezoneText($next_term_timezone) ?>
                    </p>
                </div>
            </div>
        <?php endif; ?>
        <div class="form-group none" id="NewCurrentTerm">
            <label class="col col-sm-3 control-label form-label"><?= __("Current term after changed") ?></label>

            <div class="col col-sm-6">
                <p class="form-control-static font_darkRed" id=""></p>
            </div>
        </div>
        <div class="form-group none" id="NewNextTerm">
            <label class="col col-sm-3 control-label form-label"><?= __("Current term after changed") ?></label>

            <div class="col col-sm-6">
                <p class="form-control-static font_darkRed" id=""></p>
            </div>
        </div>
    </div>

    <div class="panel-footer addteam_pannel-footer">
        <div class="row">
            <div class="col-sm-9 col-sm-offset-3">
                <?=
                $this->Form->submit(__("Change term settings"),
                    ['class' => 'btn btn-primary display-inline', 'div' => false, 'disabled' => 'disabled']) ?>
            </div>
        </div>
    </div>
    <?= $this->Form->end(); ?>
</div>
<?php $this->append('script') ?>
<script type="text/javascript">
    $(document).ready(function () {

        $('[rel="tooltip"]').tooltip();

        $('#EditTermForm').bootstrapValidator({
            live: 'enabled',
            fields: {
                "data[Team][photo]": {
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
    });
</script>
<?php $this->end() ?>
<?= $this->App->viewEndComment()?>
