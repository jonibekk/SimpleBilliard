<?php
/**
 * Created by PhpStorm.
 * User: saeki
 * Date: 15/04/17
 * Time: 16:50
 *
 * @var CodeCompletionView $this
 * @var                    $oneself_incomplete_users
 * @var                    $evaluate_term_id
 */
?>
<?= $this->App->viewStartComment()?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close font_33px close-design" data-dismiss="modal" aria-hidden="true"><span
                    class="close-icon">&times;</span></button>
            <h4 class="modal-title"><?= __("Pending self-evaluation") ?></h4>
        </div>
        <div class="modal-body">
            <?php if (!empty($oneself_incomplete_users)): ?>
                <div class="row borderBottom">
                    <?php foreach ($oneself_incomplete_users as $user): ?>
                        <?=
                        $this->element('Evaluation/incomplete_oneself',
                            ['user' => $user['EvaluatorUser'], 'evaluate_term_id' => $evaluate_term_id]) ?>
                    <?php endforeach ?>
                </div>
            <?php else: ?>
                <?= __("Done all evaluations.") ?>
            <?php endif ?>
        </div>
    </div>
</div>
<?= $this->App->viewEndComment()?>
