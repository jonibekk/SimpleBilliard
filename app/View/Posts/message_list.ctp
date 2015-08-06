<?php echo $this->Html->script('app/message_list'); ?>
<?php echo $this->Html->script('controller/message_list'); ?>
<?php echo $this->Html->script('vendor/angular/pusher-angular.min'); ?>
<?php echo $this->Html->script('vendor/angular/ng-infinite-scroll.min'); ?>

<script type="text/javascript">
    document.getElementById('SubHeaderMenu').style.display = "none";
</script>

<div ng-app="messageListApp">
    <div ui-view> ロード中....</div>
</div>

