<?php
/**
 * Created by PhpStorm.
 * User: saeki
 * Date: 15/04/13
 * Time: 15:18
 *
 * @var CodeCompletionView $this
 * @var                    $progress_percent
 * @var                    $statuses
 * @var                    $previous_term_id
 * @var                    $previous_progress
 * @var                    $previous_statuses
 * @var                    $current_term_id
 * @var                    $current_progress
 * @var                    $current_statuses
 * @var                    $previous_eval_is_started
 * @var                    $previous_term_start_date
 * @var                    $previous_term_end_date
 * @var                    $current_eval_is_started
 * @var                    $current_term_start_date
 * @var                    $current_term_end_date
 */
?>
<!-- START app/View/Elements/Team/evaluation_progress.ctp -->
<div class="panel panel-default">
    <div class="panel-heading"><?= __d('gl', "評価状況") ?></div>
    <?php if ($previous_eval_is_started): ?>
        <div class="panel-body">
            <h4><?= __d('gl', '前期') ?>(<?= $this->TimeEx->date($previous_term_start_date) ?>
                - <?= $this->TimeEx->date($previous_term_end_date) ?>)</h4>
            <?= $this->element('Team/eval_progress_item',
                               ['evaluate_term_id' => $previous_term_id,
                                'progress_percent' => $previous_progress,
                                'statuses'         => $previous_statuses]) ?>
        </div>
    <?php endif; ?>
    <?php if ($current_eval_is_started): ?>
        <div class="panel-body">
            <h4><?= __d('gl', '今期') ?>(<?= $this->TimeEx->date($current_term_start_date) ?>
                - <?= $this->TimeEx->date($current_term_end_date) ?>)</h4>
            <?= $this->element('Team/eval_progress_item',
                               ['evaluate_term_id' => $current_term_id,
                                'progress_percent' => $current_progress,
                                'statuses'         => $current_statuses]) ?>
        </div>
    <?php endif; ?>
</div>
<!-- END app/View/Elements/Team/evaluation_progress.ctp -->
