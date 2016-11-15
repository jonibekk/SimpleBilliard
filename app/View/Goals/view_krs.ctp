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
 * @var $is_goal_member
 */
?>
<?= $this->App->viewStartComment() ?>
<div class="col-sm-8 col-sm-offset-2">
    <div class="panel panel-default">
        <?= $this->element('Goal/simplex_top_section') ?>
        <div class="panel-body goal-detail-kr-panel">
            <p class="goal-detail-kr-progress-score">
                <?= __('Goal progress rate') ?>: <?= h($goal['Goal']['progress']) ?>%
            </p>
            <div class="progress mb_0px goals-column-progress-bar goal-detail-goal-progress-bar-wrap">
                <div class="progress-bar progress-bar-info goal-detail-goal-progress-bar" role="progressbar"
                     aria-valuenow="<?= h($goal['Goal']['progress']) ?>" aria-valuemin="0"
                     aria-valuemax="100" style="width: <?= h($goal['Goal']['progress']) ?>%;">
                    <span class="ml_12px"><?= h($goal['Goal']['progress']) ?>%</span>
                </div>
            </div>
            <div class="clearfix mt_12px">
                <h3 class="goal-detail-kr-add-heading pull-left">
                    <i class="fa fa-key"></i>
                    <?= __('Key Results') ?>(<?= count($key_results)?>)
                    <!-- todo 数を追加 -->
                </h3>
                <?php $kr_can_edit = ($is_leader || $is_goal_member); ?>
                <?php if ($kr_can_edit): ?>
                    <div class="pull-right">
                        <a class="modal-ajax-get-add-key-result"
                           href="<?= $this->Html->url([
                               'controller' => 'goals',
                               'action'     => 'ajax_get_add_key_result_modal',
                               'goal_id'    => $goal['Goal']['id']
                           ]) ?>">
                            <i class="fa fa-plus btn-add-kr-icon"></i>
                            <span><?= __("Add Key Result") ?></span>
                        </a>
                    </div>
                <?php endif ?>
            </div>

            <div class="row borderBottom" id="GoalPageKeyResultContainer">
                <?= $this->element('Goal/key_results', ['kr_can_edit' => $kr_can_edit]) ?>
                <?php if (!$key_results): ?>
                    <?= __('There is no KR.') ?>
                <?php endif ?>
            </div>
        </div>
        <div class="panel-body panel-read-more-body goal-detail-panel-read-more">
            <a href="#" class="btn btn-link click-goal-key-result-more"
               next-page-num="2"
               id="GoalPageKeyResultMoreLink"
               list-container="#GoalPageKeyResultContainer"
               goal-id="<?= h($goal['Goal']['id']) ?>"
               kr-can-edit="<?= h((int)$kr_can_edit) ?>"
            >
                <?= __('View more') ?></a>
        </div>
    </div>
</div>
<?= $this->App->viewEndComment() ?>
