<?php
/**
 * Created by PhpStorm.
 * User: saeki
 * Date: 15/04/28
 * Time: 18:46
 *
 * @var CodeCompletionView $this
 * @var                    $notify_items
 */
?>

<!-- START app/View/Elements/Notification/notify_items_in_list_box.ctp -->
<?= $this->element('Notification/notify_items', ['notify_items' => $notify_items, 'location_type' => 'dropdown']) ?>
<!-- END app/View/Elements/Notification/notify_items_in_list_box.ctp -->
