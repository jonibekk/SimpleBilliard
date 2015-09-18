<?php
/**
 * @var $ranking
 * @var $text_list
 * @var $url_list
 */
?>
<?php if (isset($ranking)): ?>
    <!-- START app/View/Teams/insight_ranking_result.ctp -->
    <?php
    $icon = [
        'action_goal_ranking'    => ['text' => 'fa-quote-left', 'count' => 'fa-check-circle'],
        'action_like_ranking'    => ['text' => 'fa-quote-left', 'count' => 'fa-thumbs-o-up'],
        'action_comment_ranking' => ['text' => 'fa-quote-left', 'count' => 'fa-comment-o'],
        'action_user_ranking'    => ['text' => 'fa-user', 'count' => 'fa-check-circle'],
        'post_user_ranking'      => ['text' => 'fa-user', 'count' => 'fa-comment-o'],
        'post_like_ranking'      => ['text' => 'fa-quote-left', 'count' => 'fa-thumbs-o-up'],
        'post_comment_ranking'   => ['text' => 'fa-quote-left', 'count' => 'fa-comment-o'],
    ];
    ?>
    <table class="table mt_18px">
        <tr class="insight-table-header">
            <th>#</th>
            <th><i class="fa <?= $icon[$this->request->query('type')]['text'] ?>"></i></th>
            <th>
                <i class="fa <?= $icon[$this->request->query('type')]['count'] ?>"></i>
            </th>
        </tr>
        <?php
        $no = 1;
        foreach ($ranking as $id => $count): ?>
            <tr>
                <td><?= h($no++) ?></td>
                <td>
                    <a href="<?= $url_list[$id] ?>"><?= h(mb_substr($text_list[$id], 0, 40, 'UTF-8')) ?></a>
                </td>
                <td><?= h($count) ?></td>
            </tr>
        <?php endforeach ?>
    </table>
    <!-- END app/View/Teams/insight_ranking_result.ctp -->
<?php endif ?>