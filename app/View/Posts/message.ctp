<?php echo $this->Html->script('app/message'); ?>
<?php echo $this->Html->script('controller/message_detail'); ?>
<?php echo $this->Html->script('vendor/angular/pusher-angular.min'); ?>
<?php echo $this->Html->script('vendor/angular/ng-infinite-scroll.min'); ?>

<div ng-app="messageApp">
    <div ui-view> ロード中....</div>
</div>