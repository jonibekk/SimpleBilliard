<?php
/**
 * Created by PhpStorm.
 * User: saeki
 * Date: 15/04/13
 * Time: 15:18
 */
?>
<!-- START app/View/Elements/Team/evaluation_progress.ctp -->
<div class="panel panel-default">
    <div class="panel-heading"><?= __d('gl', "評価状況") ?></div>
    <div class="panel-body">
        <div class="form-group" style="overflow:hidden">
            <div class="progress-bar progress-bar-info" role="progressbar"
                 aria-valuenow="<?= $progress_percent ?>" aria-valuemin="0"
                 aria-valuemax="100" style="width: <?= $progress_percent ?>%;">
                <span class="ml_12px"><?= $progress_percent ?>%</span>
            </div>
        </div>
        <div class="form-group">
            <a class="modal-ajax-get pointer"
               href="<?= $this->Html->url(['controller' => 'evaluations', 'action' => 'ajax_get_incomplete_evaluatees']) ?>">
                <?= __d('gl', "未完了の被評価者をみる") ?>
            </a>
        </div>
        <hr>
        <div class="form-group">
            <label for="TeamName" class="col control-label form-label"><?= __d('gl', "未完了数") ?></label>
        </div>
        <? foreach ($statuses as $status): ?>
            <div class="form-group">
                <label for="0EvaluationComment" class="col col-xxs-12 col-sm-3 control-label form-label">
                    <?= $status['label'] ?>
                </label>

                <div class="col col-sm-8">
                    <?= $status['incomplete_num'] ?>/<?= $status['all_num'] ?>
                </div>
            </div>
        <? endforeach; ?>
    </div>
    <!-- END app/View/Elements/Team/evaluation_progress.ctp -->