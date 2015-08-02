<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 7/9/15
 * Time: 3:33 PM
 *
 * @var $goal
 * @var $members
 */
?>
<!-- START app/View/Goals/view_members.ctp -->
<div class="col-sm-8 col-sm-offset-2">
    <div class="panel panel-default">
        <?= $this->element('Goal/simplex_top_section') ?>
        <div class="panel-body goal-detail-member-panel">
            <div class="goal-detail-member-cards row borderBottom" id="GoalPageMemberContainer">
                <?= $this->element('Goal/members') ?>
            </div>
        </div>
        <div class="panel-body panel-read-more-body goal-detail-panel-read-more">
            <a href="#" class="btn btn-link click-goal-member-more"
               next-page-num="2"
               id="GoalPageMemberMoreLink"
               list-container="#GoalPageMemberContainer"
               goal-id="<?= h($goal['Goal']['id']) ?>"
                >
                <?= __d('gl', 'さらに読み込む') ?></a>
        </div>
    </div>
</div>
<!-- END app/View/Goals/view_members.ctp -->
