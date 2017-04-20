<?php
/**
 * @var $followers
 */
?>
<div class="col goal-member-navigation">
    <div class="goal-member-navigation-link col-xxs-6 col-xs-4 col-xs-offset-2 col-sm-3 col-sm-offset-3">
        <a href="<?= $this->Html->url(
           [
               'controller' => 'goals',
               'action'     => 'view_members',
               'goal_id'    => $goal['Goal']['id'],
           ]); ?>"><?= h(__('Members')) ?> <strong>(<?= h($this->NumberEx->formatHumanReadable($member_count, ['convert_start' => 10000])) ?>)</strong></a>
    </div>
    <div class="goal-member-navigation-link mod-active col-xxs-6 col-xs-4 col-sm-3">
        <a href="<?= $this->Html->url(
           [
               'controller' => 'goals',
               'action'     => 'view_followers',
               'goal_id'    => $goal['Goal']['id'],
           ]); ?>"><?= h(__('Follower')) ?> <strong>(<?= h($this->NumberEx->formatHumanReadable($follower_count, ['convert_start' => 10000])) ?>)</strong></a>
    </div>
</div>
<?php if ($followers): ?>
    <?= $this->App->viewStartComment() ?>
    <?php foreach ($followers as $follower): ?>
        <div class="goal-detail-follower-card">
            <a href="<?= $this->Html->url([
                'controller' => 'users',
                'action'     => 'view_goals',
                'user_id'    => $follower['User']['id']
            ]) ?>"
            class="link-dark-gray">
                <div>
                    <?=
                    $this->Upload->uploadImage($follower['User'], 'User.photo', ['style' => 'medium_large'],
                        ['class' => 'goal-detail-follower-avatar'])
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
            </a>
        </div>
    <?php endforeach ?>
    <?= $this->App->viewEndComment() ?>
<?php endif ?>
