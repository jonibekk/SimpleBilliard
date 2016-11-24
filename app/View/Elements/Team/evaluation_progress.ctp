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
<?= $this->App->viewStartComment() ?>
<div class="panel panel-default">
    <div class="panel-heading"><?= __("Evaluation status") ?></div>
    <?php if (!$current_eval_is_started && !$previous_eval_is_started): ?>
        <div class="panel-body">
            <p class="text-align_c"><?= __("Evaluation has not started.") ?></p>
        </div>
    <?php else: ?>
        <?php if ($previous_eval_is_started): ?>
            <div class="panel-body">
                <h4><?= __('Previous Term') ?>(<?= $this->TimeEx->date($previous_term_start_date) ?>
                    - <?= $this->TimeEx->date($previous_term_end_date) ?>)</h4>
                <?= $this->element('Team/eval_progress_item',
                    [
                        'evaluate_term_id' => $previous_term_id,
                        'progress_percent' => $previous_progress,
                        'statuses'         => $previous_statuses
                    ]) ?>
            </div>
        <?php endif; ?>
        <?php if ($current_eval_is_started): ?>
            <div class="panel-body">
                <h4><?= __('Current Term') ?>(<?= $this->TimeEx->date($current_term_start_date) ?>
                    - <?= $this->TimeEx->date($current_term_end_date) ?>)</h4>
                <?= $this->element('Team/eval_progress_item',
                    [
                        'evaluate_term_id' => $current_term_id,
                        'progress_percent' => $current_progress,
                        'statuses'         => $current_statuses
                    ]) ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>
<?= $this->App->viewEndComment() ?>
