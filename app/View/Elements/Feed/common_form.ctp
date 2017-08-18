<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 7/4/14
 * Time: 10:45 AM
 *
 * @var CodeCompletionView $this
 * @var                    $current_circle
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
?>
<?= $this->App->viewStartComment() ?>
<div id="ActionFormWrapper">
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
                    <a href="/topics" class="switch-message-anchor">
                        <i class="fa fa-paper-plane-o"></i><?= __("Message") ?></a><span class="switch-arrow"></span>
                </li>
            </ul>
        </div>
        <!-- Tab panes -->
        <div class="tab-content">
            <?= $this->element('Goal/action_form_content', compact('is_edit_mode')) ?>

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
                            <button type="button" class="btn pull-left btn-photo-up"><i
                                    class="fa fa-paperclip post-camera-icon"></i>
                            </button>
                        </a>

                        <div class="row form-horizontal form-group post-share-range" id="PostShare">
                            <?=
                            $this->Form->submit($is_edit_mode ? __("Save") : __("Post"),
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
        </div>
    </div>
</div>
<?= $this->element('file_upload_form') ?>
<?= $this->App->viewEndComment() ?>
