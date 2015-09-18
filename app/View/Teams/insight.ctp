<?php
/**
 * @var $team_list
 * @var $group_list
 * @var $prev_week
 * @var $prev_month
 */
?>
<!-- START app/View/Teams/insight.ctp -->
<div class="col-sm-8 col-sm-offset-2">
    <div class="panel panel-default" style="padding:20px;">
        <?= $this->Form->create('TeamInsight', [
            'url'           => ['controller' => 'teams',
                                'action'     => 'insight'],
            'inputDefaults' => [
                'div'       => 'form-group',
                'label'     => false,
                'wrapInput' => '',
                'class'     => 'form-control',
            ],
            'id'            => 'InsightForm',
            'type'          => 'get',
        ]); ?>
        <?= $this->element('Team/insight_form_input', ['use' => ['team', 'date_range', 'group', 'timezone']]) ?>
        <?= $this->Form->end() ?>

        <div id="InsightResult" class="mt_18px"></div>

    </div>
</div>
<!-- END app/View/Teams/insight.ctp -->
