<?php
/**
 * @var $group_list
 * @var $team_list
 * @var $text_list
 * @var $url_list
 */
?>
<!-- START app/View/Teams/insight_ranking.ctp -->
<div class="col-sm-8 col-sm-offset-2">
    <div class="panel panel-default" style="padding:20px;">
        <?= $this->Form->create('TeamInsight', [
            'url'           => ['controller' => 'teams',
                                'action'     => 'insight_ranking'],
            'inputDefaults' => [
                'div'       => 'form-group',
                'label'     => false,
                'wrapInput' => '',
                'class'     => 'form-control',
            ],
            'id'            => 'InsightForm',
            'type'          => 'get',
        ]); ?>
        <?= $this->element('Team/insight_form_input', ['use' => ['date_range', 'group', 'ranking_type', 'timezone']]) ?>
        <?= $this->Form->end() ?>

        <div id="InsightRankingResult" class="mt_18px"></div>
    </div>
</div>
<!-- END app/View/Teams/insight_ranking.ctp -->
