<div id="message-list-app" ng-app="messageListApp">
    <?= $this->element('Feed/common_form', [
        'common_form_type'     => 'message',
        'common_form_only_tab' => 'message'
    ]) ?>
    <div ui-view>
        <center><i id="__feed_loader" class="fa fa-refresh fa-spin"></i></center>'
    </div>
</div>
