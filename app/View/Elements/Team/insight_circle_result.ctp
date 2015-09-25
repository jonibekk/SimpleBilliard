<?php
/**
 * @var $circle_insights
 *
 */
?>
<?php if (isset($circle_insights)): ?>
    <!-- START app/View/Teams/insight_circle_result.ctp -->

    <table class="table">
        <tr class="insight-table-header insight-circle-table-header">
            <th><i class="fa fa-circle-o" data-toggle="tooltip" title="<?= __d('gl', 'サークル名') ?>"></i></th>
            <th><i class="fa fa-user" data-toggle="tooltip" title="<?= __d('gl', 'メンバー') ?>"></i></th>
            <th><i class="fa fa-comment-o" data-toggle="tooltip" title="<?= __d('gl', '投稿') ?>"></i></th>
            <th><i class="fa fa-check" data-toggle="tooltip" title="<?= __d('gl', 'リーチ') ?>"></i></th>
            <th><i class="fa fa-heart-o" data-toggle="tooltip" title="<?= __d('gl', 'エンゲージメント') ?>"></i></th>
        </tr>
        <?php foreach ($circle_insights as $circle): ?>
            <tr class="insight-circle-table-row">
                <td><?= h($circle['name']) ?></td>
                <?php foreach (['user_count', 'post_count', 'post_read_count', 'engage_percent'] as $key): ?>
                    <td>
                        <div class="insight-circle-value">
                            <?= h($circle[$key]) ?>
                            <?php if (strpos($key, '_percent') !== false): ?><span
                                class="font_12px">%</span><?php endif ?>
                        </div>
                        <?php if (isset($circle["{$key}_cmp"])): ?>
                            <?php if ($circle["{$key}_cmp"] >= 0): ?>
                                <div class="insight-circle-cmp-percent insight-cmp-percent-plus">
                                    <?= __d('gl', '▲') ?> <?= h($circle["{$key}_cmp"]) ?>%
                                </div>
                            <?php elseif ($circle["{$key}_cmp"] < 0): ?>
                                <div class="insight-circle-cmp-percent insight-cmp-percent-minus">
                                    <?= __d('gl', '▼') ?> <?= h(abs($circle["{$key}_cmp"])) ?>%
                                </div>
                            <?php endif ?>
                        <?php endif ?>
                    </td>
                <?php endforeach ?>
            </tr>
        <?php endforeach ?>
    </table>
    <!-- END app/View/Teams/insight_circle_result.ctp -->

<?php endif ?>