<?php
/**
 * @var $insights
 */
?>
<?php if (isset($insights)): ?>
    <!-- START app/View/Teams/insight_result.ctp -->
    <?php
    // 先週/先月の集計情報
    $insight = $insights[0];
    ?>
    <div>
        <?php
        // グラフの横軸ラベル
        // 最初と最後のラベルは空にする
        $xaxis = [''];
        for ($i = count($insights) - 2; $i > 0; $i--) {
            $xaxis[] =  date('M. j', strtotime($insights[$i]['start_date']));
        }
        $xaxis[] = '';
        ?>
        <?= $this->element('Team/insight_item', [
            'item_id'           => 'UserCountItem',
            'item_value'        => $insight['user_count'],
            'item_label'        => __d('gl', 'メンバー（アクティブ）'),
            'item_cmp_percent'  => isset($insight['user_count_cmp']) ? $insight['user_count_cmp'] : null,
            'item_graph_id'     => 'UserCountGraph',
            'item_graph_xaxis'  => $xaxis,
            'item_graph_values' => array_reverse(array_map(function ($v) {
                return $v['user_count'];
            }, $insights)),
        ]) ?>

        <?= $this->element('Team/insight_item', [
            'item_id'           => 'AccessUserCountItem',
            'item_value'        => $insight['access_user_count'],
            'item_label'        => __d('gl', 'ログインメンバー'),
            'item_cmp_percent'  => isset($insight['access_user_count_cmp']) ? $insight['access_user_count_cmp'] : null,
            'item_graph_id'     => 'AccessUserCountGraph',
            'item_graph_xaxis'  => $xaxis,
            'item_graph_values' => array_reverse(array_map(function ($v) {
                return $v['access_user_count'];
            }, $insights)),
        ]) ?>
    </div>

    <hr>

    <?= $this->Form->select('unique',
                            [
                                'total'  => __d('gl', 'トータル'),
                                'unique' => __d('gl', 'ユニーク'),
                            ], [
                                'id'    => 'InsightUniqueToggle',
                                'class' => 'form-control',
                                'empty' => false
                            ]) ?>
    <div id="InsightTotalRow">
        <?= $this->element('Team/insight_item', [
            'item_id'           => 'ActionCountItem',
            'item_value'        => $insight['action_count'],
            'item_label'        => __d('gl', 'アクション'),
            'item_cmp_percent'  => isset($insight['action_count_cmp']) ? $insight['action_count_cmp'] : null,
            'item_graph_id'     => 'ActionCountGraph',
            'item_graph_xaxis'  => $xaxis,
            'item_graph_values' => array_reverse(array_map(function ($v) {
                return $v['action_count'];
            }, $insights)),
        ]) ?>
        <?= $this->element('Team/insight_item', [
            'item_id'           => 'PostCountItem',
            'item_value'        => $insight['post_count'],
            'item_label'        => __d('gl', '投稿'),
            'item_cmp_percent'  => isset($insight['post_count_cmp']) ? $insight['post_count_cmp'] : null,
            'item_graph_id'     => 'PostCountGraph',
            'item_graph_xaxis'  => $xaxis,
            'item_graph_values' => array_reverse(array_map(function ($v) {
                return $v['post_count'];
            }, $insights)),
        ]) ?>
        <?= $this->element('Team/insight_item', [
            'item_id'           => 'LikeCountItem',
            'item_value'        => $insight['like_count'],
            'item_label'        => __d('gl', 'いいね'),
            'item_cmp_percent'  => isset($insight['like_count_cmp']) ? $insight['like_count_cmp'] : null,
            'item_graph_id'     => 'LikeCountGraph',
            'item_graph_xaxis'  => $xaxis,
            'item_graph_values' => array_reverse(array_map(function ($v) {
                return $v['like_count'];
            }, $insights)),
        ]) ?>
        <?= $this->element('Team/insight_item', [
            'item_id'           => 'CommentCountItem',
            'item_value'        => $insight['comment_count'],
            'item_label'        => __d('gl', 'コメント'),
            'item_cmp_percent'  => isset($insight['comment_count_cmp']) ? $insight['comment_count_cmp'] : null,
            'item_graph_id'     => 'CommentCountGraph',
            'item_graph_xaxis'  => $xaxis,
            'item_graph_values' => array_reverse(array_map(function ($v) {
                return $v['comment_count'];
            }, $insights)),
        ]) ?>
        <?= $this->element('Team/insight_item', [
            'item_id'           => 'MessageCountItem',
            'item_value'        => $insight['message_count'],
            'item_label'        => __d('gl', 'メッセージ'),
            'item_cmp_percent'  => isset($insight['message_count_cmp']) ? $insight['message_count_cmp'] : null,
            'item_graph_id'     => 'MessageCountGraph',
            'item_graph_xaxis'  => $xaxis,
            'item_graph_values' => array_reverse(array_map(function ($v) {
                return $v['message_count'];
            }, $insights)),
        ]) ?>
    </div>

    <div id="InsightUniqueRow" class="none">
        <?= $this->element('Team/insight_item', [
            'item_id'           => 'ActionUserCountItem',
            'item_value'        => $insight['action_user_count'],
            'item_label'        => __d('gl', 'アクション'),
            'item_cmp_percent'  => isset($insight['action_user_count_cmp']) ? $insight['action_user_count_cmp'] : null,
            'item_graph_id'     => 'ActionUserCountGraph',
            'item_graph_xaxis'  => $xaxis,
            'item_graph_values' => array_reverse(array_map(function ($v) {
                return $v['action_user_count'];
            }, $insights)),
        ]) ?>
        <?= $this->element('Team/insight_item', [
            'item_id'           => 'PostUserCountItem',
            'item_value'        => $insight['post_user_count'],
            'item_label'        => __d('gl', '投稿'),
            'item_cmp_percent'  => isset($insight['post_user_count_cmp']) ? $insight['post_user_count_cmp'] : null,
            'item_graph_id'     => 'PostUserCountGraph',
            'item_graph_xaxis'  => $xaxis,
            'item_graph_values' => array_reverse(array_map(function ($v) {
                return $v['post_user_count'];
            }, $insights)),
        ]) ?>
        <?= $this->element('Team/insight_item', [
            'item_id'           => 'LikeUserCountItem',
            'item_value'        => $insight['like_user_count'],
            'item_label'        => __d('gl', 'いいね'),
            'item_cmp_percent'  => isset($insight['like_user_count_cmp']) ? $insight['like_user_count_cmp'] : null,
            'item_graph_id'     => 'LikeUserCountGraph',
            'item_graph_xaxis'  => $xaxis,
            'item_graph_values' => array_reverse(array_map(function ($v) {
                return $v['like_user_count'];
            }, $insights)),
        ]) ?>
        <?= $this->element('Team/insight_item', [
            'item_id'           => 'CommentUserCountItem',
            'item_value'        => $insight['comment_user_count'],
            'item_label'        => __d('gl', 'コメント'),
            'item_cmp_percent'  => isset($insight['comment_user_count_cmp']) ? $insight['comment_user_count_cmp'] : null,
            'item_graph_id'     => 'CommentUserCountGraph',
            'item_graph_xaxis'  => $xaxis,
            'item_graph_values' => array_reverse(array_map(function ($v) {
                return $v['comment_user_count'];
            }, $insights)),
        ]) ?>
        <?= $this->element('Team/insight_item', [
            'item_id'           => 'MessageUserCountItem',
            'item_value'        => $insight['message_user_count'],
            'item_label'        => __d('gl', 'メッセージ'),
            'item_cmp_percent'  => isset($insight['message_user_count_cmp']) ? $insight['message_user_count_cmp'] : null,
            'item_graph_id'     => 'MessageUserCountGraph',
            'item_graph_xaxis'  => $xaxis,
            'item_graph_values' => array_reverse(array_map(function ($v) {
                return $v['message_user_count'];
            }, $insights)),
        ]) ?>
    </div>

    <hr>

    <div>
        <?= $this->element('Team/insight_item', [
            'item_id'           => 'ActionUserPercentItem',
            'item_value'        => $insight['action_user_percent'] . ' %',
            'item_label'        => __d('gl', 'アクション'),
            'item_cmp_percent'  => isset($insight['action_user_percent_cmp']) ? $insight['action_user_percent_cmp'] : null,
            'item_graph_id'     => 'ActionUserPercentGraph',
            'item_graph_xaxis'  => $xaxis,
            'item_graph_values' => array_reverse(array_map(function ($v) {
                return $v['action_user_percent'];
            }, $insights)),
        ]) ?>
        <?= $this->element('Team/insight_item', [
            'item_id'           => 'PostUserPercentItem',
            'item_value'        => $insight['post_user_percent'] . ' %',
            'item_label'        => __d('gl', '投稿'),
            'item_cmp_percent'  => isset($insight['post_user_percent_cmp']) ? $insight['post_user_percent_cmp'] : null,
            'item_graph_id'     => 'PostUserPercentGraph',
            'item_graph_xaxis'  => $xaxis,
            'item_graph_values' => array_reverse(array_map(function ($v) {
                return $v['post_user_percent'];
            }, $insights)),
        ]) ?>
        <?= $this->element('Team/insight_item', [
            'item_id'           => 'LikeUserPercentItem',
            'item_value'        => $insight['like_user_percent'] . ' %',
            'item_label'        => __d('gl', 'いいね'),
            'item_cmp_percent'  => isset($insight['like_user_percent_cmp']) ? $insight['like_user_percent_cmp'] : null,
            'item_graph_id'     => 'LikeUserPercentGraph',
            'item_graph_xaxis'  => $xaxis,
            'item_graph_values' => array_reverse(array_map(function ($v) {
                return $v['like_user_percent'];
            }, $insights)),
        ]) ?>
        <?= $this->element('Team/insight_item', [
            'item_id'           => 'CommentUserPercentItem',
            'item_value'        => $insight['comment_user_percent'] . ' %',
            'item_label'        => __d('gl', 'コメント'),
            'item_cmp_percent'  => isset($insight['comment_user_percent_cmp']) ? $insight['comment_user_percent_cmp'] : null,
            'item_graph_id'     => 'CommentUserPercentGraph',
            'item_graph_xaxis'  => $xaxis,
            'item_graph_values' => array_reverse(array_map(function ($v) {
                return $v['comment_user_percent'];
            }, $insights)),
        ]) ?>
        <?= $this->element('Team/insight_item', [
            'item_id'           => 'MessageUserPercentItem',
            'item_value'        => $insight['message_user_percent'] . ' %',
            'item_label'        => __d('gl', 'メッセージ'),
            'item_cmp_percent'  => isset($insight['message_user_percent_cmp']) ? $insight['message_user_percent_cmp'] : null,
            'item_graph_id'     => 'MessageUserPercentGraph',
            'item_graph_xaxis'  => $xaxis,
            'item_graph_values' => array_reverse(array_map(function ($v) {
                return $v['message_user_percent'];
            }, $insights)),
        ]) ?>
    </div>
<!-- END app/View/Teams/insight_result.ctp -->

<?php endif ?>