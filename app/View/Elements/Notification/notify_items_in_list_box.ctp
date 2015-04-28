<?php
/**
 * Created by PhpStorm.
 * User: saeki
 * Date: 15/04/28
 * Time: 18:46
 *
 * var @notify_items
 *
 */
?>

<!-- START app/View/Elements/Notification/notify_items_in_list_box.ctp -->
    <?=
    $this->element('Notification/notify_items',
                   ['notify_items' => $notify_items]) ?>
    <li class="divider notify-divider"></li>
    <li class="text-align_c notify-all-view-link">
        <a href="<?= $this->Html->url(['controller' => 'notifications', 'action' => 'index']) ?>">
            <?= __d('gl', "すべて見る") ?>
        </a>
    </li>

<!-- END app/View/Elements/Notification/notify_items_in_list_box.ctp -->
