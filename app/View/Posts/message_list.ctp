<div id="message-list-app" ng-app="messageListApp">
    <?= $this->element('Feed/common_form', [
        'common_form_type'     => 'message',
        'common_form_only_tab' => 'message'
    ]) ?>
    <div ui-view> ロード中....</div>
</div>
