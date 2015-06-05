<?php
/**
 * Created by PhpStorm.
 * User: saeki
 * Date: 15/04/28
 * Time: 18:46
 * var @notify_items
 */
?>

<!-- START app/View/Elements/Notification/notify_items_in_list_box.ctp -->
<?= $this->element('Notification/notify_items', ['notify_items' => $notify_items, 'location_type' => 'dropdown']) ?>
<!--
    <li class="notify-card-empty" id="notifyCardEmpty">
       <i class="fa fa-smile-o font_33px mr_8px"></i><span
           class="notify-empty-text"><?= __d('gl', "未読の通知はありません。") ?></span>
    </li>
     -->
<a href="<?= $this->Html->url(['controller' => 'notifications', 'action' => 'index']) ?>">
    <li class="text-align_c notify-all-view-link">
        <?= __d('gl', "すべて見る") ?>
    </li>
</a>

<!-- END app/View/Elements/Notification/notify_items_in_list_box.ctp -->
