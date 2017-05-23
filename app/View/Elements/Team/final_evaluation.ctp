<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 6/30/14
 * Time: 9:59 AM
 *
 * @var CodeCompletionView $this
 * @var array              $my_teams
 * @var                    $term_start_date
 * @var                    $term_end_date
 * @var                    $eval_enabled
 * @var                    $eval_start_button_enabled
 * @var                    $previous_term_id
 * @var                    $previous_term_start_date
 * @var                    $previous_term_end_date
 * @var                    $current_term_id
 * @var                    $current_term_start_date
 * @var                    $current_term_end_date
 * @var                    $previous_eval_is_frozen
 * @var                    $current_eval_is_frozen
 */
?>
<?= $this->App->viewStartComment()?>
<section class="panel panel-default">
    <header>
        <h2><?= __("Final evaluation") ?></h2>
    </header>
    <div class="panel-body">
        <div class="form-group">
            <label for="TeamName" class="col col-sm-3 control-label form-label"></label>

            <div class="col col-sm-6">
                <?php if ($this->Session->read('ua.device_type') == 'Desktop'): ?>
                    <p class="form-control-static"><?= __("Final evaluation is performed by CSV.") ?></p>

                    <p class="form-control-static">
                        <?= __("Download CSV. After editting, upload it.") ?>
                    </p>

                    <p class="form-control-static"><?= __("") ?></p>

                    <p class="form-control-static"><?= __("") ?></p>

                    <p class="form-control-static"><?= __("") ?></p>

                    <p class="form-control-static"><?= __("") ?></p>
                <?php else: ?>
                    <p class="form-control-static"><?= __("This function can be used only by PC.") ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php if ($this->Session->read('ua.device_type') == 'Desktop'): ?>
        <footer>
            <a href="#" data-toggle="modal" data-target="#ModalFinalEvaluation_<?= $previous_term_id ?>_ByCsv"
            class="btn btn-primary" <?= $previous_term_id && $previous_eval_is_frozen ? null : 'disabled' ?>>
            <?= __('Perform final evaluation of previous term') ?></a>
            <a href="#" data-toggle="modal" data-target="#ModalFinalEvaluation_<?= $current_term_id ?>_ByCsv"
            class="btn btn-primary" <?= $current_term_id && $current_eval_is_frozen ? null : 'disabled' ?>>
                        <?= __('Perform final evaluation of current term') ?></a>
                
        </footer>
    <?php endif; ?>
</section>
<?= $this->App->viewEndComment()?>
<?php $this->start('modal');
if ($previous_term_id && $previous_eval_is_frozen) {
    echo $this->element('Team/modal_final_evaluation_by_csv',
        [
            'evaluate_term_id' => $previous_term_id,
            'start'            => $previous_term_start_date,
            'end'              => $previous_term_end_date
        ]);
}
if ($current_term_id && $current_eval_is_frozen) {
    echo $this->element('Team/modal_final_evaluation_by_csv',
        ['evaluate_term_id' => $current_term_id, 'start' => $current_term_start_date, 'end' => $current_term_end_date]);
}
$this->end();
?>
