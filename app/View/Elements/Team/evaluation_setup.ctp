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
<div class="panel panel-default">
    <div class="panel-heading"><?= __("Evaluation settings") ?></div>
    <div class="panel-body form-horizontal">
        <?php if ($this->Expt->is('EnableEvaluationFeature') === false): ?>
            <?= __('Evaluation feature is disabled now. If you would like to use it,') ?> <a
                href="mailto:<?= INTERCOM_APP_ID ?>@incoming.intercom.io" id="#ContactForEvaluation"
                class=""><?= __('please contact us.') ?></a>
        <?php else: ?>
            <?=
            $this->Form->create('EvaluationSetting', [
                'inputDefaults' => [
                    'div'       => false,
                    'label'     => false,
                    'wrapInput' => 'col col-sm-9',
                    'class'     => 'bt-switch'
                ],
                'class'         => 'form-horizontal',
                'novalidate'    => true,
                'id'            => '',
                'url'           => ['controller' => 'teams', 'action' => 'save_evaluation_setting']
            ]); ?>
            <?= $this->Form->hidden('id') ?>
            <?= $this->Form->hidden('team_id', ['value' => $this->Session->read('current_team_id')]) ?>
            <div class="form-group">
                <label class="col col-sm-3 control-label form-label"><?= __('Evaluate') ?></label>
                <?= $this->Form->input("enable_flg", ['default' => false,]) ?>
            </div>
            <div class="form-group">
                <label class="col col-sm-3 control-label form-label"><?= __('Self evaluation') ?></label>
                <?= $this->Form->input("self_flg", ['default' => true,]) ?>
            </div>
            <div class="form-group">
                <label class="col col-sm-3 control-label form-label"><?= __('Self evaluation goal score') ?></label>
                <?= $this->Form->input("self_goal_score_flg", ['default' => true,]) ?>
            </div>
            <div class="form-group">
                <label
                    class="col col-sm-3 control-label form-label"><?= __('Self evaluation goal score required') ?></label>
                <?= $this->Form->input("self_goal_score_required_flg", ['default' => true,]) ?>
            </div>
            <div class="form-group">
                <label class="col col-sm-3 control-label form-label"><?= __('Self evaluation goal comment') ?></label>
                <?= $this->Form->input("self_goal_comment_flg", ['default' => true,]) ?>
            </div>
            <div class="form-group">
                <label
                    class="col col-sm-3 control-label form-label"><?= __('Self evaluation goal comment required') ?></label>
                <?= $this->Form->input("self_goal_comment_required_flg", ['default' => true,]) ?>
            </div>
            <div class="form-group">
                <label class="col col-sm-3 control-label form-label"><?= __('Self evaluation total score') ?></label>
                <?= $this->Form->input("self_score_flg", ['default' => true,]) ?>
            </div>
            <div class="form-group">
                <label
                    class="col col-sm-3 control-label form-label"><?= __('Self evaluation total score required') ?></label>
                <?= $this->Form->input("self_score_required_flg", ['default' => true,]) ?>
            </div>
            <div class="form-group">
                <label class="col col-sm-3 control-label form-label"><?= __('Self evaluation total comment') ?></label>
                <?= $this->Form->input("self_comment_flg", ['default' => true,]) ?>
            </div>
            <div class="form-group">
                <label
                    class="col col-sm-3 control-label form-label"><?= __('Self evaluation total comment required') ?></label>
                <?= $this->Form->input("self_comment_required_flg", ['default' => true,]) ?>
            </div>
            <div class="form-group">
                <label class="col col-sm-3 control-label form-label"><?= __('Evaluation by Evaluator') ?></label>
                <?= $this->Form->input("evaluator_flg", ['default' => true,]) ?>
            </div>
            <div class="form-group">
                <label
                    class="col col-sm-3 control-label form-label"><?= __('Evaluation goal score by Evaluator') ?></label>
                <?= $this->Form->input("evaluator_goal_score_flg", ['default' => true,]) ?>
            </div>
            <div class="form-group">
                <label
                    class="col col-sm-3 control-label form-label"><?= __('Evaluation goal score by Evaluator required') ?></label>
                <?= $this->Form->input("evaluator_goal_score_reuqired_flg", ['default' => true,]) ?>
            </div>
            <div class="form-group">
                <label
                    class="col col-sm-3 control-label form-label"><?= __('Evaluation goal comment by Evaluator') ?></label>
                <?= $this->Form->input("evaluator_goal_comment_flg", ['default' => true,]) ?>
            </div>
            <div class="form-group">
                <label
                    class="col col-sm-3 control-label form-label"><?= __('Evaluation goal comment by Evaluator required') ?></label>
                <?= $this->Form->input("evaluator_goal_comment_required_flg", ['default' => true,]) ?>
            </div>
            <div class="form-group">
                <label
                    class="col col-sm-3 control-label form-label"><?= __('Evaluation total score by Evaluator') ?></label>
                <?= $this->Form->input("evaluator_score_flg", ['default' => true,]) ?>
            </div>
            <div class="form-group">
                <label
                    class="col col-sm-3 control-label form-label"><?= __('Evaluation total score by Evaluator required') ?></label>
                <?= $this->Form->input("evaluator_score_required_flg", ['default' => true,]) ?>
            </div>
            <div class="form-group">
                <label
                    class="col col-sm-3 control-label form-label"><?= __('Evaluation total comment by Evaluator') ?></label>
                <?= $this->Form->input("evaluator_comment_flg", ['default' => true,]) ?>
            </div>
            <div class="form-group">
                <label
                    class="col col-sm-3 control-label form-label"><?= __('Evaluation total comment by Evaluator required') ?></label>
                <?= $this->Form->input("evaluator_comment_required_flg", ['default' => false,]) ?>
            </div>
            <div class="form-group">
                <label class="col col-sm-3 control-label form-label"><?= __('Evaluation by Final Evaluator') ?></label>
                <?= $this->Form->input("final_flg", ['default' => true,]) ?>
            </div>
            <div class="form-group">
                <label
                    class="col col-sm-3 control-label form-label"><?= __('Evaluation total score by Final Evaluator') ?></label>
                <?= $this->Form->input("final_score_flg", ['default' => true,]) ?>
            </div>
            <div class="form-group">
                <label
                    class="col col-sm-3 control-label form-label"><?= __('Evaluation total score by Final Evaluator required') ?></label>
                <?= $this->Form->input("final_score_required_flg", ['default' => true,]) ?>
            </div>
            <div class="form-group">
                <label
                    class="col col-sm-3 control-label form-label"><?= __('Evaluation total comment by Final Evaluator') ?></label>
                <?= $this->Form->input("final_comment_flg", ['default' => true,]) ?>
            </div>
            <div class="form-group">
                <label
                    class="col col-sm-3 control-label form-label"><?= __('Evaluation total comment by Final Evaluator required') ?></label>
                <?= $this->Form->input("final_comment_required_flg", ['default' => true,]) ?>
            </div>
            <?php /* 今後実装予定機能
        <div class="form-group">
            <label class="col col-sm-3 control-label form-label"><?= __('リーダー評価') ?></label>
            <?= $this->Form->input("leader_flg", ['default' => true,]) ?>
        </div>
        <div class="form-group">
            <label class="col col-sm-3 control-label form-label"><?= __('リーダー評価 ゴール スコア') ?></label>
            <?= $this->Form->input("leader_goal_score_flg", ['default' => false,]) ?>
        </div>
        <div class="form-group">
            <label class="col col-sm-3 control-label form-label"><?= __('リーダー評価 ゴール スコア必須') ?></label>
            <?= $this->Form->input("leader_goal_score_reuqired_flg", ['default' => true,]) ?>
        </div>
        <div class="form-group">
            <label class="col col-sm-3 control-label form-label"><?= __('リーダー評価 ゴール コメント') ?></label>
            <?= $this->Form->input("leader_goal_comment_flg", ['default' => true,]) ?>
        </div>
        <div class="form-group">
            <label class="col col-sm-3 control-label form-label"><?= __('リーダー評価 ゴール コメント必須') ?></label>
            <?= $this->Form->input("leader_goal_comment_required_flg", ['default' => false,]) ?>
        </div>
        */ ?>
        <?php endif; ?>
    </div>
    <?php if ($this->Expt->is('EnableEvaluationFeature') === true): ?>
        <div class="panel-footer">
            <div class="row">
                <div class="col-sm-9 col-sm-offset-3">
                    <?= $this->Form->submit(__('Save settings'), ['class' => 'btn btn-primary pull-right']) ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?= $this->Form->end() ?>

</div>
<?= $this->App->viewEndComment() ?>
