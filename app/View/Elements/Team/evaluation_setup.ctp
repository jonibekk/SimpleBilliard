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
<?= $this->App->viewStartComment() ?>
<section class="panel panel-default">
    <header>
        <h2><?= __("Evaluation settings") ?></h2>
    </header>
    <div class="panel-body form-horizontal">
        <?php if ($this->Expt->is(Experiment::NAME_ENABLE_EVALUATION_FEATURE) === false): ?>
            <?= __('Evaluation feature is disabled now. If you would like to use it,') ?> <a
                href="mailto:<?= INTERCOM_APP_ID ?>@incoming.intercom.io"
                class="intercom-launcher "><?= __('please contact us.') ?></a>
        <?php else: ?>
            <?=
            $this->Form->create('EvaluationSetting', [
                'inputDefaults' => [
                    'div'       => false,
                    'label'     => false,
                    'class'     => 'bt-switch'
                ],
                'class'         => 'form-horizontal',
                'novalidate'    => true,
                'id'            => 'evaluation_setting',
                'url'           => ['controller' => 'teams', 'action' => 'save_evaluation_setting']
            ]); ?>
            <?= $this->Form->hidden('id') ?>
            <?= $this->Form->hidden('team_id', ['value' => $this->Session->read('current_team_id')]) ?>
            <fieldset>
                <label class="control-label form-label"><?= __('Evaluate') ?></label>
                <?= $this->Form->input("enable_flg", ['default' => false,]) ?>
            </fieldset>
        <?php endif; ?>
    </div>
    <?php if ($this->Expt->is(Experiment::NAME_ENABLE_EVALUATION_FEATURE) === true): ?>
        <footer>
            <?= $this->Form->submit(__('Save settings'), ['class' => 'btn btn-primary']) ?>
        </footer>
    <?php endif; ?>

    <?= $this->Form->end() ?>

</section>
<?= $this->App->viewEndComment() ?>
