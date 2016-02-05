<div ng-app="messageApp" id="message-app">
    <div class="panel-body none" id="MessageFormShareUser">

        <?php echo $this->element('Feed/add_member_on_message', [
            'common_form_type'     => 'message',
            'common_form_only_tab' => 'message'
        ]) ?>

    </div>
    <div ui-view> ロード中....</div>


    <?php echo $this->element('file_upload_form') ?>

</div>
