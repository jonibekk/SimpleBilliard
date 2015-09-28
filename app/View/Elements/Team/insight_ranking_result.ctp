<?php
/**
 * @var $ranking
 * @var $type
 * @var $text_list
 * @var $url_list
 */
?>
<?php if (isset($ranking)): ?>
    <!-- START app/View/Teams/insight_ranking_result.ctp -->
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
    <table class="table mt_18px">
        <tr class="insight-table-header insight-ranking-table-header">
            <th>#</th>
            <th><i class="fa <?= $icon[$type]['img'] ?>"></i></th>
            <th><i class="fa <?= $icon[$type]['text'] ?>"></i></th>
            <th><i class="fa <?= $icon[$type]['count'] ?>"></i></th>
        </tr>
        <?php
        $no = 1;
        foreach ($ranking as $id => $row): ?>
            <tr class="insight-ranking-table-row">
                <td><?= h($no++) ?></td>
                <td>
                    <?php if ($type == 'action_goal_ranking'): ?>
                        <a href="<?= $this->Html->url(
                            ['controller' => 'goals',
                             'action'     => 'view_info',
                             'goal_id'    => $row['Goal']['id']
                            ]) ?>"><?= $this->Upload->uploadImage($row['Goal'],
                                                                  'Goal.photo',
                                                                  ['style' => 'small']) ?></a>
                    <?php else: ?>
                        <a href="<?= $this->Html->url(
                            ['controller' => 'users',
                             'action'     => 'view_goals',
                             'user_id'    => $row['User']['id']
                            ]) ?>"
                           data-toggle="tooltip"
                           title="<?= $row['User']['display_username'] ?>"><?= $this->Upload->uploadImage($row['User'],
                                                                                                          'User.photo',
                                                                                                          ['style' => 'small']) ?></a>
                    <?php endif ?>
                </td>
                <td>
                    <a href="<?= $row['url'] ?>"><?= h(mb_substr($row['text'], 0, 40, 'UTF-8')) ?></a>
                </td>
                <td><?= h($row['count']) ?></td>
            </tr>
        <?php endforeach ?>
    </table>
    <!-- END app/View/Teams/insight_ranking_result.ctp -->
<?php endif ?>