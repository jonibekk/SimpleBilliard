<?php
/**
 * @var                    $members
 * @var CodeCompletionView $this
 */
?>
<div class="col goal-member-navigation">
    <div class="goal-member-navigation-link mod-active col-xxs-6 col-xs-4 col-xs-offset-2 col-sm-3 col-sm-offset-3">
        <a href="<?= $this->Html->url(
           [
               'controller' => 'goals',
               'action'     => 'view_members',
               'goal_id'    => $goal['Goal']['id'],
           ]); ?>"><?= h(__('Members')) ?> (<?= h($this->NumberEx->formatHumanReadable($member_count, ['convert_start' => 10000])) ?>)</a>
    </div>
    <div class="goal-member-navigation-link col-xxs-6 col-xs-4 col-sm-3">
        <a href="<?= $this->Html->url(
           [
               'controller' => 'goals',
               'action'     => 'view_followers',
               'goal_id'    => $goal['Goal']['id'],
           ]); ?>"><?= h(__('Follower')) ?> (<?= h($this->NumberEx->formatHumanReadable($follower_count, ['convert_start' => 10000])) ?>)</a>
    </div>
</div>
<?php if ($members): ?>
    <?= $this->App->viewStartComment() ?>
    <?php foreach ($members as $member): ?>
        <div class="goal-detail-member-card">
            <div>
                <a href="<?= $this->Html->url([
                    'controller' => 'users',
                    'action'     => 'view_goals',
                    'user_id'    => $member['User']['id']
                ]) ?>"
                   class="link-dark-gray">
                    <?=
                    $this->Upload->uploadImage($member['User'], 'User.photo', ['style' => 'medium_large'],
                        ['class' => 'goal-detail-member-avatar',])
                    ?>
                </a>

                <div class="goal-detail-member-info">
                    <a href="<?= $this->Html->url([
                        'controller' => 'users',
                        'action'     => 'view_goals',
                        'user_id'    => $member['User']['id']
                    ]) ?>"
                       class="link-dark-gray">
                        <span class="goal-detail-member-name"><?= h($member['User']['display_username']) ?></span>
                        <?php if ($member['GoalMember']['type'] == GoalMember::TYPE_OWNER): ?>
                            <span class="goal-detail-member-owner">
                            <i class="fa fa-star"></i>
                        </span>
                        <?php endif ?>
                    </a>

                    <p class="font_bold"><?= h($member['GoalMember']['role']) ?></p>

                    <p class="goal-detail-member-collab-wa showmore-xtra-mini">
                        <?= nl2br(h($member['GoalMember']['description'])) ?>
                    </p>
                </div>
            </div>
        </div>
    <?php endforeach ?>
    <?= $this->App->viewEndComment() ?>
<?php endif ?>
