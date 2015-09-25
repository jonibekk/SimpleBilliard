<?php
/**
 * @var $team_list
 * @var $prev_week
 * @var $prev_month
 * @var $current_term
 * @var $group_list
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
            'options' => $team_list]) ?>
    <?php endif ?>
<?php endif ?>

<?php if (in_array('date_range', $use)): ?>
    <?= $this->Form->input('date_range', [
        'id'      => 'InsightInputDateRange',
        'type'    => 'select',
        'options' => [
            'prev_week'    => __d('gl', '先週') . sprintf(" (%s - %s)",
                                                        str_replace('-', '/', $prev_week['start']),
                                                        str_replace('-', '/', $prev_week['end'])),
            'prev_month'   => __d('gl', '先月') . sprintf(" (%s - %s)",
                                                        str_replace('-', '/', $prev_month['start']),
                                                        str_replace('-', '/', $prev_month['end'])),
            'current_term' => __d('gl', '今期') . sprintf(" (%s - %s)",
                                                        str_replace('-', '/', $current_term['start']),
                                                        str_replace('-', '/', $current_term['end'])),
        ]]) ?>
<?php endif ?>

<?php if (in_array('group', $use)): ?>
    <?= $this->Form->input('group', [
        'id'      => 'InsightInputGroup',
        'type'    => 'select',
        'empty'   => __d('gl', 'すべてのメンバー'),
        'options' => $group_list])
    ?>
<?php endif ?>

<?php if (in_array('ranking_type', $use)): ?>
    <?= $this->Form->input('type', [
        'id'      => 'InsightInputType',
        'type'    => 'select',
        'empty'   => false,
        'options' => ['action_goal_ranking'    => __d('gl', 'アクションされたゴール'),
                      'action_like_ranking'    => __d('gl', 'いいねされたアクション'),
                      'action_comment_ranking' => __d('gl', 'コメントされたアクション'),
                      'action_user_ranking'    => __d('gl', 'アクションしたメンバー'),
                      'post_user_ranking'      => __d('gl', '投稿したメンバー'),
                      'post_like_ranking'      => __d('gl', 'いいねされた投稿'),
                      'post_comment_ranking'   => __d('gl', 'コメントされた投稿'),
        ]])
    ?>
<?php endif ?>

<?php if (in_array('timezone', $use)): ?>
    <?= $this->Form->input('timezone', [
        'id'      => 'InsightInputTimezone',
        'type'    => 'select',
        'empty'   => false,
        'options' => ['9'   => __d('gl', '(GMT+09:00) 東京'),
                      '5.5' => __d('gl', '(GMT+05:30) ニューデリー'),
                      '1'   => __d('gl', '(GMT+01:00) ベルリン'),
                      '-8'  => __d('gl', '(GMT-08:00) 太平洋標準時 (アメリカ & カナダ)')
        ]])
    ?>
<?php endif ?>
<!-- END app/View/Elements/Team/insight_form_input.ctp -->
