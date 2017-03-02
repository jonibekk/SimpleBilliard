<?php /**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 6/11/14
 * Time: 11:40 AM
 *
 * @var CodeCompletionView $this
 * @var                    $border_months_options
 * @var                    $start_term_month_options
 * @var                    $current_eval_is_started
 * @var                    $previous_term_start_date
 * @var                    $previous_term_end_date
 * @var                    $previous_term_timezone
 * @var                    $current_term_start_date
 * @var                    $current_term_end_date
 * @var                    $current_term_timezone
 * @var                    $next_term_start_date
 * @var                    $next_term_end_date
 * @var                    $next_term_timezone
 * @var                    $timezones
 */
?>
<?= $this->App->viewStartComment()?>
<div class="panel panel-default">
    <div class="panel-heading"><?= __("Term settings") ?></div>
    <div class="panel-body add-team-panel-body">
        <?php if ($current_term_start_date && $current_term_end_date): ?>
            <div class="form-group">
                <label class="col col-sm-3 control-label form-label"><?= __("Current term") ?></label>

                <div class="col col-sm-6">
                    <p class="form-control-static" id="">
                        <?= $this->TimeEx->date($current_term_start_date, $current_term_timezone) ?>
                        - <?= $this->TimeEx->date($current_term_end_date, $current_term_timezone) ?>
                        <?= $this->TimeEx->getTimezoneText($current_term_timezone) ?>
                    </p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php $this->end() ?>
<?= $this->App->viewEndComment()?>
