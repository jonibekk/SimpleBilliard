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
<?= $this->App->viewEndComment()?>
