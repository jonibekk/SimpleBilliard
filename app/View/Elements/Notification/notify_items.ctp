<?php
/**
 * Created by PhpStorm.
 * User: saeki
 * Date: 15/04/27
 * Time: 16:57
 *
 * @var CodeCompletionView $this
 * @var                    $notify_items
 * @var                    $location_type
 */
?>

<?= $this->App->viewStartComment() ?>
<?php foreach ($notify_items as $notify_item): ?>
    <?=
    $this->element('Notification/notify_item',
        [
            'user'          => Hash::get($notify_item, 'User'),
            'notification'  => $notify_item['Notification'],
            'location_type' => $location_type
        ]) ?>
<?php endforeach; ?>
<?= $this->App->viewEndComment() ?>
