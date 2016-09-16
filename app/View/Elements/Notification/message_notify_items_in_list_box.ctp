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

<?= $this->App->viewStartComment()?>
<?= $this->element('Notification/notify_items', ['notify_items' => $notify_items, 'location_type' => 'dropdown']) ?>
<!--
    <li class="notify-card-empty" id="notifyCardEmpty">
       <i class="fa fa-smile-o font_33px mr_8px"></i><span
           class="notify-empty-text"><?= __("No new notification") ?></span>
    </li>
     -->
<a href="#" get-url="<?= $this->Html->url(['controller' => 'posts', 'action' => 'message_list#']) ?>"
   onclick="evMessageList()">
    <li class="message-all-view-link">
        <?= __("See All") ?>
    </li>
</a>

<?= $this->App->viewEndComment()?>
