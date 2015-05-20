<?php
/**
 * Created by PhpStorm.
 * User: saeki
 * Date: 15/04/27
 * Time: 14:22
 *
 * @var $notify_items
 * @var $isExistMoreNotify
 */
?>

<!-- START app/View/Notifications/index.ctp -->

<div class="panel panel-default">
    <div class="panel-heading">
        <?= __d('gl', "すべてのお知らせ") ?>
    </div>
    <div class="panel-body panel-body-notify-page">
        <ul class="notify-list-page" role="menu">
            <?=
            $this->element('Notification/notify_items',
                           ['user' => $notify_items]) ?>
        </ul>
        <?php if ($isExistMoreNotify): ?>
            <div class="feed-read-more">
                <div class="panel-read-more-body">
                    <span class="none" id="ShowMoreNoData"><?= __d('gl', "これ以上のデータがありません。") ?></span>
                    <a href="#" class="btn btn-link click-notify-read-more"
                       get-url="<?=
                       $this->Html->url(['controller' => 'notifications', 'action' => 'ajax_get_old_notify_more']) ?>"
                        >
                        <?= __d('gl', "もっと見る ▼") ?></a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- END app/View/Notifications/index.ctp -->
