<?php
/**
 * @var $circle_insights
 * @var $team_list
 */
?>
<?php $this->start('sidebar'); ?>
<?= $this->element('Team/side_menu', ['active' => 'insight_circle']); ?>
<?php $this->end(); ?>
<?= $this->App->viewStartComment()?>
<div>
    <div class="panel panel-default">
        <div class="panel-body">
            <?= $this->Form->create('TeamInsight', [
                'url'           => [
                    'controller' => 'teams',
                    'action'     => 'insight_circle'
                ],
                'inputDefaults' => [
                    'div'       => 'form-group',
                    'label'     => false,
                    'wrapInput' => '',
                    'class'     => 'form-control disable-change-warning',
                ],
                'id'            => 'InsightForm',
                'type'          => 'get',
            ]); ?>
            <?= $this->element('Team/insight_form_input',
                ['use' => ['team', 'date_range', 'timezone', 'sort_logic']]) ?>
            <?= $this->Form->end() ?>

            <div id="InsightCircleResult" class="mt_18px"></div>
        </div>
    </div>
</div>
<?= $this->App->viewEndComment()?>
