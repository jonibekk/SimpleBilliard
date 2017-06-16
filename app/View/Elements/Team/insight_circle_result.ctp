<?php
/**
 * @var $circle_insights
 */
?>
<?php if (isset($circle_insights)): ?>
    <?= $this->App->viewStartComment()?>
    <?php
    // this code is for sort arrow, decide the direction and location of the arrow.
    $cls = 'fa-angle-down';
    if (!empty($sort_type)) {
        if ($sort_type == 'asc') {
            $cls = 'fa-angle-up';
        }
    }

    $user_count = $post_count = $engage_percent = $post_read_count = false;
    switch ($sort_by) {
        case 'user_count':
            $user_count = true;
            break;
        case 'post_count':
            $post_count = true;
            break;
        case 'engage_percent':
            $engage_percent = true;
            break;

        default:
            $post_read_count = true;
    }
    ?>
    <table class="table">
        <tr class="insight-table-header insight-circle-table-header">
            <th><i class="fa fa-circle-o" data-toggle="tooltip" title="<?= __('Circle name') ?>"></i></th>
            <th id="user_count_header">
                <i class="fa fa-user" data-toggle="tooltip" title="<?= __('Members') ?>"></i>
                <?php if ($user_count) { ?>
                    <i class="fa <?= $cls ?>" data-toggle="tooltip" title="" data-original-title=""></i>
                <?php } ?>
            </th>
            <th id="post_count_header">
                <i class="fa fa-comment-o" data-toggle="tooltip" title="<?= __('Posts') ?>"></i>
                <?php if ($post_count) { ?>
                    <i class="fa <?= $cls ?>" data-toggle="tooltip" title="" data-original-title=""></i>
                <?php } ?>
            </th>
            <th id="post_read_count_header">
                <i class="fa fa-check" data-toggle="tooltip" title="<?= __('Reach') ?>"></i>
                <?php if ($post_read_count) { ?>
                    <i class="fa <?= $cls ?>" data-toggle="tooltip" title="" data-original-title=""></i>
                <?php } ?>
            </th>
            <th id="engage_percent_header">
                <i class="fa fa-heart-o" data-toggle="tooltip" title="<?= __('Engagement') ?>"></i>
                <?php if ($engage_percent) { ?>
                    <i class="fa <?= $cls ?>" data-toggle="tooltip" title="" data-original-title=""></i>
                <?php } ?>
            </th>
        </tr>
        <?php foreach ($circle_insights as $circle): ?>
            <tr class="insight-circle-table-row">
                <td><?= h($circle['name']) ?></td>
                <?php foreach (['user_count', 'post_count', 'post_read_count', 'engage_percent'] as $key): ?>
                    <td>
                        <div class="insight-circle-value">
                            <?= h($circle[$key]) ?><?php if (strpos($key, '_percent') !== false): ?><small>%</small><?php endif ?>
                        </div>
                        <?php if (isset($circle["{$key}_cmp"])): ?>
                            <?php if ($circle["{$key}_cmp"] >= 0): ?>
                                <div class="insight-circle-cmp-percent insight-cmp-percent-plus">
                                    <?php if ($circle["{$key}_cmp"] > 0): ?>
                                        <?= __('▲') ?>
                                    <?php endif ?>
                                    <?= h($circle["{$key}_cmp"]) ?>%
                                </div>
                            <?php elseif ($circle["{$key}_cmp"] < 0): ?>
                                <div class="insight-circle-cmp-percent insight-cmp-percent-minus">
                                    <?= __('▼') ?> <?= h(abs($circle["{$key}_cmp"])) ?>%
                                </div>
                            <?php endif ?>
                        <?php endif ?>
                    </td>
                <?php endforeach ?>
            </tr>
        <?php endforeach ?>
    </table>
    <?= $this->App->viewEndComment()?>
<?php endif ?>