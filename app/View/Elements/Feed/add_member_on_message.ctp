<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 7/4/14
 * Time: 10:45 AM
 *
 * @var CodeCompletionView $this
 */
?>
<?= $this->App->viewStartComment()?>
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
                    $this->Form->submit(__("Add"),
                        ['class'    => 'btn btn-primary pull-right post-submit-button',
                         'id'       => 'MessageSubmit',
                         'disabled' => 'disabled'
                        ]) ?>
                </div>
            </div>
        </div>
        <?= $this->Form->end() ?>
    </div>
</div>
<?= $this->App->viewEndComment()?>
