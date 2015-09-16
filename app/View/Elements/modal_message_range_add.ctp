<?php
/**
 * Created by PhpStorm.
 *
 * @var                    $users
 * @var CodeCompletionView $this
 * @var                    $total_share_user_count
 */
?>
<!-- START app/View/Elements/modal_message_range.ctp -->
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close font_33px close-design" data-dismiss="modal" aria-hidden="true"><span
                    class="close-icon">&times;</span></button>
            <h4 class="modal-title font_18px font_bold"><?= __d('gl', "会話のメンバー (%s)", $total_share_user_count) ?>dfsdf</h4>
        </div>
        <div class="modal-body modal-feed-body">
            <div class="row borderBottom">
                <?php if (!empty($users)): ?>
                    <?php foreach ($users as $user): ?>
                        <?=
                        $this->element('Feed/read_like_user',
                                       ['user' => $user['User'], 'created' => null]) ?>
                    <?php endforeach ?>
                <?php endif; ?>
            </div>
        </div>

        <?php echo $this->Html->script('app/message'); ?>
        <?php echo $this->Html->script('controller/message_detail'); ?>
        <?php echo $this->Html->script('app/message_list'); ?>
        <?php echo $this->Html->script('controller/message_list'); ?>
        <?php echo $this->Html->script('vendor/angular/pusher-angular.min'); ?>
        <?php echo $this->Html->script('vendor/angular/ng-infinite-scroll.min'); ?>

        <div ng-app="messageApp">

            <div ui-view> ロード中....</div>

            <div class="post-message-dest panel-body">
                <div class="col col-xxs-10 col-xs-10 post-share-range-list" id="MessagePublicShareInputWrap">
                    <?= __d('gl', "To:") ?>
                    <?=
                    $this->Form->hidden('share_public', [
                        'id'    => 'select2Member',
                        'style' => "width: 85%"
                    ]) ?>
                    <?php $this->Form->unlockField('Message.share_public') ?>
                </div>
                <?= $this->Form->hidden('share_range', [
                    'id'    => 'messageShareRange',
                    'value' => 'public',
                ]) ?>
                <?php $this->Form->unlockField('Message.share_range') ?>
                <?php $this->Form->unlockField('socket_id') ?>
            </div>
            <?php echo $this->element('file_upload_form') ?>

        </div>
        <div class="modal-footer modal-feed-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?= __d('gl', "閉じる") ?></button>
        </div>
    </div>
</div>
<!-- END app/View/Elements/modal_message_range.ctp -->
