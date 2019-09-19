<?= $this->App->viewStartComment() ?>
<?php $isUnread = ($circle['unread_count'] > 0); ?>
<li class="dashboard-circle-list-row-wrap circle-layout <?= $isHamburger ? 'circleListMore' : '' ?>" circle_id="<?= $circle['id'] ?>">
    <a class="dashboard-circle-list-row <?= $isUnread ? 'is-unread' : 'is-read' ?> <?= $isHamburger ? 'is-hamburger' : '' ?>"
       image-url="<?= $circle['image'] ?>"
       title="<?= h($circle['name']) ?>"
       circle-id="<?= $circle['id'] ?>"
       public-flg="<?= $circle['public_flg'] ?>"
       team-all-flg="<?= $circle['team_all_flg'] ?>"
       oldest-post-time="<?= $circle['created'] ?>"
       href="<?= $this->Html->url("/circles/{$circle['id']}/posts") ?>">
        <div class="dashboard-circle-unread-point">
            <div class="circle"></div>
        </div>
        <p class="dashboard-circle-name-box <?= $isHamburger ? 'is-hamburger' : '' ?>"
           title="<?= h($circle['name']) ?>"><?= h($circle['name']) ?>
        </p>
        <div class="dashboard-circle-count-box-wrapper">
            <div class="dashboard-circle-count-box js-circle-count-box">
                <?php if ($isUnread): ?>
                    <?php $unreadCount = $circle['unread_count']; ?>
                    <?php if ($unreadCount > 0): ?>
                        <?= $this->NumberEx->addPlusIfOverLimit($unreadCount, $limit = 9); ?>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </a>
</li>
<?= $this->App->viewEndComment() ?>
