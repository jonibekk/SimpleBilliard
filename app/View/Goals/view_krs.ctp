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
        <div class="panel-body">
            <?= __d('gl', 'ゴール進捗率') ?>: <?= h($goal['Goal']['progress']) ?>%
            <div class="progress mb_0px goals-column-progress-bar">
                <div class="progress-bar progress-bar-info" role="progressbar"
                     aria-valuenow="<?= h($goal['Goal']['progress']) ?>" aria-valuemin="0"
                     aria-valuemax="100" style="width: <?= h($goal['Goal']['progress']) ?>%;">
                    <span class="ml_12px"><?= h($goal['Goal']['progress']) ?>%</span>
                </div>
            </div>
            <?php $edit_kr = ($is_leader || $is_collaborator); ?>
            <?php if ($edit_kr): ?>
                <div>
                    <a class="col col-xxs-12 bd-dash font_lightGray-gray p_10px modal-ajax-get-add-key-result"
                       href="<?= $this->Html->url(['controller' => 'goals', 'action' => 'ajax_get_add_key_result_modal', 'goal_id' => $goal['Goal']['id']]) ?>">
                        <i class="fa fa-plus-circle font_brownRed"></i>
                        <span class="ml_2px"><?= __d('gl', "出したい成果を追加") ?></span>
                    </a>
                </div>
            <?php endif ?>
            <?= __d('gl', '出したい成果') ?>
            <div class="row borderBottom" id="GoalPageKeyResultContainer">
                <?= $this->element('Goal/key_results', ['edit_kr' => $edit_kr]) ?>
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
<!-- END app/View/Goals/view_krs.ctp -->
