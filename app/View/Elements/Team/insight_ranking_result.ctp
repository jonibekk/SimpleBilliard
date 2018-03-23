<?php
/**
 * @var $ranking
 * @var $type
 * @var $text_list
 * @var $url_list
 */
?>
<?php if (isset($ranking)): ?>
    <?= $this->App->viewStartComment()?>
    <?php
    $icon = [
        'action_goal_ranking'    => ['img' => 'fa-flag', 'text' => 'fa-quote-left', 'count' => 'fa-check-circle'],
        'action_like_ranking'    => ['img' => 'fa-user', 'text' => 'fa-quote-left', 'count' => 'fa-thumbs-o-up'],
        'action_comment_ranking' => ['img' => 'fa-user', 'text' => 'fa-quote-left', 'count' => 'fa-comment-o'],
        'action_user_ranking'    => ['img' => 'fa-user', 'text' => '', 'count' => 'fa-check-circle'],
        'post_user_ranking'      => ['img' => 'fa-user', 'text' => '', 'count' => 'fa-comment-o'],
        'post_like_ranking'      => ['img' => 'fa-user', 'text' => 'fa-quote-left', 'count' => 'fa-thumbs-o-up'],
        'post_comment_ranking'   => ['img' => 'fa-user', 'text' => 'fa-quote-left', 'count' => 'fa-comment-o'],
    ];
    ?>
    <table class="table mt_18px" id="ranking-table">
        <tr class="insight-table-header insight-ranking-table-header">
            <th>#</th>
            <th><i class="fa <?= h($icon[$type]['img']) ?>"></i></th>
            <th><i class="fa <?= h($icon[$type]['text']) ?>"></i></th>
            <th><i class="fa <?= h($icon[$type]['count']) ?>"></i></th>
        </tr>
        <?php
        foreach ($ranking as $id => $row): ?>
            <tr class="insight-ranking-table-row">
                <td><?= h($row['rank']) ?></td>
                <td>
                    <?php if ($type == 'action_goal_ranking'): ?>
                        <a href="<?= $this->Html->url(
                            [
                                'controller' => 'goals',
                                'action'     => 'view_krs',
                                'goal_id'    => $row['Goal']['id']
                            ]) ?>"><?= $this->Upload->uploadImage($row['Goal'],
                                'Goal.photo',
                                ['style' => 'small'],
                                ['class' => 'insight-ranking-result-table-images']
                            ) ?></a>
                    <?php else: ?>
                        <a href="<?= $this->Html->url(
                            [
                                'controller' => 'users',
                                'action'     => 'view_goals',
                                'user_id'    => $row['User']['id']
                            ]) ?>"
                           data-toggle="tooltip"
                           title="<?= h($row['User']['display_username']) ?>"><?= $this->Upload->uploadImage($row['User'],
                                'User.photo',
                                ['style' => 'small']) ?></a>
                    <?php endif ?>
                </td>
                <td>
                    <a href="<?= h($row['url']) ?>"><?= h(mb_substr($row['text'], 0, 40, 'UTF-8')) ?></a>
                </td>
                <td><?= h($row['count']) ?></td>
            </tr>
        <?php endforeach ?>
    </table>
    <div class="panel panel-default feed-read-more" id="RankingMoreRead">
        <div class="panel-body panel-read-more-body">
            <span class="none" id="ShowMoreNoData"><?= __("There is no more ranking to show.") ?></span>
            <a href="#" class="click-ranking-read-more"
               no-data-text-id="ShowMoreNoData"
               get-url="<?=
               $this->Html->url([
                   'controller'     => 'teams',
                   'action'         => 'ajax_get_insight_ranking',
                   '?' => [
                       'date_range'     => $date_range,
                       'group'          => $group,
                       'type'           => $type,
                       'timezone'       => $timezone,
                       'last_rank'      => $last_rank
                    ]
               ]) ?>"
               id="RankingMoreReadLink"
            >
                <?= __("More...") ?> </a>
        </div>
    </div>
    <?= $this->App->viewEndComment()?>
<?php endif ?>
