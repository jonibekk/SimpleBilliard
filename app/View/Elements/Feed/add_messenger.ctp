<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 7/4/14
 * Time: 10:45 AM
 *
 * @var CodeCompletionView $this
 * @var                    $current_circle
 * @var                    $goal_list_for_action_option
 * @var string             $common_form_type     デフォルトで有効にするフォーム種類 (action, post, message)
 * @var string             $common_form_mode     新規登録 or 編集(edit)
 * @var string             $common_form_only_tab フォームのタブ表示を１つに絞る (action, post, message)
 */
?>
<!-- START app/View/Elements/Feed/add_messenger.ctp -->
<div class="panel panel-default global-form">
    <div class="tab-pane active" id="MessageForm">
        <?=
        $this->Form->create('Post', [
            'url'           => ['controller' => 'posts', 'action' => 'edit_message_users'],
            'inputDefaults' => [
                'div'       => 'form-group',
                'label'     => false,
                'wrapInput' => '',
                'class'     => 'form-control',
            ],
            'id'            => 'MessageDisplayForm',
            'type'          => 'file',
            'novalidate'    => true,
            'class'         => 'form-feed-notify'
        ]); ?>
        <div class="post-message-dest panel-body" id="MessageFormShare">
            <div class="col col-xxs-10 col-xs-10 post-share-range-list" id="MessagePublicShareInputWrap">

                <?=
                $this->Form->hidden('share_public', [
                    'id'    => 'selectOnlyMember',
                    'style' => "width: 115%"
                ]) ?>
                <?=
                $this->Form->input('post_id', [
                    'id'    => 'post_messenger',
                    'class' => "none",
                    'value' => '',
                    'type'  => 'text'
                ]) ?>

                <?php $this->Form->unlockField('Post.share_public') ?>
            </div>
            <?= $this->Form->hidden('share_range', [
                'id'    => 'messageShareRange',
                'value' => 'public',
            ]) ?>
            <?php $this->Form->unlockField('Post.share_range') ?>
            <?php $this->Form->unlockField('socket_id') ?>
        </div>

        <div class="post-panel-footer">
            <div class="font_12px" id="MessageFormFooter">
                <div class="row form-horizontal form-group post-share-range" id="MessageShare">
                    <?=
                    $this->Form->submit(__d('gl', "ADD"),
                                        ['class' => 'btn btn-primary pull-right post-submit-button', 'id' => 'MessageSubmit', 'disabled' => 'disabled']) ?>
                </div>
            </div>
        </div>
        <?= $this->Form->end() ?>
    </div>
</div>
<!-- END app/View/Elements/Feed/add_messenger.ctp -->
