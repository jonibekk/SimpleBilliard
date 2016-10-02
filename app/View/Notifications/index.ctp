<?php
/**
 * Created by PhpStorm.
 * User: saeki
 * Date: 15/04/27
 * Time: 14:22
 *
 * @var                    $notify_items
 * @var                    $isExistMoreNotify
 * @var CodeCompletionView $this
 */
?>

<?= $this->App->viewStartComment()?>

<div class="panel panel-default">
    <div class="panel-heading">
        <div class="noitify-mark-allread-wrap">
            <i class="fa fa-check btn-link notify-mark-allread" id="mark_all_read" style='color:#d2d4d5'></i>
        </div>
        <?= __("Your Notifications") ?>
    </div>
    <div class="panel-body panel-body-notify-page">
        <ul class="notify-page-cards" role="menu">
            <?=
            $this->element('Notification/notify_items', ['user' => $notify_items, 'location_type' => 'page']) ?>
        </ul>
        <?php if ($isExistMoreNotify): ?>
            <div class="panel-read-more-body">
                <span class="none" id="ShowMoreNoData"><?= __("There is no more data.") ?></span>
                <a id="FeedMoreReadLink" href="#" class="btn btn-link font_bold click-notify-read-more-page"
                   get-url="<?=
                   $this->Html->url(['controller' => 'notifications', 'action' => 'ajax_get_old_notify_more']) ?>"
                >
                    <?= __("View more") ?>▼</a>
            </div>
        <?php endif; ?>
    </div>
</div>
<?= $this->element('file_upload_form') ?>
<?= $this->App->viewEndComment()?>
