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
        <?= $this->element('Team/insight_item', [
            'item_id'           => 'user_count_item',
            'item_value'        => $insight['user_count'],
            'item_label'        => __('メンバー（アクティブ）'),
            'item_cmp_percent'  => isset($insight['user_count_cmp']) ? $insight['user_count_cmp'] : null,
        ]) ?>

        <?= $this->element('Team/insight_item', [
            'item_id'           => 'access_user_count_item',
            'item_value'        => $insight['access_user_count'],
            'item_label'        => __('ログインメンバー'),
            'item_cmp_percent'  => isset($insight['access_user_count_cmp']) ? $insight['access_user_count_cmp'] : null,
        ]) ?>
    </div>

    <hr>

    <?= $this->Form->select('unique',
                            [
                                'total'  => __('トータル'),
                                'unique' => __('ユニーク'),
                            ], [
                                'id'    => 'InsightUniqueToggle',
                                'class' => 'form-control',
                                'empty' => false
                            ]) ?>
    <div id="InsightTotalRow">
        <?= $this->element('Team/insight_item', [
            'item_id'           => 'action_count_item',
            'item_value'        => $insight['action_count'],
            'item_label'        => __('Action'),
            'item_cmp_percent'  => isset($insight['action_count_cmp']) ? $insight['action_count_cmp'] : null,
        ]) ?>
        <?= $this->element('Team/insight_item', [
            'item_id'           => 'post_count_item',
            'item_value'        => $insight['post_count'],
            'item_label'        => __('Posts'),
            'item_cmp_percent'  => isset($insight['post_count_cmp']) ? $insight['post_count_cmp'] : null,
        ]) ?>
        <?= $this->element('Team/insight_item', [
            'item_id'           => 'like_count_item',
            'item_value'        => $insight['like_count'],
            'item_label'        => __('いいね'),
            'item_cmp_percent'  => isset($insight['like_count_cmp']) ? $insight['like_count_cmp'] : null,
        ]) ?>
        <?= $this->element('Team/insight_item', [
            'item_id'           => 'comment_count_item',
            'item_value'        => $insight['comment_count'],
            'item_label'        => __('コメント'),
            'item_cmp_percent'  => isset($insight['comment_count_cmp']) ? $insight['comment_count_cmp'] : null,
        ]) ?>
        <?= $this->element('Team/insight_item', [
            'item_id'           => 'message_count_item',
            'item_value'        => $insight['message_count'],
            'item_label'        => __('Message'),
            'item_cmp_percent'  => isset($insight['message_count_cmp']) ? $insight['message_count_cmp'] : null,
        ]) ?>
    </div>

    <div id="InsightUniqueRow" class="none">
        <?= $this->element('Team/insight_item', [
            'item_id'           => 'action_user_count_item',
            'item_value'        => $insight['action_user_count'],
            'item_label'        => __('Action'),
            'item_cmp_percent'  => isset($insight['action_user_count_cmp']) ? $insight['action_user_count_cmp'] : null,
        ]) ?>
        <?= $this->element('Team/insight_item', [
            'item_id'           => 'post_user_count_item',
            'item_value'        => $insight['post_user_count'],
            'item_label'        => __('Posts'),
            'item_cmp_percent'  => isset($insight['post_user_count_cmp']) ? $insight['post_user_count_cmp'] : null,
        ]) ?>
        <?= $this->element('Team/insight_item', [
            'item_id'           => 'like_user_count_item',
            'item_value'        => $insight['like_user_count'],
            'item_label'        => __('いいね'),
            'item_cmp_percent'  => isset($insight['like_user_count_cmp']) ? $insight['like_user_count_cmp'] : null,
        ]) ?>
        <?= $this->element('Team/insight_item', [
            'item_id'           => 'comment_user_count_item',
            'item_value'        => $insight['comment_user_count'],
            'item_label'        => __('コメント'),
            'item_cmp_percent'  => isset($insight['comment_user_count_cmp']) ? $insight['comment_user_count_cmp'] : null,
        ]) ?>
        <?= $this->element('Team/insight_item', [
            'item_id'           => 'message_user_count_item',
            'item_value'        => $insight['message_user_count'],
            'item_label'        => __('Message'),
            'item_cmp_percent'  => isset($insight['message_user_count_cmp']) ? $insight['message_user_count_cmp'] : null,
        ]) ?>
    </div>

    <hr>

    <div>
        <?= $this->element('Team/insight_item', [
            'item_id'           => 'action_user_percent_item',
            'item_value'        => $insight['action_user_percent'] . ' %',
            'item_label'        => __('Action'),
            'item_cmp_percent'  => isset($insight['action_user_percent_cmp']) ? $insight['action_user_percent_cmp'] : null,
        ]) ?>
        <?= $this->element('Team/insight_item', [
            'item_id'           => 'post_user_percent_item',
            'item_value'        => $insight['post_user_percent'] . ' %',
            'item_label'        => __('Posts'),
            'item_cmp_percent'  => isset($insight['post_user_percent_cmp']) ? $insight['post_user_percent_cmp'] : null,
        ]) ?>
        <?= $this->element('Team/insight_item', [
            'item_id'           => 'like_user_percent_item',
            'item_value'        => $insight['like_user_percent'] . ' %',
            'item_label'        => __('いいね'),
            'item_cmp_percent'  => isset($insight['like_user_percent_cmp']) ? $insight['like_user_percent_cmp'] : null,
        ]) ?>
        <?= $this->element('Team/insight_item', [
            'item_id'           => 'comment_user_percent_item',
            'item_value'        => $insight['comment_user_percent'] . ' %',
            'item_label'        => __('コメント'),
            'item_cmp_percent'  => isset($insight['comment_user_percent_cmp']) ? $insight['comment_user_percent_cmp'] : null,
        ]) ?>
        <?= $this->element('Team/insight_item', [
            'item_id'           => 'message_user_percent_item',
            'item_value'        => $insight['message_user_percent'] . ' %',
            'item_label'        => __('Message'),
            'item_cmp_percent'  => isset($insight['message_user_percent_cmp']) ? $insight['message_user_percent_cmp'] : null,
        ]) ?>
    </div>
<!-- END app/View/Teams/insight_result.ctp -->

<?php endif ?>