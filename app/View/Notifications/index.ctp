<?php
/**
 * Created by PhpStorm.
 * User: saeki
 * Date: 15/04/27
 * Time: 14:22
 *
 * @var $notify_items
 */
?>

<!-- START app/View/Notifications/index.ctp -->

<div class="panel panel-default">
    <div class="panel-heading">
        <?= __d('gl', "お知らせ") ?>
    </div>
    <div class="panel-body">
        <?=
        $this->element('Notification/notify_items',
                       ['user' => $notify_items]) ?>
        <hr>
    </div>
    <div class="panel-footer">
    </div>
</div>

<!-- END app/View/Notifications/index.ctp -->
