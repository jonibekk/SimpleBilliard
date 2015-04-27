<?php
/**
 * Created by PhpStorm.
 * User: saeki
 * Date: 15/04/27
 * Time: 16:57
 *
 * @var $notify_items
 *
 */
?>

<!-- START app/View/Elements/Notification/bell_notification_items.ctp -->

<? foreach($notify_items as $notify_item): ?>
    <?=
    $this->element('Notification/notify_item',
                   ['user' => $notify_item['User'], 'notification' => $notify_item['Notification']]) ?>
<? endforeach; ?>

<!-- END app/View/Elements/Notification/bell_notification_items.ctp -->
