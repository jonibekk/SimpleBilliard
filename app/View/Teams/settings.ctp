<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 6/18/14
 * Time: 5:40 PM
 *
 * @var CodeCompletionView $this
 * @var                    $team_id
 * @var                    $unvalued
 */
?>
<!-- START app/View/Teams/settings.ctp -->
<? $this->start('sidebar') ?>
<li class="active"><a href="#account"><?= __d('gl', "メンバー招待") ?></a></li>
<li class=""><a href="#profile"><?= __d('gl', "一括登録") ?></a></li>
<li class=""><a href="#evaluation"><?= __d('gl', "評価設定") ?></a></li>
<li class=""><a href="#evaluation_score_setting"><?= __d('gl', "評価スコア設定") ?></a></li>
<li class=""><a href="#evaluation_start"><?= __d('gl', "評価開始") ?></a></li>
<li class=""><a href="#evaluation_freeze"><?= __d('gl', "評価凍結") ?></a></li>
<li class=""><a href="#final_evaluation"><?= __d('gl', "最終評価") ?></a></li>
<li class=""><a href="#progress"><?= __d('gl', "評価状況") ?></a></li>
<? $this->end() ?>
<div id="account">
    <?= $this->element('Team/invite') ?>
</div>
<div id="profile">
    <?= $this->element('Team/batch_setup') ?>
</div>
<div id="evaluation">
    <?= $this->element('Team/evaluation_setup') ?>
</div>
<div id="evaluation_score_setting">
    <?= $this->element('Team/evaluation_score_setting') ?>
</div>
<div id="evaluation_start">
    <?= $this->element('Team/evaluation_start') ?>
</div>
<div id="evaluation_freeze">
    <?= $this->element('Team/evaluation_freeze') ?>
</div>
<div id="final_evaluation">
    <?= $this->element('Team/final_evaluation') ?>
</div>
<div id="progress">
    <?= $this->element('Team/evaluation_progress') ?>
</div>
<!-- END app/View/Teams/settings.ctp -->
