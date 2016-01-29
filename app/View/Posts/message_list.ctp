<?php //echo $this->Html->script('app/message_list'); // もともとコメントアウトしてある?>
<?php //echo $this->Html->script('ng_app.min'); ?>
<?php //echo $this->Html->script('ng_controller'); ?>
<?php //echo $this->Html->script('vendor/angular/pusher-angular.min'); ?>
<?php //echo $this->Html->script('vendor/angular/ng-infinite-scroll.min'); ?>

<div id="message-list-app" ng-app="messageListApp">
    <?= $this->element('Feed/common_form', [
        'common_form_type'     => 'message',
        'common_form_only_tab' => 'message'
    ]) ?>
    <div ui-view> ロード中....</div>
</div>
