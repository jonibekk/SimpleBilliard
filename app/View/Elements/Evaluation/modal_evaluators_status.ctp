<?php
/**
 * Created by PhpStorm.
 * User: saeki
 * Date: 15/04/15
 * Time: 10:35
 *
 * @var CodeCompletionView $this
 * @var                    $evaluatee
 */
?>
<?= $this->App->viewStartComment()?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close font_33px close-design" data-dismiss="modal" aria-hidden="true"><span
                    class="close-icon">&times;</span></button>
            <h4 class="modal-title"><?= __("%s Evaluation Status",
                    $evaluatee['EvaluateeUser']['display_username']) ?></h4>
        </div>
        <div class="modal-body modal-feed-body">
            <?php if (!empty($evaluators)): ?>
                <div class="row borderBottom">
                    <?php foreach ($evaluators as $user): ?>
                        <?=
                        $this->element('Evaluation/evaluators_status',
                            ['user' => $user]) ?>
                    <?php endforeach ?>
                </div>
            <?php else: ?>
                <?= __("No one has read this comment yet.") ?>
            <?php endif ?>
        </div>
        <div class="modal-footer modal-feed-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?= __("Close") ?></button>
        </div>
    </div>
</div>
<?= $this->App->viewEndComment()?>
