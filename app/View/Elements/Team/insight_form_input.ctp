<?php
/**
 * @var $team_list
 * @var $group_list
 * @var $date_ranges
 * @var $today_time
 */
$use = isset($use) ? $use : [];
?>
<!-- START app/View/Elements/Team/insight_form_input.ctp -->
<?php if (in_array('team', $use)): ?>
<?php
// システム管理者の場合
    if ($this->Session->read('Auth.User.admin_flg')): ?>
        <?= $this->Form->input('team', [
            'id'      => 'InsightInputTeam',
            'type'    => 'select',
            'options' => $team_list,
            'wrapInput' => 'circle-insight-team-select']) ?>
    <?php endif ?>
<?php endif ?>

<?php if (in_array('date_range', $use)): ?>
    <?= $this->Form->input('date_range', [
        'id'      => 'InsightInputDateRange',
        'type'    => 'select',
        // 月曜日に表示した時は「先週」をデフォルト選択する
        'value' => (date('w', $today_time) == 1) ? 'prev_week' : 'current_week',
        'options' => [
            'current_week'  => __('今週') . sprintf(" (%s - %s)",
                                                         str_replace('-', '/', $date_ranges['current_week']['start']),
                                                         str_replace('-', '/', $date_ranges['current_week']['end'])),
            'prev_week'     => __('先週') . sprintf(" (%s - %s)",
                                                         str_replace('-', '/', $date_ranges['prev_week']['start']),
                                                         str_replace('-', '/', $date_ranges['prev_week']['end'])),
            'current_month' => __('今月') . sprintf(" (%s - %s)",
                                                         str_replace('-', '/', $date_ranges['current_month']['start']),
                                                         str_replace('-', '/', $date_ranges['current_month']['end'])),
            'prev_month'    => __('先月') . sprintf(" (%s - %s)",
                                                         str_replace('-', '/', $date_ranges['prev_month']['start']),
                                                         str_replace('-', '/', $date_ranges['prev_month']['end'])),
            'current_term'  => __('Current Term') . sprintf(" (%s - %s)",
                                                         str_replace('-', '/', $date_ranges['current_term']['start']),
                                                         str_replace('-', '/', $date_ranges['current_term']['end'])),
            'prev_term'     => __('Previous Term') . sprintf(" (%s - %s)",
                                                         str_replace('-', '/', $date_ranges['prev_term']['start']),
                                                         str_replace('-', '/', $date_ranges['prev_term']['end'])),
        ],
        'wrapInput' => 'team-ranking-periods'
        ]) ?>
<?php endif ?>

<?php if (in_array('group', $use)): ?>
    <?= $this->Form->input('group', [
        'id'      => 'InsightInputGroup',
        'type'    => 'select',
        'empty'   => __('すべてのメンバー'),
        'options' => $group_list,
        'wrapInput' => 'team-ranking-members'])
    ?>
<?php endif ?>

<?php if (in_array('ranking_type', $use)): ?>
    <?= $this->Form->input('type', [
        'id'      => 'InsightInputType',
        'type'    => 'select',
        'empty'   => false,
        'options' => ['action_goal_ranking'    => __('アクションされたゴール'),
                      'action_like_ranking'    => __('いいねされたアクション'),
                      'action_comment_ranking' => __('コメントされたアクション'),
                      'action_user_ranking'    => __('アクションしたメンバー'),
                      'post_user_ranking'      => __('投稿したメンバー'),
                      'post_like_ranking'      => __('いいねされた投稿'),
                      'post_comment_ranking'   => __('コメントされた投稿'),
        ],
        'wrapInput' => 'team-ranking-types',
    ])
    ?>
<?php endif ?>

<?php if (in_array('timezone', $use)): ?>
    <?= $this->Form->input('timezone', [
        'id'      => 'InsightInputTimezone',
        'type'    => 'select',
        'empty'   => false,
        'options' => ['9'   => __('(GMT+09:00) 東京'),
                      '5.5' => __('(GMT+05:30) ニューデリー'),
                      '1'   => __('(GMT+01:00) ベルリン'),
                      '-8'  => __('(GMT-08:00) 太平洋標準時 (アメリカ & カナダ)')
        ],
        'wrapInput' => 'team-ranking-timezones'
    ])
    ?>
<?php endif ?>

<?php if (in_array('graph_type', $use)): ?>
    <div class="form-group text-align_r">
        <label class="insight-graph-icon"><i class="fa fa-area-chart"></i>&nbsp;:&nbsp; </label>
        <div class="btn-group" data-toggle="buttons" id="InsightGraphTypeButtonGroup">
            <label class="btn insight-graph-type-button" data-value="term" disabled="disabled">
                <input type="radio" name="graph_type" value="term"> <?= __('期') ?>
            </label>
            <label class="btn insight-graph-type-button" data-value="month" disabled="disabled">
                <input type="radio" name="graph_type" value="month"> <?= __('月') ?>
            </label>
            <label class="btn insight-graph-type-button" data-value="week" disabled="disabled">
                <input type="radio" name="graph_type" value="week"> <?= __('週') ?>
            </label>
            <label class="btn insight-graph-type-button" data-value="day" disabled="disabled">
                <input type="radio" name="graph_type" value="day"> <?= __('日') ?>
            </label>
        </div>
    </div>
<?php endif ?>
<!-- END app/View/Elements/Team/insight_form_input.ctp -->
