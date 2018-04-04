<?= $this->App->viewStartComment() ?>
<?php $isUnread = ($circle['unread_count'] > 0); ?>
<li class="dashboard-circle-list-row-wrap circle-layout <?= $isHamburger ? 'circleListMore' : '' ?>" circle_id="<?= $circle['id'] ?>">
    <?php if ($circle['admin_flg']): ?>
        <a href="#"
           data-url="<?= $this->Html->url([
               'controller' => 'circles',
               'action'     => 'ajax_get_edit_modal',
               'circle_id'  => $circle['id']
           ]) ?>"
           class="dashboard-circle-list-edit-wrap modal-ajax-get-circle-edit">
            <i class="fa fa-cog dashboard-circle-list-edit"></i>
        </a>
    <?php endif; ?> 
    <a class="dashboard-circle-list-row js-dashboard-circle-list <?= $isUnread ? 'is-unread' : 'is-read' ?> <?= $isHamburger ? 'is-hamburger' : '' ?>"
       get-url="<?= $this->Html->url([
           'controller' => 'posts',
           'action'     => 'feed',
           'circle_id'  => $circle['id']
       ]) ?>"
       image-url="<?= $circle['image'] ?>"
       title="<?= h($circle['name']) ?>"
       circle-id="<?= $circle['id'] ?>"
       public-flg="<?= $circle['public_flg'] ?>"
       team-all-flg="<?= $circle['team_all_flg'] ?>"
       oldest-post-time="<?= $circle['modified'] ?>"
       href="<?= $this->Html->url("/circle_feed/{$circle['id']}") ?>">
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