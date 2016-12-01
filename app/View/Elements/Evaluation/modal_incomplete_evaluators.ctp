<?php
/**
 * Created by PhpStorm.
 * User: saeki
 * Date: 15/04/17
 * Time: 14:12
 *
 * @var CodeCompletionView $this
 * @var                    $incomplete_evaluators
 * @var                    $evaluate_term_id
 */
?>
<?= $this->App->viewStartComment()?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close font_33px close-design" data-dismiss="modal" aria-hidden="true"><span
                    class="close-icon">&times;</span></button>
            <h4 class="modal-title"><?= __("Pending Evaluator (%s)", count($incomplete_evaluators)) ?></h4>
        </div>
        <div class="modal-body">
            <?php if (!empty($incomplete_evaluators)): ?>
                <div class="row borderBottom">
                    <?php foreach ($incomplete_evaluators as $user): ?>
                        <?=
                        $this->element('Evaluation/incomplete_evaluator',
                            ['user' => $user['User'], 'evaluate_term_id' => $evaluate_term_id]) ?>
                    <?php endforeach ?>
                </div>
            <?php else: ?>
                <?= __("Done all evaluations.") ?>
            <?php endif ?>
        </div>
    </div>
</div>
<?= $this->App->viewEndComment()?>
