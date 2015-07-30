<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 7/9/15
 * Time: 3:33 PM
 *
 * @var $goal
 * @var $key_results
 * @var $is_leader
 * @var $is_collaborator
 */
?>
<!-- START app/View/Goals/view_krs.ctp -->
<div class="col-sm-8 col-sm-offset-2">
    <div class="panel panel-default">
        <?= $this->element('Goal/simplex_top_section') ?>
        <div class="panel-body goal-detail-kr-panel">
            <p class="goal-detail-kr-progress-score">
                <?= __d('gl', 'ゴール進捗率') ?>: <?= h($goal['Goal']['progress']) ?>%
            </p>
            <div class="progress mb_0px goals-column-progress-bar goal-detail-kr-progress-bar-wrap">
                <div class="progress-bar progress-bar-info goal-detail-kr-progress-bar" role="progressbar"
                     aria-valuenow="<?= h($goal['Goal']['progress']) ?>" aria-valuemin="0"
                     aria-valuemax="100" style="width: <?= h($goal['Goal']['progress']) ?>%;">
                    <span class="ml_12px"><?= h($goal['Goal']['progress']) ?>%</span>
                </div>
            </div>
            <h3 class="goal-detail-kr-add-heading">
                <i class="fa fa-key"></i>
                <?= __d('gl', 'このゴールの達成要素') ?>
                <!-- todo 数を追加 -->
            </h3>
            <?php $kr_can_edit = ($is_leader || $is_collaborator); ?>
            <?php if ($kr_can_edit): ?>
                <div>
                    <a class="btn-add-kr modal-ajax-get-add-key-result"
                       href="<?= $this->Html->url(['controller' => 'goals', 'action' => 'ajax_get_add_key_result_modal', 'goal_id' => $goal['Goal']['id']]) ?>">
                        <i class="fa fa-plus btn-add-kr-icon"></i>
                        <span><?= __d('gl', "達成要素を追加") ?></span>
                    </a>
                </div>
            <?php endif ?>
            <div class="row borderBottom" id="GoalPageKeyResultContainer">
                <?= $this->element('Goal/key_results', ['kr_can_edit' => $kr_can_edit]) ?>
                <?php if (!$key_results): ?>
                    <?= __d('gl', '達成要素は登録されていません') ?>
                <? endif ?>
            </div>
        </div>
        <div class="panel-body panel-read-more-body">
            <a href="#" class="btn btn-link click-goal-key-result-more"
               next-page-num="2"
               id="GoalPageKeyResultMoreLink"
               list-container="#GoalPageKeyResultContainer"
               goal-id="<?= h($goal['Goal']['id']) ?>"
               kr-can-edit="<?= h((int)$kr_can_edit) ?>"
                >
                <?= __d('gl', 'さらに読み込む') ?></a>
        </div>
    </div>
</div>
<!-- END app/View/Goals/view_krs.ctp -->
