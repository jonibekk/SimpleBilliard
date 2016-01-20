<!-- <?php echo $this->Html->script('app/message'); ?> -->
<?php echo $this->Html->script('ng_app.min'); ?>
<?php echo $this->Html->script('ng_controller'); ?>
<?php echo $this->Html->script('vendor/angular/pusher-angular.min'); ?>
<?php echo $this->Html->script('vendor/angular/ng-infinite-scroll.min'); ?>

<div ng-app="messageApp">
    <div class="panel-body none" id="MessageFormShareUser">

        <?php echo $this->element('Feed/add_member_on_message', [
            'common_form_type'     => 'message',
            'common_form_only_tab' => 'message'
        ]) ?>

    </div>
    <div ui-view> ロード中....</div>


    <?php echo $this->element('file_upload_form') ?>

</div>
