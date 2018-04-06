<?= $this->App->viewStartComment() ?>
<?php foreach ($circles as $circle): ?>
    <?php $isUnread = ($circle['CircleMember']['unread_count'] > 0); ?>
    <div class="dashboard-circle-list-row-wrap circle-layout <?= $isHamburger ? 'circleListMore' : '' ?>"
         circle_id="<?= $circle['Circle']['id'] ?>">
        <?php if ($circle['CircleMember']['admin_flg']): ?>
            <a href="#"
               data-url="<?= $this->Html->url([
                   'controller' => 'circles',
                   'action'     => 'ajax_get_edit_modal',
                   'circle_id'  => $circle['Circle']['id']
               ]) ?>"
               class="dashboard-circle-list-edit-wrap modal-ajax-get-circle-edit">
                <i class="fa fa-cog dashboard-circle-list-edit"></i>
            </a>
        <?php endif; ?>
        <a class="dashboard-circle-list-row js-dashboard-circle-list <?= $isUnread ? 'is-unread' : 'is-read' ?> <?= $isHamburger ? 'is-hamburger' : '' ?>"
           get-url="<?= $this->Html->url([
               'controller' => 'posts',
               'action'     => 'feed',
               'circle_id'  => $circle['Circle']['id']
           ]) ?>"
           image-url="<?= $this->Upload->uploadUrl($circle, 'Circle.photo', ['style' => 'small']) ?>"
           title="<?= h($circle['Circle']['name']) ?>"
           circle-id="<?= $circle['Circle']['id'] ?>"
           public-flg="<?= $circle['Circle']['public_flg'] ?>"
           team-all-flg="<?= $circle['Circle']['team_all_flg'] ?>"
           oldest-post-time="<?= $circle['Circle']['created'] ?>"
           href="<?= $this->Html->url("/circle_feed/{$circle['Circle']['id']}") ?>">
            <div class="dashboard-circle-unread-point">
                <div class="circle"></div>
            </div>
            <p class="dashboard-circle-name-box <?= $isHamburger ? 'is-hamburger' : '' ?>"
               title="<?= h($circle['Circle']['name']) ?>"><?= h($circle['Circle']['name']) ?>
            </p>
            <div class="dashboard-circle-count-box-wrapper">
                <div class="dashboard-circle-count-box js-circle-count-box">
                    <?php if ($isUnread): ?>
                        <?php $unreadCount = $circle['CircleMember']['unread_count']; ?>
                        <?php if ($unreadCount > 0): ?>
                            <?= $this->NumberEx->addPlusIfOverLimit($unreadCount, $limit = 9); ?>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </a>
    </div>
<?php endforeach ?>
<?= $this->App->viewEndComment() ?>
