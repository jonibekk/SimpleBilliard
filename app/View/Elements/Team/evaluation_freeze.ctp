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
 * @var                    $current_eval_is_started
 * @var                    $current_term_start_date
 * @var                    $current_term_end_date
 * @var                    $current_eval_is_frozen
 * @var                    $current_term_id
 * @var                    $previous_eval_is_started
 * @var                    $previous_term_start_date
 * @var                    $previous_term_end_date
 * @var                    $previous_eval_is_frozen
 * @var                    $previous_term_id
 */
?>
<?= $this->App->viewStartComment()?>
<div class="panel panel-default">
    <div class="panel-heading"><?= __("Paused evaluation settings.") ?></div>
    <div class="panel-body form-horizontal">
        <?php if ($current_eval_is_started): ?>
            <h4><?= __("Current Term") ?>(<?= $this->TimeEx->date($current_term_start_date) ?>
                - <?= $this->TimeEx->date($current_term_end_date) ?>)</h4>
            <?php if ($current_eval_is_frozen): ?>
                <?=
                $this->Form->postLink(__("Resume current term evaluations."),
                    ['controller'       => 'teams',
                     'action'           => 'change_freeze_status',
                     'evaluate_term_id' => $current_term_id
                    ],
                    ['class' => 'btn btn-default'],
                    __("Would you like to resume current term evaluations?")) ?>
            <?php else: ?>
                <?=
                $this->Form->postLink(__("Pause current terms evaluations."),
                    ['controller'       => 'teams',
                     'action'           => 'change_freeze_status',
                     'evaluate_term_id' => $current_term_id
                    ],
                    ['class' => 'btn btn-primary'],
                    __("Would you like to pause currtent term evaluations?")) ?>
            <?php endif; ?>
        <?php endif; ?>
        <?php if ($previous_eval_is_started): ?>
            <h4><?= __("Previous Term") ?>(<?= $this->TimeEx->date($previous_term_start_date) ?>
                - <?= $this->TimeEx->date($previous_term_end_date) ?>)</h4>
            <?php if ($previous_eval_is_frozen): ?>
                <?=
                $this->Form->postLink(__("Resume previous term evaluations."),
                    ['controller'       => 'teams',
                     'action'           => 'change_freeze_status',
                     'evaluate_term_id' => $previous_term_id
                    ],
                    ['class' => 'btn btn-default'],
                    __("Would you like to resume previous term evaluations?")) ?>
            <?php else: ?>
                <?=
                $this->Form->postLink(__("Pause previous term evaluations."),
                    ['controller'       => 'teams',
                     'action'           => 'change_freeze_status',
                     'evaluate_term_id' => $previous_term_id
                    ],
                    ['class' => 'btn btn-primary'],
                    __("Would you like to pause previous term evaluations?")) ?>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>
<?= $this->App->viewEndComment()?>
