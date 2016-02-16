<?php
/**
 * 必須パラメータ
 *
 * @var  $item_id
 * @var  $item_value
 * @var  $item_label
 * @var  $item_cmp_percent
 */
?>
<div class="insight-row">
    <div id="<?= h($item_id) ?>" class="insight-value-container">
        <?php if ($item_cmp_percent !== null): ?>
            <?php if ($item_cmp_percent >= 0): ?>
                <div class="insight-cmp-percent insight-cmp-percent-plus"><?= __d('app', '▲') ?> <?= h($item_cmp_percent) ?>%</div>
            <?php elseif ($item_cmp_percent < 0): ?>
                <div class="insight-cmp-percent insight-cmp-percent-minus"><?= __d('app', '▼') ?> <?= h(abs($item_cmp_percent)) ?>%</div>
            <?php endif ?>
        <?php endif ?>

        <div class="insight-value"><?= h($item_value) ?></div>
        <div class="insight-label"><?= h($item_label) ?></div>
    </div>

    <div class="insight-graph-container"><i class="fa fa-refresh fa-spin"></i></div>
</div>