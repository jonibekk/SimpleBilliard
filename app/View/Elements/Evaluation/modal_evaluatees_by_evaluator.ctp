<?php
/**
 * Created by PhpStorm.
 * User: saeki
 * Date: 15/04/17
 * Time: 15:12
 *
 * @var CodeCompletionView $this
 * @var                    $evaluator
 * @var                    $evaluate_term_id
 */
?>
<?= $this->App->viewStartComment()?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close font_33px close-design" data-dismiss="modal" aria-hidden="true"><span
                    class="close-icon">&times;</span></button>
            <h4 class="modal-title"><?= __("%s Pending", $evaluator['EvaluatorUser']['display_username']) ?></h4>
        </div>
        <div class="modal-body modal-feed-body">
            <?php if (!empty($incomplete_evaluatees)): ?>
                <div class="row borderBottom">
                    <?php foreach ($incomplete_evaluatees as $user): ?>
                        <?=
                        $this->element('Evaluation/evaluatee_by_evaluator',
                            ['user' => $user, 'evaluate_term_id' => $evaluate_term_id]) ?>
                    <?php endforeach ?>
                </div>
            <?php else: ?>
                <?= __("Done all evaluations.") ?>
            <?php endif ?>
        </div>
        <div class="modal-footer modal-feed-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?= __("Close") ?></button>
        </div>
    </div>
</div>
<?= $this->App->viewEndComment()?>
