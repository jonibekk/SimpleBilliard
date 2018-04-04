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
 * @var                    $previous_eval_start_button_enabled
 */
?>
<?= $this->App->viewStartComment()?>
<section class="panel panel-default">
    <header>
        <h2><?= __("Begin evaluation") ?></h2>
    </header>
    <div class="panel-body form-horizontal">
        <div class="form-group">
            <div class="col col-sm-12">
                <p class="form-control-static">
                    <?= __("You may start evaluations with your team's current settings. Only one term can be selected for evaluations.") ?>
                </p>

                <p class="form-control-static">
                    <?= __("Notice: Team evaluation settings cannot be changed after starting evaluations.") ?>
                </p>

                <?php if ($both_term_selectable): ?>
                <p class="form-control-static font_rouge font_bold">
                    <?= __("Please select.") ?>
                </p>
                <?php endif; ?>

                <div class="form-group">
                    <?php if ($previous_term_exists): ?>
                    <div class="radio">
                        <label class="<?= $disable_previous_radio ? " font-cgray" : "" ?>">
                            <input type="radio" name="term_id" value="<?= $previous_term_id ?>"<?= $previous_radio_checked ? " checked" : "" ?><?= $disable_previous_radio ? " disabled" : "" ?>>
                            <?= __("Previous term") ?><br>
                            <?= $this->TimeEx->date($previous_term_start_date) ?> - <?= $this->TimeEx->date($previous_term_end_date) ?>
                        </label>
                    </div>
                    <?php endif; ?>
                    <?php if ($current_term_exists): ?>
                    <div class="radio">
                        <label class="<?= $disable_current_radio ? " font-cgray" : "" ?>">
                            <input type="radio" name="term_id" value="<?= $current_term_id ?>"<?= $current_radio_checked ? " checked" : "" ?><?= $disable_current_radio ? " disabled" : "" ?>>
                            <?= __("Current term") ?><br>
                            <?= $this->TimeEx->date($current_term_start_date) ?> - <?= $this->TimeEx->date($current_term_end_date) ?>
                        </label>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php if (!$eval_enabled): ?>
            <div class="alert alert-danger" role="alert">
                <?= __("You need to active Evaluation settings before starting Evaluation.") ?>
            </div>
        <?php elseif (!$can_start_evaluation): ?>
            <div class="alert alert-info" role="alert">
                <?= __("Evaluation commenced") ?>
            </div>
        <?php endif; ?>
    </div>
    <?php if ($can_start_evaluation && $eval_enabled && ($eval_start_button_enabled || $previous_eval_start_button_enabled)): ?>
        <?php $existScore = count($eval_scores['EvaluateScore']) > 0; ?>
        <footer>
            <?php if(!$existScore): ?>
                <div class="form-group">
                        <p class="eval-setting-alert-text"><b><?= __('Add definition of evaluation score') ?></b></p>
                </div>
            <?php endif; ?>
            <?php if($eval_start_button_enabled || $previous_eval_start_button_enabled): ?>
            <button id="buttonStartEvaluation" class="btn btn-primary width100_per"<?= $either_start_button_enabled ? "" : " disabled" ?>>
                <?= __("Start evaluations") ?>
            </button>
            <?php endif; ?>
        </footer>
    <?php endif; ?>
</section>
<?= $this->App->viewEndComment()?>
