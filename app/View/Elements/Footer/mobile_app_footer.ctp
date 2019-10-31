<?php
$footerMenuList = [
    [
        'name'  => 'home',
        'url'   => '/',
        'icon'  => '',
        'label' => __('Home')

    ],
    [
        'name'  => 'messages',
        'url'   => '/topics',
        'icon'  => 'send',
        'label' => __('Message')
    ],
    [
        'name'  => 'circles',
        'url'   => '/circles',
        'icon'  => 'group_work',
        'label' => __('Circle')
    ],
    [
        'name'  => 'notifications',
        'url'   => '/notifications',
        'icon'  => 'notifications',
        'label' => __('Notification')
    ],
    [
        'name'  => 'more',
        'url'   => '/others',
        'icon'  => 'dehaze',
        'label' => __('More')
    ],
];

$new_notify_message_cnt = $new_notify_message_cnt ?? 0;
$new_notify_cnt = $new_notify_cnt ?? 0;
$circle_badge_cnt = $circle_badge_cnt ?? 0;
$all_alert_cnt = $all_alert_cnt ?? 0;

$badgeCounts = [
    'messages'      => $new_notify_message_cnt,
    'notifications' => Router::url() === '/notifications' ? 0 : $new_notify_cnt,
    'circles'       => Router::url() === '/circles' ? 0 : $circle_badge_cnt,
    'more'          => $all_alert_cnt
]
?>

<footer class="mobile-app-footer" id="MobileAppFooter" data-is-always-hidden="false">
    <ul class="mobile-app-footer-list">
        <?php foreach ($footerMenuList as $menu): ?>
            <li class="mobile-app-footer-list-item">
                <a href="<?= $menu['url'] ?>"
                   class="mobile-app-footer-list-item-link <?= Router::url() === $menu['url'] ? 'active' : '' ?>">
                    <?php if ($menu['name'] === 'home') : ?>
                        <div class="material-icons mod-feed"></div>
                    <?php else: ?>
                        <i class="material-icons"><?= $menu['icon'] ?></i>
                    <?php endif; ?>
                    <span class=""><?= $menu['label'] ?></span>
                </a>
                <?php if (in_array($menu['name'], ['messages', 'notifications', 'circles', 'more'], false)) : ?>
                    <div class="btn btn-xs notify-function-numbers <?= $menu['name'] === 'circles' ? 'mod-small' : '' ?> js-mbAppFooter-setBadgeCnt-<?= $menu['name'] ?> <?= empty($badgeCounts[$menu['name']]) ? 'hidden' : '' ?>">
                        <span class="<?= $badgeCounts[$menu['name']] > 99 && $menu['name'] !== 'circles' ? 'oval' : '' ?>" id="<?= $menu['name'] === 'circles' ? 'nav-circle-badge-count' : '' ?>">
                           <?= $badgeCounts[$menu['name']] ?>
                         </span>
                    </div>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>
</footer>
