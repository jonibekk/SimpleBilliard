<?php
$footerMenuList = [
    [
        'name' => 'feed',
        'url'  => '/',
        'icon' => 'fa-newspaper-o',
        'label' => 'Feed'

    ],
    [
        'name' => 'messages',
        'url'  => '/topics',
        'icon' => 'fa-paper-plane',
        'label' => 'Message'
    ],
    [
        'name' => 'kr_progress',
        'url'  => '/goals/kr_progress',
        'icon' => 'fa-flag',
        'label' => 'My Goal'
    ],
    [
        'name' => 'notifications',
        'url'  => '/notifications',
        'icon' => 'fa-bell',
        'label' => 'Notifications'
    ],
    [
        'name' => 'mypage',
        'url'  => '/users/view_goals/user_id:' . $this->Session->read('Auth.User.id'),
        'icon' => 'fa-user',
        'label' => 'My Page'
    ],
];

$badgeCounts = [
    'messages' => $new_notify_message_cnt,
    'notifications' => Router::url() === '/notifications' ? 0 : $new_notify_cnt,
]
?>

<footer class="mobile-app-footer">
    <ul class="mobile-app-footer-list">
        <?php foreach ($footerMenuList as $menu): ?>
            <li class="mobile-app-footer-list-item">
                <a href="<?= $menu['url']?>" class="mobile-app-footer-list-item-link <?= Router::url() === $menu['url'] ? 'active' : '' ?>">
                    <i class="fa <?= $menu['icon']?> mobile-app-footer-list-item-icon"></i>
                    <span class="mobile-app-footer-list-item-name"><?= $menu['label']?></span>
                </a>
                <?php if(in_array($menu['name'], ['messages', 'notifications'], false)) :?>
                    <div class="btn btn-xs notify-function-numbers js-mbAppFooter-setBadgeCnt-<?=$menu['name']?> <?= empty($badgeCounts[$menu['name']]) ? 'hidden' : '' ?>">
                         <span>
                           <?= $badgeCounts[$menu['name']] ?>
                         </span>
                    </div>
                <?php endif;?>
            </li>
        <?php endforeach; ?>
    </ul>
</footer>
