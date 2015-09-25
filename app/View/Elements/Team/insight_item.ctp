<?php
/**
 * 必須パラメータ
 *
 * @var  $item_id
 * @var  $item_value
 * @var  $item_label
 * @var  $item_cmp_percent
 * @var  $item_graph_id
 * @var  $item_graph_xaxis
 * @var  $item_graph_values [0 => 6週間前のデータ ... 5 => 1週間前のデータ]
 *                            or [0 => 6ヶ月のデータ ... 5 => 1ヶ月のデータ]
 */
?>
<div class="insight-row">
    <a href="#" id="<?= h($item_id) ?>">
        <?php if ($item_cmp_percent !== null): ?>
            <?php if ($item_cmp_percent >= 0): ?>
                <div class="insight-cmp-percent insight-cmp-percent-plus"><?= __d('gl', '▲') ?> <?= h($item_cmp_percent) ?>%</div>
            <?php elseif ($item_cmp_percent < 0): ?>
                <div class="insight-cmp-percent insight-cmp-percent-minus"><?= __d('gl', '▼') ?> <?= h(abs($item_cmp_percent)) ?>%</div>
            <?php endif ?>
        <?php endif ?>

        <div class="insight-value"><?= h($item_value) ?></div>
        <div class="insight-label"><?= h($item_label) ?></div>
    </a>

    <div id="<?= h($item_graph_id) ?>" style="" class="insight-graph-container none"></div>
</div>
<script type="text/javascript">
    $(function () {
        var $row = $('#<?= h($item_id) ?>');
        var $graphContainer = $("#<?= h($item_graph_id) ?>");
        $row.on('click', function (e) {
            e.preventDefault();

            var d1 = [];
            var data = [];
            <?php foreach ($item_graph_values as $k => $v): ?>
            d1.push([<?= h(intval($k)) ?>, <?= h(intval($v)) ?>]);
            <?php endforeach ?>
            data.push({
                data: d1,
                color: '#aaaaff'
            });

            var options = {
                autoscale: true,
                xaxis: {
                    ticks: [
                        <?php foreach ($item_graph_xaxis as $k => $v): ?>
                        [<?= h($k) ?>, '<?= h($v) ?>'],
                        <?php endforeach ?>
                    ]
                },
                yaxis: {
                    min: 0,
                    tickDecimals: 0
                },
                series: {
                    lines: {show: true, fill: true},
                    points: {show: true}
                },
                grid: {
                    hoverable: true
                }
            };

            $graphContainer.slideToggle('fast', function () {
                if ($(this).is(':visible')) {
                    $.plot($graphContainer, data, options);
                }
            });

            $(window).on('resize', function (event) {
                $.plot($graphContainer, data, options);
            });
        });
    });
</script>