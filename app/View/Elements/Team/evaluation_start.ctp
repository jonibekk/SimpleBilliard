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
 * @var                    $current_eval_is_available
 * @var                    $current_term_start_date
 * @var                    $current_term_end_date
 * @var                    $current_eval_is_frozen
 * @var                    $current_term_id
 * @var                    $previous_eval_is_available
 * @var                    $previous_term_start_date
 * @var                    $previous_term_end_date
 * @var                    $previous_eval_is_frozen
 * @var                    $previous_term_id
 */
?>
<?= $this->App->viewStartComment()?>
<section class="panel panel-default">
    <header>
        <h2><?= __("Begin evaluation") ?></h2>
    </header>
    <div class="panel-body form-horizontal">
        <div class="form-group">
            <label for="TeamName" class="col col-sm-3 control-label form-label"></label>

            <div class="col col-sm-6">
                <p class="form-control-static">
                    <?= __("You can start evaluation with the current settings.") ?>
                </p>

                <p class="form-control-static">
                    <?= __("Notice - These settings can't be canceled.") ?>
                </p>
            </div>
        </div>
        <div class="form-group">
            <label for="TeamName" class="col col-sm-3 control-label form-label"><?= __("Current term") ?></label>

            <div class="col col-sm-6">
                <p class="form-control-static"><b><?= $this->TimeEx->date($current_term_start_date) ?>
                        - <?= $this->TimeEx->date($current_term_end_date) ?></b></p>
            </div>
        </div>
        <?php if (!$eval_enabled): ?>
            <div class="alert alert-danger" role="alert">
                <?= __("You need to active Evaluation settings before starting Evaluation.") ?>
            </div>
        <?php elseif (!$eval_start_button_enabled): ?>
            <div class="alert alert-info" role="alert">
                <?= __("In evaluation term") ?>
            </div>
        <?php endif; ?>
    </div>
    <?php if ($eval_enabled && $eval_start_button_enabled): ?>
        <?php $existScore = count($eval_scores['EvaluateScore']) > 0; ?>
        <footer>
            <?php if(!$existScore): ?>
                <div class="form-group">
                        <p class="eval-setting-alert-text"><b><?= __('Add definition of evaluation score') ?></b></p>
                </div>
            <?php endif; ?>
            <?= $this->Form->postLink(__("Start current term evaluations"),
                        ['controller' => 'teams', 'action' => 'start_evaluation',],
                        [
                            'class' => "btn btn-primary",
                            'disabled' => $existScore ? '' : 'disabled'
                        ],
                        __("Unable to cancel. Do you really want to start evaluations?")) ?>

        </footer>
    <?php endif; ?>
</section>
<?= $this->App->viewEndComment()?>
