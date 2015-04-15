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
 */
?>
<!-- START app/View/Elements/Team/evaluation_setup.ctp -->
<div class="panel panel-default">
    <div class="panel-heading"><?= __d('gl', "評価設定") ?></div>
    <div class="panel-body form-horizontal">
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
            <label class="col col-sm-3 control-label form-label"><?= __d('gl', '評価する') ?></label>
            <?= $this->Form->input("enable_flg", ['default' => false,]) ?>
        </div>
        <div class="form-group">
            <label class="col col-sm-3 control-label form-label"><?= __d('gl', '自己評価') ?></label>
            <?= $this->Form->input("self_flg", ['default' => true,]) ?>
        </div>
        <div class="form-group">
            <label class="col col-sm-3 control-label form-label"><?= __d('gl', '自己評価ゴールスコア') ?></label>
            <?= $this->Form->input("self_goal_score_flg", ['default' => true,]) ?>
        </div>
        <div class="form-group">
            <label class="col col-sm-3 control-label form-label"><?= __d('gl', '自己評価ゴールスコア必須') ?></label>
            <?= $this->Form->input("self_goal_score_required_flg", ['default' => true,]) ?>
        </div>
        <div class="form-group">
            <label class="col col-sm-3 control-label form-label"><?= __d('gl', '自己評価 ゴール コメント') ?></label>
            <?= $this->Form->input("self_goal_comment_flg", ['default' => true,]) ?>
        </div>
        <div class="form-group">
            <label class="col col-sm-3 control-label form-label"><?= __d('gl', '自己評価 ゴール コメント必須') ?></label>
            <?= $this->Form->input("self_goal_comment_required_flg", ['default' => true,]) ?>
        </div>
        <div class="form-group">
            <label class="col col-sm-3 control-label form-label"><?= __d('gl', '自己評価 トータル スコア') ?></label>
            <?= $this->Form->input("self_score_flg", ['default' => true,]) ?>
        </div>
        <div class="form-group">
            <label class="col col-sm-3 control-label form-label"><?= __d('gl', '自己評価 トータル スコア 必須') ?></label>
            <?= $this->Form->input("self_score_required_flg", ['default' => true,]) ?>
        </div>
        <div class="form-group">
            <label class="col col-sm-3 control-label form-label"><?= __d('gl', '自己評価 トータル コメント') ?></label>
            <?= $this->Form->input("self_comment_flg", ['default' => true,]) ?>
        </div>
        <div class="form-group">
            <label class="col col-sm-3 control-label form-label"><?= __d('gl', '自己評価 トータル コメント 必須') ?></label>
            <?= $this->Form->input("self_comment_required_flg", ['default' => true,]) ?>
        </div>
        <div class="form-group">
            <label class="col col-sm-3 control-label form-label"><?= __d('gl', '評価者評価') ?></label>
            <?= $this->Form->input("evaluator_flg", ['default' => true,]) ?>
        </div>
        <div class="form-group">
            <label class="col col-sm-3 control-label form-label"><?= __d('gl', '評価者評価 ゴール スコア') ?></label>
            <?= $this->Form->input("evaluator_goal_score_flg", ['default' => true,]) ?>
        </div>
        <div class="form-group">
            <label class="col col-sm-3 control-label form-label"><?= __d('gl', '評価者評価 ゴール スコア必須') ?></label>
            <?= $this->Form->input("evaluator_goal_score_reuqired_flg", ['default' => true,]) ?>
        </div>
        <div class="form-group">
            <label class="col col-sm-3 control-label form-label"><?= __d('gl', '評価者評価 ゴール コメント') ?></label>
            <?= $this->Form->input("evaluator_goal_comment_flg", ['default' => true,]) ?>
        </div>
        <div class="form-group">
            <label class="col col-sm-3 control-label form-label"><?= __d('gl', '評価者評価 ゴール コメント必須') ?></label>
            <?= $this->Form->input("evaluator_goal_comment_required_flg", ['default' => true,]) ?>
        </div>
        <div class="form-group">
            <label class="col col-sm-3 control-label form-label"><?= __d('gl', '評価者評価 トータル スコア') ?></label>
            <?= $this->Form->input("evaluator_score_flg", ['default' => true,]) ?>
        </div>
        <div class="form-group">
            <label class="col col-sm-3 control-label form-label"><?= __d('gl', '評価者評価 トータル スコア 必須') ?></label>
            <?= $this->Form->input("evaluator_score_required_flg", ['default' => true,]) ?>
        </div>
        <div class="form-group">
            <label class="col col-sm-3 control-label form-label"><?= __d('gl', '評価者評価 トータル コメント') ?></label>
            <?= $this->Form->input("evaluator_comment_flg", ['default' => true,]) ?>
        </div>
        <div class="form-group">
            <label class="col col-sm-3 control-label form-label"><?= __d('gl', '評価者評価 トータル コメント 必須') ?></label>
            <?= $this->Form->input("evaluator_comment_required_flg", ['default' => true,]) ?>
        </div>
        <div class="form-group">
            <label class="col col-sm-3 control-label form-label"><?= __d('gl', '最終評価者評価') ?></label>
            <?= $this->Form->input("final_flg", ['default' => true,]) ?>
        </div>
        <div class="form-group">
            <label class="col col-sm-3 control-label form-label"><?= __d('gl', '最終評価者評価 トータル スコア') ?></label>
            <?= $this->Form->input("final_score_flg", ['default' => true,]) ?>
        </div>
        <div class="form-group">
            <label class="col col-sm-3 control-label form-label"><?= __d('gl', '最終評価者評価 トータル スコア 必須') ?></label>
            <?= $this->Form->input("final_score_required_flg", ['default' => true,]) ?>
        </div>
        <div class="form-group">
            <label class="col col-sm-3 control-label form-label"><?= __d('gl', '最終評価者評価 トータル コメント') ?></label>
            <?= $this->Form->input("final_comment_flg", ['default' => true,]) ?>
        </div>
        <div class="form-group">
            <label class="col col-sm-3 control-label form-label"><?= __d('gl', '最終評価者評価 トータル コメント 必須') ?></label>
            <?= $this->Form->input("final_comment_required_flg", ['default' => true,]) ?>
        </div>
        <div class="form-group">
            <label class="col col-sm-3 control-label form-label"><?= __d('gl', 'リーダ評価') ?></label>
            <?= $this->Form->input("leader_flg", ['default' => true,]) ?>
        </div>
        <div class="form-group">
            <label class="col col-sm-3 control-label form-label"><?= __d('gl', 'リーダ評価 ゴール スコア') ?></label>
            <?= $this->Form->input("leader_goal_score_flg", ['default' => true,]) ?>
        </div>
        <div class="form-group">
            <label class="col col-sm-3 control-label form-label"><?= __d('gl', 'リーダ評価 ゴール スコア必須') ?></label>
            <?= $this->Form->input("leader_goal_score_reuqired_flg", ['default' => true,]) ?>
        </div>
        <div class="form-group">
            <label class="col col-sm-3 control-label form-label"><?= __d('gl', 'リーダ評価 ゴール コメント') ?></label>
            <?= $this->Form->input("leader_goal_comment_flg", ['default' => true,]) ?>
        </div>
        <div class="form-group">
            <label class="col col-sm-3 control-label form-label"><?= __d('gl', 'リーダ評価 ゴール コメント必須') ?></label>
            <?= $this->Form->input("leader_goal_comment_required_flg", ['default' => true,]) ?>
        </div>
        <div class="form-actions">
            <?= $this->Form->submit(__d('gl', '評価設定を保存'), ['class' => 'btn btn-primary']) ?>
        </div>
        <?= $this->Form->end() ?>


        <div class="form-group">
            <label for="TeamName" class="col col-sm-3 control-label form-label"></label>

            <div class="col col-sm-6">
                <p class="form-control-static"><?= __d('gl', "このセクションでは、現在の評価設定に基づき、評価を開始できます。") ?></p>

                <p class="form-control-static"><?= __d('gl', "この設定は取り消すことができませんので気を付けてください。") ?></p>
            </div>
        </div>
        <div class="form-group">
            <label for="TeamName" class="col col-sm-3 control-label form-label"><?= __d('gl', "今期の期間") ?></label>

            <div class="col col-sm-6">
                <p class="form-control-static"><b><?= $this->TimeEx->date($term_start_date) ?>
                        - <?= $this->TimeEx->date($term_end_date) ?></b></p>
            </div>
        </div>
        <? if (!$eval_enabled): ?>
            <div class="alert alert-danger" role="alert">
                <?= __d('gl', "現在、評価設定が有効では無い為、評価を開始する事ができません。") ?>
            </div>
        <? elseif (!$eval_start_button_enabled): ?>
            <div class="alert alert-info" role="alert">
                <?= __d('gl', "評価期間中です。") ?>
            </div>
        <? endif; ?>
    </div>
    <? if ($eval_enabled && $eval_start_button_enabled): ?>
        <div class="panel-footer">
            <div class="row">
                <div class="col-sm-9 col-sm-offset-3">
                    <?=
                    $this->Form->postLink(__d('gl', "今期の評価を開始する"),
                                          ['controller' => 'teams', 'action' => 'start_evaluation',],
                                          ['class' => 'btn btn-primary'], __d('gl', "取り消しができません。よろしいですか？")) ?>
                </div>
            </div>
        </div>
    <? endif; ?>
</div>
<!-- END app/View/Elements/Team/evaluation_setup.ctp -->
<? $this->start('modal') ?>
<?= $this->element('modal_add_members_by_csv') ?>
<?= $this->element('modal_edit_members_by_csv') ?>
<? $this->end() ?>
