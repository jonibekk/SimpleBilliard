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

<?= $this->App->viewStartComment() ?>
<?php if (empty($notify_items)): ?>
    <div class="empty">
        <div class="empty-box">
            <i class="material-icons">notifications</i>
            <p><?= __("No notifications")?></p>
        </div>
    </div>
<?php else: ?>
    <?= $this->element('Notification/notify_items', ['notify_items' => $notify_items, 'location_type' => 'dropdown']) ?>
<?php endif; ?>
<?= $this->App->viewEndComment() ?>
