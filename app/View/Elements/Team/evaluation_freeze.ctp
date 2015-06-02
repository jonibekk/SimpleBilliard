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
<!-- START app/View/Elements/Team/evaluation_freeze.ctp -->
<div class="panel panel-default">
    <div class="panel-heading"><?= __d('gl', "評価凍結設定") ?></div>
    <div class="panel-body form-horizontal">
        <?php if ($current_eval_is_started): ?>
            <h4><?= __d('gl', "今期") ?>(<?= $this->TimeEx->date($current_term_start_date) ?>
                - <?= $this->TimeEx->date($current_term_end_date) ?>)</h4>
            <?php if ($current_eval_is_frozen): ?>
                <?=
                $this->Form->postLink(__d('gl', "今期の評価の凍結を解除する"),
                                      ['controller' => 'teams', 'action' => 'change_freeze_status',],
                                      ['class' => 'btn btn-default', 'data' => ['evaluate_term_id' => $current_term_id]],
                                      __d('gl', "今期の評価の凍結を解除します。よろしいですか？")) ?>
            <?php else: ?>
                <?=
                $this->Form->postLink(__d('gl', "今期の評価を凍結する"),
                                      ['controller' => 'teams', 'action' => 'change_freeze_status',],
                                      ['class' => 'btn btn-primary', 'data' => ['evaluate_term_id' => $current_term_id]],
                                      __d('gl', "今期の評価を凍結します。よろしいですか？")) ?>
            <?php endif; ?>
        <?php endif; ?>
        <?php if ($previous_eval_is_started): ?>
            <h4><?= __d('gl', "前期") ?>(<?= $this->TimeEx->date($previous_term_start_date) ?>
                - <?= $this->TimeEx->date($previous_term_end_date) ?>)</h4>
            <?php if ($previous_eval_is_frozen): ?>
                <?=
                $this->Form->postLink(__d('gl', "前期の評価の凍結を解除する"),
                                      ['controller' => 'teams', 'action' => 'change_freeze_status',],
                                      ['class' => 'btn btn-default', 'data' => ['evaluate_term_id' => $previous_term_id]],
                                      __d('gl', "前期の評価の凍結を解除します。よろしいですか？")) ?>
            <?php else: ?>
                <?=
                $this->Form->postLink(__d('gl', "前期の評価を凍結する"),
                                      ['controller' => 'teams', 'action' => 'change_freeze_status',],
                                      ['class' => 'btn btn-primary', 'data' => ['evaluate_term_id' => $previous_term_id]],
                                      __d('gl', "前期の評価を凍結します。よろしいですか？")) ?>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>
<!-- END app/View/Elements/Team/evaluation_freeze.ctp -->
