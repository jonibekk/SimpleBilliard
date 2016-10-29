<?php
/**
 * @var                    $members
 * @var CodeCompletionView $this
 */
?>
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
<? endif ?>
