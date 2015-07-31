<?php
/**
 * @var $followers
 */
?>
<?php if ($followers): ?>
    <!-- START app/View/Elements/Goal/followers.ctp -->
    <?php foreach ($followers as $follower): ?>
        <div class="goal-detail-follower-card">
            <?=
            $this->Upload->uploadImage($follower['User'], 'User.photo', ['style' => 'small'],
                                       ['class' => 'goal-detail-follower-avator'])
            ?>
            <div class="goal-detail-follower-info" style="padding:3px;">
                <p class="goal-detail-follower-name">
                    <?= h($follower['User']['display_username']) ?>
                </p>
                <i class="fa-sitemap fa"></i>
                <span class="goal-detail-follower-group">
                    <?= h($follower['Group']['name']) ?>
                </span>
            </div>
        </div>
    <?php endforeach ?>
    <!-- END app/View/Elements/Goal/followers.ctp -->
<? endif ?>
