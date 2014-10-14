<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 9/26/14
 * Time: 11:14 AM
 *
 * @var CodeCompletionView $this
 * @var                    $key_results
 */
?>
<!-- START app/View/Elements/Goals/key_result_items.ctp -->
<? if (!empty($key_results)): ?>
    <? foreach ($key_results as $kr): ?>
        <div class="col col-xxs-12">
            <?= h($kr['KeyResult']['name']) ?>
        </div>
    <? endforeach ?>
<? else: ?>
    <div class="col col-xxs-12">
        <?= __d('gl', "基準はまだありません。") ?>
    </div>
<?endif; ?>
<!-- End app/View/Elements/Goals/key_result_items.ctp -->