<div ng-app="messageApp" id="message-app">
    <div class="panel-body none" id="MessageFormShareUser">

        <?php echo $this->element('Feed/add_member_on_message', [
            'common_form_type'     => 'message',
            'common_form_only_tab' => 'message'
        ]) ?>

    </div>
    <div ui-view>
        <center><i id="__feed_loader" class="fa fa-refresh fa-spin"></i></center>
    </div>


    <?php echo $this->element('file_upload_form') ?>

</div>
