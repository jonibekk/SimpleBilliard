<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 7/9/15
 * Time: 3:33 PM
 *
 * @var $goal
 * @var $key_results
 */
?>
<!-- START app/View/Users/view_krs.ctp -->
<div class="col-sm-8 col-sm-offset-2">
    <div class="panel panel-default">
        <?= $this->element('Goal/simplex_top_section') ?>
        <div class="panel-body">
            ゴール進捗率: <?= h($goal['Goal']['progress']) ?>%
            <div class="progress mb_0px goals-column-progress-bar">
                <div class="progress-bar progress-bar-info" role="progressbar"
                     aria-valuenow="<?= h($goal['Goal']['progress']) ?>" aria-valuemin="0"
                     aria-valuemax="100" style="width: <?= h($goal['Goal']['progress']) ?>%;">
                    <span class="ml_12px"><?= h($goal['Goal']['progress']) ?>%</span>
                </div>
            </div>
            出したい成果
            <div class="row borderBottom" id="GoalPageKeyResultContainer">
                <?= $this->element('Goal/key_results') ?>
                <?php if (!$key_results): ?>
                    <?= __d('gl', '成果は登録されていません') ?>
                <? endif ?>
            </div>
        </div>
        <div class="panel-body panel-read-more-body">
            <a href="#" class="btn btn-link click-goal-key-result-more"
               next-page-num="2"
               id="GoalPageKeyResultMoreLink"
               list-container="#GoalPageKeyResultContainer"
               goal-id="<?= h($goal['Goal']['id']) ?>"
                >
                <?= __d('gl', 'さらに読み込む') ?></a>
        </div>
    </div>
</div>
<!-- END app/View/Users/view_krs.ctp -->
