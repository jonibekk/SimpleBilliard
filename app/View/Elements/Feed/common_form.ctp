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

// 編集時に true
$is_edit_mode = isset($common_form_mode) && $common_form_mode == 'edit';

// 表示するタブを「アクション」のみにする
// 以下のいずれかの場合に true
//   1. 「アクション」の編集時
//   2. $common_form_only_tab == 'action' が指定された場合
$only_tab_action =
    ($is_edit_mode && $common_form_type == 'action') ||
    (isset($common_form_only_tab) && $common_form_only_tab == 'action');

// 表示するタブを「投稿」のみにする
// 以下のいずれかの場合に true
//   1. 「投稿」の編集時
//   2. $common_form_only_tab == 'post' が指定された場合
$only_tab_post =
    ($is_edit_mode && $common_form_type == 'post') ||
    (isset($common_form_only_tab) && $common_form_only_tab == 'post');

// 表示するタブを「メッセージ」のみする
// 以下のいずれかの場合に true
//   1. $common_form_only_tab == 'message' が指定された場合
$only_tab_message = (isset($common_form_only_tab) && $common_form_only_tab == 'message');
//メッセージ通知からajaxで呼ばれた場合に$goal_list_for_action_optionの変数がセットされなくなるのでその場合の対応
if (!isset($goal_list_for_action_option)) {
    $goal_list_for_action_option = [];
}
?>
<!-- START app/View/Elements/Feed/common_form.ctp -->
<div class="panel panel-default global-form" id="GlobalForms">
    <div class="post-panel-heading ptb_7px plr_11px">
        <!-- Nav tabs -->
        <ul class="feed-switch clearfix plr_0px" role="tablist" id="CommonFormTabs">
            <li class="switch-action <?php
            // ファイル上部の宣言部を参照
            if ($only_tab_post || $only_tab_message): ?>
                none
            <?php endif ?>">
                <a href="#ActionForm" role="tab" data-toggle="tab"
                   class="switch-action-anchor click-target-focus"
                   target-id="CommonActionName"><i
                        class="fa fa-check-circle"></i><?= __("Action") ?></a><span class="switch-arrow"></span>
            </li>
            <li class="switch-post <?php
            // ファイル上部の宣言部を参照
            if ($only_tab_action || $only_tab_message): ?>
                none
            <?php endif ?>">
                <a href="#PostForm" role="tab" data-toggle="tab"
                   class="switch-post-anchor click-target-focus"
                   target-id="CommonPostBody"><i
                        class="fa fa-comment-o"></i><?= __("Posts") ?></a><span class="switch-arrow"></span>
            </li>
            <li class="switch-message <?php
            // ファイル上部の宣言部を参照
            if ($only_tab_action || $only_tab_post): ?>
                none
            <?php endif ?><?php
            // ファイル上部の宣言部を参照
            if ($common_form_type == "message"): ?>
                active
            <?php endif ?>">
                <a href="#MessageForm" role="tab" data-toggle="tab"
                   class="switch-message-anchor click-target-focus"
                   target-id="s2id_autogen1"><i
                        class="fa fa-paper-plane-o"></i><?= __("Message") ?></a><span class="switch-arrow"></span>
            </li>
        </ul>
    </div>
    <!-- Tab panes -->
    <div class="tab-content">
        <div class="tab-pane fade" id="ActionForm">
            <?php if (count($goal_list_for_action_option) <= 1): ?>
                <div class="post-panel-body plr_11px ptb_7px">
                    <div class="alert alert-warning" role="alert">
                        <?= __('You have no goal.') ?>
                        <a href="<?= $this->Html->url(['controller' => 'goals', 'action' => 'add']) ?>"
                           class="alert-link"><?= __('Create a goal') ?></a>
                    </div>
                </div>
            <?php else: ?>
                <?= $this->Form->create('ActionResult', [
                    'url'           => $is_edit_mode
                        ? [
                            'controller'       => 'goals',
                            'action'           => 'edit_action',
                            'action_result_id' => $this->request->data['ActionResult']['id']
                        ]
                        : ['controller' => 'goals', 'action' => 'add_completed_action'],
                    'inputDefaults' => [
                        'div'       => 'form-group',
                        'label'     => false,
                        'wrapInput' => '',
                        'class'     => 'form-control',
                    ],
                    'id'            => 'CommonActionDisplayForm',
                    'type'          => 'file',
                    'novalidate'    => true,
                    'class'         => 'form-feed-notify'
                ]); ?>
                <div class="post-panel-body plr_11px ptb_7px">
                    <a href="#"
                       id="ActionImageAddButton"
                       class="post-action-image-add-button <?php
                       // 投稿編集モードの場合は画像選択の画面をスキップする
                       if ($is_edit_mode && $common_form_type == 'action'): ?>
                        skip
                        <?php endif ?>"
                       target-id="CommonActionSubmit,WrapActionFormName,WrapCommonActionGoal,CommonActionFooter,CommonActionFormShowOptionLink,ActionUploadFileDropArea"
                       delete-method="hide">
                        <span class="action-image-add-button-text"><i
                                class="fa fa-image action-image-add-button-icon"></i> <span><?= __(
                                    'Upload an image as your action') ?></span></span>
                    </a>
                </div>

                <div id="ActionUploadFilePhotoPreview" class="pull-left action-upload-main-image-preview"></div>

                <div id="WrapActionFormName" class="panel-body action-form-panel-body none pull-left action-input-name">
                    <?=
                    $this->Form->input('name', [
                        'id'                           => 'CommonActionName',
                        'label'                        => false,
                        'type'                         => 'textarea',
                        'wrap'                         => 'soft',
                        'rows'                         => 1,
                        'required'                     => true,
                        'placeholder'                  => __("Write an action..."),
                        'class'                        => 'form-control change-warning',
                        'data-bv-notempty-message'     => __("Input is required."),
                        'data-bv-stringlength'         => 'true',
                        'data-bv-stringlength-max'     => 10000,
                        'data-bv-stringlength-message' => __("It's over limit characters (%s).", 10000),
                    ])
                    ?>
                </div>
                <!-- 目印 -->
                <div id="ActionUploadFileDropArea" class="action-upload-file-drop-area">
                    <div class="panel-body action-form-panel-body form-group none" id="WrapCommonActionGoal">
                        <div class="input-group feed-action-goal-select-wrap">
                            <span class="input-group-addon" id=""><i class="fa fa-flag"></i></span>
                            <?=
                            $this->Form->input('goal_id', [
                                'label'                    => false,
                                'div'                      => false,
                                'required'                 => true,
                                'data-bv-notempty-message' => __("Input is required."),
                                'class'                    => 'form-control change-next-select-with-value',
                                'id'                       => 'GoalSelectOnActionForm',
                                'options'                  => $goal_list_for_action_option,
                                'target-id'                => 'KrSelectOnActionForm',
                                'toggle-target-id'         => 'WrapKrSelectOnActionForm',
                                'target-value'             =>
                                    isset($this->request->data['ActionResult']['key_result_id'])
                                        ? $this->request->data['ActionResult']['key_result_id']
                                        : "",
                                'ajax-url'                 =>
                                    $this->Html->url([
                                        'controller' => 'goals',
                                        'action'     => 'ajax_get_kr_list',
                                        'goal_id'    => ""
                                    ]),
                            ])
                            ?>
                        </div>
                    </div>
                    <div class="panel-body action-form-panel-body <?php
                    if (!(isset($kr_list) && $kr_list)): ?>
                        none
                    <?php endif ?>" id="WrapKrSelectOnActionForm">
                        <div class="input-group feed-action-kr-select-wrap">
                            <span class="input-group-addon" id=""><i class="fa fa-key"></i></span>
                            <?=
                            $this->Form->input('key_result_id', [
                                'label'    => false,
                                'div'      => false,
                                'required' => false,
                                'id'       => 'KrSelectOnActionForm',
                                'options'  => isset($kr_list) ? $kr_list : [null => __('Select a key result (optional)')],
                            ])
                            ?>
                        </div>
                    </div>

                    <?php
                    // 新規登録時のみ表示
                    if (!$is_edit_mode): ?>
                        <a href="#" class="graylink-dark- target-show click-this-remove none"
                           target-id="ActionFormOptionFields"
                           id="CommonActionFormShowOptionLink">
                            <div class="panel-body action-form-panel-body font_11px font_lightgray"
                                 id="CommonActionFormShare">
                                <p class="text-center"><?= __("View options") ?></p>

                                <p class="text-center"><i class="fa fa-chevron-down"></i></p>
                            </div>
                        </a>

                        <div id="ActionFormOptionFields" class="none">
                            <div class="panel-body action-form-panel-body" id="CommonActionFormShare">
                                <div class="col col-xxs-12 col-xs-12 post-share-range-list"
                                     id="CommonActionShareInputWrap">
                                    <div class="input-group action-form-share-input-group">
                                        <span class="input-group-addon" id=""><i class="fa fa-bullhorn"></i></span>

                                        <div class="form-control">
                                            <?=
                                            $this->Form->hidden('share',
                                                [
                                                    'id'    => 'select2ActionCircleMember',
                                                    'value' => "",
                                                    'style' => "width: 100%",
                                                ]) ?>
                                            <?php $this->Form->unlockField('ActionResult.share') ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif ?>

                    <div id="ActionUploadFilePreview" class="action-upload-file-preview">
                    </div>
                    <div class="post-panel-footer none" id="CommonActionFooter">
                        <div class="font_12px" id="CommonActionFormFooter">
                            <a href="#" class="link-red" id="ActionFileAttachButton">
                                <button type="button" class="btn pull-left photo-up-btn"><i
                                        class="fa fa-paperclip post-camera-icon"></i>
                                </button>
                            </a>

                            <div class="row form-horizontal form-group post-share-range" id="CommonActionShare">
                                <?=
                                $this->Form->submit(__($is_edit_mode ? "保存する" : "アクション登録"),
                                    [
                                        'class' => 'btn btn-primary pull-right post-submit-button',
                                        'id'    => 'CommonActionSubmit'
                                    ]) ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php if ($is_edit_mode): ?>
                    <?php if (isset($this->request->data['ActionResultFile']) && is_array($this->request->data['ActionResultFile'])): ?>
                        <?php foreach ($this->request->data['ActionResultFile'] as $file): ?>
                            <?= $this->Form->hidden('file_id', [
                                'id'        => 'AttachedFile_' . $file['AttachedFile']['id'],
                                'name'      => 'data[file_id][]',
                                'value'     => $file['AttachedFile']['id'],
                                'data-url'  => $this->Upload->uploadUrl($file, 'AttachedFile.attached',
                                    ['style' => 'small']),
                                'data-name' => $file['AttachedFile']['attached_file_name'],
                                'data-size' => $file['AttachedFile']['file_size'],
                                'data-ext'  => $file['AttachedFile']['file_ext'],
                            ]); ?>
                        <?php endforeach ?>
                    <?php endif; ?>
                <?php endif ?>
                <?php $this->Form->unlockField('socket_id') ?>
                <?php $this->Form->unlockField('file_id') ?>
                <?php $this->Form->unlockField('ActionResult.file_id') ?>
                <?php $this->Form->unlockField('deleted_file_id') ?>

                <?= $this->Form->end() ?>
            <?php endif; ?>
        </div>

        <div class="tab-pane fade" id="PostForm">
            <?=
            $this->Form->create('Post', [
                'url'           => $is_edit_mode && isset($this->request->data['Post']['id'])
                    ? [
                        'controller' => 'posts',
                        'action'     => 'post_edit',
                        'post_id'    => $this->request->data['Post']['id']
                    ]
                    : ['controller' => 'posts', 'action' => 'add'],
                'inputDefaults' => [
                    'div'       => 'form-group',
                    'label'     => false,
                    'wrapInput' => '',
                    'class'     => 'form-control',
                ],
                'id'            => 'PostDisplayForm',
                'type'          => 'file',
                'novalidate'    => true,
                'class'         => 'form-feed-notify'
            ]); ?>
            <div class="post-panel-body plr_11px ptb_7px">
                <?=
                $this->Form->input('body', [
                    'id'                           => 'CommonPostBody',
                    'label'                        => false,
                    'type'                         => 'textarea',
                    'wrap'                         => 'soft',
                    'rows'                         => 1,
                    'placeholder'                  => __("Write something..."),
                    'class'                        => 'form-control tiny-form-text-change post-form feed-post-form box-align change-warning',
                    "required"                     => true,
                    'data-bv-notempty-message'     => __("Input is required."),
                    'data-bv-stringlength'         => 'true',
                    'data-bv-stringlength-max'     => 10000,
                    'data-bv-stringlength-message' => __("It's over limit characters (%s).", 10000),
                ])
                ?>

                <?= $this->Form->hidden('site_info_url', ['id' => 'PostSiteInfoUrl']) ?>
                <?php $this->Form->unlockField('Post.site_info_url') ?>
                <?= $this->Form->hidden('redirect_url', ['id' => 'PostRedirectUrl']) ?>
                <?php $this->Form->unlockField('Post.redirect_url') ?>

                <div id="PostOgpSiteInfo" class="post-ogp-site-info"></div>
                <div id="PostUploadFilePreview" class="post-upload-file-preview"></div>
            </div>

            <?php
            // 新規登録時のみ表示
            if (!$is_edit_mode): ?>
                <div class="panel-body post-share-range-panel-body" id="PostFormShare">

                    <?php
                    // 共有範囲「公開」のデフォルト選択
                    // 「チーム全体サークル」以外のサークルフィードページの場合は、対象のサークルIDを指定。
                    // それ以外は「チーム全体サークル」(public)を指定する。
                    $public_share_default = 'public';
                    if (isset($current_circle) && $current_circle['Circle']['public_flg'] && !$current_circle['Circle']['team_all_flg']) {
                        $public_share_default = "circle_" . $current_circle['Circle']['id'];
                    }

                    // 共有範囲「秘密」のデフォルト選択
                    // 秘密サークルのサークルフィードページの場合は、対象のサークルIDを指定する。
                    $secret_share_default = '';
                    if (isset($current_circle) && !$current_circle['Circle']['public_flg']) {
                        $secret_share_default = "circle_" . $current_circle['Circle']['id'];
                    }
                    ?>
                    <div class="col col-xxs-10 col-xs-10 post-share-range-list" id="PostPublicShareInputWrap"
                         <?php if ($secret_share_default) : ?>style="display:none"<?php endif ?>>
                        <?=
                        $this->Form->hidden('share_public', [
                            'id'    => 'select2PostCircleMember',
                            'value' => $public_share_default,
                            'style' => "width: 100%"
                        ]) ?>
                        <?php $this->Form->unlockField('Post.share_public') ?>
                    </div>
                    <div class="col col-xxs-10 col-xs-10 post-share-range-list" id="PostSecretShareInputWrap"
                         <?php if (!$secret_share_default) : ?>style="display:none"<?php endif ?>>
                        <?=
                        $this->Form->hidden('share_secret', [
                            'id'    => 'select2PostSecretCircle',
                            'value' => $secret_share_default,
                            'style' => "width: 100%;"
                        ]) ?>
                        <?php $this->Form->unlockField('Post.share_secret') ?>
                    </div>
                    <div class="col col-xxs-2 col-xs-2 text-center post-share-range-toggle-button-container">
                        <?= $this->Html->link('', '#', [
                            'id'                  => 'postShareRangeToggleButton',
                            'class'               => "btn btn-lightGray btn-white post-share-range-toggle-button",
                            'data-toggle-enabled' => (isset($current_circle)) ? '' : '1',
                        ]) ?>
                        <?= $this->Form->hidden('share_range', [
                            'id'    => 'postShareRange',
                            'value' => $secret_share_default ? 'secret' : 'public',
                        ]) ?>
                    </div>
                    <?php $this->Form->unlockField('Post.share_range') ?>
                </div>
            <?php endif ?>

            <div class="post-panel-footer">
                <div class="font_12px" id="PostFormFooter">
                    <a href="#" class="link-red" id="PostUploadFileButton">
                        <button type="button" class="btn pull-left photo-up-btn"><i
                                class="fa fa-paperclip post-camera-icon"></i>
                        </button>
                    </a>

                    <div class="row form-horizontal form-group post-share-range" id="PostShare">
                        <?=
                        $this->Form->submit(__($is_edit_mode ? "Save" : "Post"),
                            [
                                'class'    => 'btn btn-primary pull-right post-submit-button',
                                'id'       => 'PostSubmit',
                                'disabled' => $is_edit_mode ? '' : 'disabled'
                            ]) ?>
                    </div>
                </div>
            </div>
            <?php if ($is_edit_mode): ?>
                <?php if (isset($this->request->data['PostFile']) && is_array($this->request->data['PostFile'])): ?>
                    <?php foreach ($this->request->data['PostFile'] as $file): ?>
                        <?= $this->Form->hidden('file_id', [
                            'id'        => 'AttachedFile_' . $file['AttachedFile']['id'],
                            'name'      => 'data[file_id][]',
                            'value'     => $file['AttachedFile']['id'],
                            'data-url'  => $this->Upload->uploadUrl($file, 'AttachedFile.attached',
                                ['style' => 'small']),
                            'data-name' => $file['AttachedFile']['attached_file_name'],
                            'data-size' => $file['AttachedFile']['file_size'],
                            'data-ext'  => $file['AttachedFile']['file_ext'],
                        ]); ?>
                    <?php endforeach ?>
                <?php endif; ?>
            <?php endif ?>
            <?php $this->Form->unlockField('socket_id') ?>
            <?php $this->Form->unlockField('file_id') ?>
            <?php $this->Form->unlockField('Post.file_id') ?>
            <?php $this->Form->unlockField('deleted_file_id') ?>

            <?= $this->Form->end() ?>
        </div>

        <div class="tab-pane <?php
        // ファイル上部の宣言部を参照
        if ($common_form_type == "message"): ?>
                active
        <?php else: ?>
                fade
        <?php endif ?>" id="MessageForm">

            <?=
            $this->Form->create('Post', [
                'url'           => ['controller' => 'posts', 'action' => 'add_message'],
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
                    <?= __("To:") ?>
                    <?=
                    $this->Form->hidden('share_public', [
                        'id'    => 'select2Member',
                        'style' => "width: 85%",
                        'value' => !empty($targetUserId) ? 'user_'.$targetUserId : ''
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

            <div id="messageDropArea">
                <div class="post-panel-body plr_11px ptb_7px">
                    <?=
                    $this->Form->input('body', [
                        'id'                           => 'CommonMessageBody',
                        'label'                        => false,
                        'type'                         => 'textarea',
                        'wrap'                         => 'soft',
                        'rows'                         => 1,
                        'required'                     => true,
                        'placeholder'                  => __("Write a message..."),
                        'class'                        => 'form-control tiny-form-text-change post-form feed-post-form box-align change-warning',
                        'target_show_id'               => "MessageFormFooter",
                        'data-bv-notempty-message'     => __("Input is required."),
                        'data-bv-stringlength'         => 'true',
                        'data-bv-stringlength-max'     => 5000,
                        'data-bv-stringlength-message' => __("It's over limit characters (%s).", 5000),
                    ]);
                    ?>
                    <div id="messageUploadFilePreviewArea"></div>
                </div>
                <div class="post-panel-footer">
                    <div class="font_12px none" id="MessageFormFooter">
                        <a href="#" class="link-red" id="messageUploadFileButton">
                            <button type="button" class="btn pull-left photo-up-btn"><i
                                    class="fa fa-paperclip post-camera-icon"></i>
                            </button>
                        </a>

                        <div class="row form-horizontal form-group post-share-range" id="MessageShare">
                            <?=
                            $this->Form->submit(__("Send Message"),
                                [
                                    'class'    => 'btn btn-primary pull-right post-submit-button',
                                    'id'       => 'MessageSubmit',
                                    'disabled' => 'disabled'
                                ]) ?>
                        </div>
                    </div>
                </div>
            </div>

            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
<?= $this->element('file_upload_form') ?>
<!-- END app/View/Elements/Feed/common_form.ctp -->
