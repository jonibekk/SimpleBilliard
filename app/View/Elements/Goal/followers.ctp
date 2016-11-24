<?php
/**
 * @var $followers
 */
?>
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
