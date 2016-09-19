<?php
/**
 * @var $team_list
 * @var $group_list
 * @var $prev_week
 * @var $prev_month
 */
?>
<?php $this->start('sidebar'); ?>
<?= $this->element('Team/side_menu', ['active' => 'insight']); ?>
<?php $this->end(); ?>
<?= $this->App->viewStartComment()?>
<div>
    <div class="panel panel-default">
        <div class="panel-body">
            <?= $this->Form->create('TeamInsight', [
                'url'           => [
                    'controller' => 'teams',
                    'action'     => 'insight'
                ],
                'inputDefaults' => [
                    'div'       => 'form-group team-insight-wraps',
                    'label'     => false,
                    'wrapInput' => '',
                    'class'     => 'form-control disable-change-warning',
                ],
                'id'            => 'InsightForm',
                'type'          => 'get',
            ]); ?>
            <?= $this->element('Team/insight_form_input',
                ['use' => ['team', 'date_range', 'group', 'timezone', 'graph_type']]) ?>
            <?= $this->Form->end() ?>

            <div id="InsightResult" class="mt_18px"></div>
        </div>
    </div>
</div>
<?= $this->App->viewEndComment()?>
