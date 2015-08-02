<?php
/**
 * @var                    $members
 * @var CodeCompletionView $this
 */
?>
<?php if ($members): ?>
    <!-- START app/View/Elements/Goal/members.ctp -->
    <?php foreach ($members as $member): ?>
        <div class="goal-detail-member-card">
            <a href="<?= $this->Html->url(['controller' => 'users', 'action' => 'view_goals', 'user_id' => $member['User']['id']]) ?>"
               class="link-dark-gray">
                <div>
                    <?=
                    $this->Upload->uploadImage($member['User'], 'User.photo', ['style' => 'medium_large'],
                                               ['class' => 'goal-detail-member-avator',])
                    ?>
                    <div class="goal-detail-member-info">
                        <span class="goal-detail-member-name"><?= h($member['User']['display_username']) ?></span>
                        <?php if ($member['Collaborator']['type'] == Collaborator::TYPE_OWNER): ?>
                            <span class="goal-detail-member-owner">
                            <i class="fa fa-star"></i>
                        </span>
                        <?php endif ?>
                        <p class="font_bold"><?= h($member['Collaborator']['role']) ?></p>

                        <p class="goal-detail-member-collab-way">
                            <?= h($member['Collaborator']['description']) ?>
                        </p>
                    </div>
                </div>
            </a>
        </div>
    <?php endforeach ?>
    <!-- END app/View/Elements/Goal/members.ctp -->
<? endif ?>
