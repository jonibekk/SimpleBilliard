<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 7/9/15
 * Time: 3:33 PM
 *
 * @var $goal
 * @var $followers
 */
?>
<?= $this->App->viewStartComment()?>
<div class="col-sm-8 col-sm-offset-2">
    <div class="panel panel-default">
        <?= $this->element('Goal/simplex_top_section') ?>
        <div class="panel-body goal-detail-follower-panel">
            <div class="goal-detail-follower-cards row borderBottom" id="GoalPageFollowerContainer">
                <?= $this->element('Goal/followers') ?>
                <?php if (!$followers): ?>
                    <?= __('No one is following.') ?>
                <? endif ?>
            </div>
        </div>
        <div class="panel-body panel-read-more-body">
            <a href="#" class="btn-link click-goal-follower-more goal-detail-panel-read-more"
               next-page-num="2"
               id="GoalPageFollowerMoreLink"
               list-container="#GoalPageFollowerContainer"
               goal-id="<?= h($goal['Goal']['id']) ?>"
            >
                <?= __('View more') ?></a>
        </div>
    </div>
</div>
<?= $this->App->viewEndComment()?>
