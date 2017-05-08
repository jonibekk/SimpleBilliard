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
        'url'           => ['action' => 'edit_term']
    ]); ?>
    <div class="panel-body add-team-panel-body form-horizontal">
        <div class="form-group">
            <label class="col col-sm-3 control-label form-label"><?= __("来期開始月") ?></label>
            <div class="col col-sm-6">
                <?=
                $this->Form->input('start_month', [
                    'label'     => false,
                    'type'      => 'select',
                    'options'   => [
                        '2107-6' => '2107-6',
                        '2107-7' => '2107-7',
                        '2107-8' => '2107-8',
                        '2107-9' => '2107-9',
                        '2107-10' => '2107-10',
                        '2107-11' => '2107-11',
                        '2107-12' => '2107-12',
                    ],
                ])
                ?>
            </div>
        </div>
        <div class="form-group">
            <label class="col col-sm-3 control-label form-label"><?= __("評価期間") ?></label>
            <div class="col col-sm-6">
                <?=
                $this->Form->input('range_month', [
                    'label'     => false,
                    'type'      => 'select',
                    'options'   => [
                        '3' => '3ヶ月',
                        '4' => '4ヶ月',
                        '6' => '半年',
                        '12' => '1年',
                    ],
                ])
                ?>
            </div>
        </div>
    </div>
    <div class="panel-footer">
        <div class="row">
            <div class="col-xxs-4 col-sm-offset-3">
                <?=
                $this->Form->submit(__("Save"),
                    ['class' => 'btn btn-primary display-inline', 'div' => false]) ?>
            </div>
        </div>
    </div>
    <?= $this->Form->end(); ?>
</div>
<?php $this->end() ?>
<?= $this->App->viewEndComment()?>
