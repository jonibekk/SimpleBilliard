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
<!-- START app/View/Goals/view_followers.ctp -->
<div class="col-sm-8 col-sm-offset-2">
    <div class="panel panel-default">
        <?= $this->element('Goal/simplex_top_section') ?>
        <div class="panel-body">
            <div class="row borderBottom" id="GoalPageFollowerContainer">
                <?= $this->element('Goal/followers') ?>
                <?php if (!$followers): ?>
                    <?= __d('gl', 'フォロワーはいません。') ?>
                <? endif ?>
            </div>
        </div>
        <div class="panel-body panel-read-more-body">
            <a href="#" class="btn btn-link click-goal-follower-more"
               next-page-num="2"
               id="GoalPageFollowerMoreLink"
               list-container="#GoalPageFollowerContainer"
               goal-id="<?= h($goal['Goal']['id']) ?>"
                >
                <?= __d('gl', 'さらに読み込む') ?></a>
        </div>
    </div>
</div>
<!-- END app/View/Goals/view_followers.ctp -->
