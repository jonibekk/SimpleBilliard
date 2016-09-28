<?php
/**
 * @var $team_list
 * @var $group_list
 * @var $date_ranges
 * @var $today_time
 */
$use = isset($use) ? $use : [];

$sort_by = isset($this->request->query['sort_by']) ? $this->request->query['sort_by'] : 'post_count';
$sort_type = isset($this->request->query['sort_type']) ? $this->request->query['sort_type'] : 'desc';
$team = isset($this->request->query['team']) ? $this->request->query['team'] : 1;
$date_range = isset($this->request->query['date_range']) ? $this->request->query['date_range'] : 'current_week';
$timezone = isset($this->request->query['timezone']) ? $this->request->query['timezone'] : 9;
$group = isset($this->request->query['group']) ? $this->request->query['group'] : '';
$type = isset($this->request->query['type']) ? $this->request->query['type'] : 'action_goal_ranking';

// 月曜日に表示した時は「先週」をデフォルト選択する
if (empty($this->request->query['date_range'])) {
    $date_range = (date('w', $today_time) == 1) ? 'prev_week' : 'current_week';
}
?>
<?= $this->App->viewStartComment()?>
<?php if (in_array('team', $use)): ?>
    <?php
// システム管理者の場合
    if ($this->Session->read('Auth.User.admin_flg')): ?>
        <?= $this->Form->input('team', [
            'id'        => 'InsightInputTeam',
            'type'      => 'select',
            'options'   => $team_list,
            'selected'  => $team,
            'wrapInput' => 'circle-insight-team-select'
        ]) ?>
    <?php endif ?>
<?php endif ?>

<?php if (in_array('date_range', $use)): ?>
    <?= $this->Form->input('date_range', [
        'id'        => 'InsightInputDateRange',
        'type'      => 'select',
        'options'   => [
            'current_week'  => __('Current Week') . sprintf(" (%s - %s)",
                    str_replace('-', '/', $date_ranges['current_week']['start']),
                    str_replace('-', '/', $date_ranges['current_week']['end'])),
            'prev_week'     => __('Last Week') . sprintf(" (%s - %s)",
                    str_replace('-', '/', $date_ranges['prev_week']['start']),
                    str_replace('-', '/', $date_ranges['prev_week']['end'])),
            'current_month' => __('Current Month') . sprintf(" (%s - %s)",
                    str_replace('-', '/', $date_ranges['current_month']['start']),
                    str_replace('-', '/', $date_ranges['current_month']['end'])),
            'prev_month'    => __('Last Month') . sprintf(" (%s - %s)",
                    str_replace('-', '/', $date_ranges['prev_month']['start']),
                    str_replace('-', '/', $date_ranges['prev_month']['end'])),
            'current_term'  => __('Current Term') . sprintf(" (%s - %s)",
                    str_replace('-', '/', $date_ranges['current_term']['start']),
                    str_replace('-', '/', $date_ranges['current_term']['end'])),
            'prev_term'     => __('Previous Term') . sprintf(" (%s - %s)",
                    str_replace('-', '/', $date_ranges['prev_term']['start']),
                    str_replace('-', '/', $date_ranges['prev_term']['end'])),
        ],
        'selected'  => $date_range,
        'wrapInput' => 'team-ranking-periods'
    ]) ?>
<?php endif ?>

<?php if (in_array('group', $use)): ?>
    <?= $this->Form->input('group', [
        'id'        => 'InsightInputGroup',
        'type'      => 'select',
        'empty'     => __('All Members'),
        'options'   => $group_list,
        'selected'  => $group,
        'wrapInput' => 'team-ranking-members'
    ])
    ?>
<?php endif ?>

<?php if (in_array('ranking_type', $use)): ?>
    <?= $this->Form->input('type', [
        'id'        => 'InsightInputType',
        'type'      => 'select',
        'empty'     => false,
        'options'   => [
            'action_goal_ranking'    => __('Goal that had been actioned'),
            'action_like_ranking'    => __('Action that had been liked'),
            'action_comment_ranking' => __('Action that had been commented'),
            'action_user_ranking'    => __('Member who actioned'),
            'post_user_ranking'      => __('Member who posted'),
            'post_like_ranking'      => __('Post that had been liked'),
            'post_comment_ranking'   => __('Post that had been commented'),
        ],
        'selected'  => $type,
        'wrapInput' => 'team-ranking-types',
    ])
    ?>
<?php endif ?>

<?php if (in_array('timezone', $use)): ?>
    <?= $this->Form->input('timezone', [
        'id'        => 'InsightInputTimezone',
        'type'      => 'select',
        'empty'     => false,
        'options'   => [
            '9'   => __('(GMT+09:00) Tokyo'),
            '5.5' => __('(GMT+05:30) New Delhi'),
            '1'   => __('(GMT+01:00) Berlin'),
            '-8'  => __('(GMT-08:00) Pacific Ocean Time (USA & Canada)')
        ],
        'selected'  => $timezone,
        'wrapInput' => 'team-ranking-timezones'
    ])
    ?>
<?php endif ?>

<?php if (in_array('sort_logic', $use)): ?>
    <?= $this->Form->hidden('sort_by', array('value' => $sort_by)); ?>
    <?= $this->Form->hidden('sort_type', array('value' => $sort_type)); ?>
<?php endif ?>


<?php if (in_array('graph_type', $use)): ?>
    <div class="form-group text-align_r">
        <label class="insight-graph-icon"><i class="fa fa-area-chart"></i>&nbsp;:&nbsp; </label>
        <div class="btn-group" data-toggle="buttons" id="InsightGraphTypeButtonGroup">
            <label class="btn insight-graph-type-button" data-value="term" disabled="disabled">
                <input type="radio" name="graph_type" value="term"> <?= __('Term') ?>
            </label>
            <label class="btn insight-graph-type-button" data-value="month" disabled="disabled">
                <input type="radio" name="graph_type" value="month"> <?= __('Month') ?>
            </label>
            <label class="btn insight-graph-type-button" data-value="week" disabled="disabled">
                <input type="radio" name="graph_type" value="week"> <?= __('Week') ?>
            </label>
            <label class="btn insight-graph-type-button" data-value="day" disabled="disabled">
                <input type="radio" name="graph_type" value="day"> <?= __('Day') ?>
            </label>
        </div>
    </div>
<?php endif ?>
<?= $this->App->viewEndComment()?>
