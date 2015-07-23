<?php echo $this->Html->script('app/message'); ?>
<?php echo $this->Html->script('controller/message_detail'); ?>

<script type="text/javascript">
    document.getElementById('SubHeaderMenu').style.display = "none";
</script>

<div ng-app="messageApp">
    <div ui-view> ロード中....</div>
</div>
