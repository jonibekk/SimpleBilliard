<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 15/04/13
 * Time: 15:18
 *
 * @var CodeCompletionView $this
 * @var                    $progress_percent
 * @var                    $statuses
 * @var                    $term_id
 */
?>
<!-- START app/View/Elements/Team/eval_progress_item.ctp -->
<div class="form-group" style="overflow:hidden">
    <div class="progress-bar progress-bar-info" role="progressbar"
         aria-valuenow="<?= $progress_percent ?>" aria-valuemin="0"
         aria-valuemax="100" style="width: <?= $progress_percent ?>%;">
        <span class="ml_12px"><?= $progress_percent ?>%</span>
    </div>
</div>
<div class="form-group">
    <a class="modal-ajax-get pointer"
       href="<?= $this->Html->url(['controller' => 'evaluations', 'action' => 'ajax_get_incomplete_evaluatees', 'evaluate_term_id' => $term_id]) ?>">
        <?= __d('gl', "未完了の被評価者をみる") ?>
    </a>
</div>
<div class="form-group">
    <a class="modal-ajax-get pointer"
       href="<?= $this->Html->url(['controller' => 'evaluations', 'action' => 'ajax_get_incomplete_evaluators', 'evaluate_term_id' => $term_id]) ?>">
        <?= __d('gl', "未完了の評価者をみる") ?>
    </a>
</div>
<hr>
<div class="form-group">
    <label for="TeamName" class="col control-label form-label"><?= __d('gl', "未完了数") ?></label>
</div>
<?php foreach ($statuses as $type => $status): ?>
    <div class="form-group">
        <label for="0EvaluationComment" class="col col-xxs-12 col-sm-3 control-label form-label">
            <?php if ($type == Evaluation::TYPE_ONESELF): ?>
                <a class="modal-ajax-get pointer"
                   href="<?= $this->Html->url(['controller' => 'evaluations', 'action' => 'ajax_get_incomplete_oneself', 'evaluate_term_id' => $term_id]) ?>">
                    <?= $status['label'] ?>
                </a>
            <?php else: ?>
                <?= $status['label'] ?>
            <?php endif ?>
        </label>

        <div class="col col-sm-8">
            <?= $status['incomplete_num'] ?>/<?= $status['all_num'] ?>
        </div>
    </div>
<?php endforeach; ?>
<!-- END app/View/Elements/Team/evaluation_progress.ctp -->
