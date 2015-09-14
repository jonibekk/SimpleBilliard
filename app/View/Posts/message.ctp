<?php echo $this->Html->script('app/message'); ?>
<?php echo $this->Html->script('controller/message_detail'); ?>
<?php echo $this->Html->script('app/message_list'); ?>
<?php echo $this->Html->script('controller/message_list'); ?>
<?php echo $this->Html->script('vendor/angular/pusher-angular.min'); ?>
<?php echo $this->Html->script('vendor/angular/ng-infinite-scroll.min'); ?>

<div ng-app="messageApp">
    <div class="post-message-dest panel-body none" id="MessageFormShareUser">

        <?php  echo $this->element('Feed/add_messenger', [
            'common_form_type'     => 'message',
            'common_form_only_tab' => 'message'
        ]) ?>
        <!-- end Add messenger -->
<!--        <p>{{post_detail.Post.id}}</p>-->
    </div>
    <div ui-view> ロード中....</div>


    <?php echo $this->element('file_upload_form') ?>

</div>
