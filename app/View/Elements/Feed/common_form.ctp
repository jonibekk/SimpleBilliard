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
 * @var bool               $isGoalCreatedInCurrentTerm True if goal has created in current term
 * @var integer            $countCurrentTermGoalUnachieved
 * @var bool               $showGuidanceGoalCreate
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

$hasVideoResource = $this->request->data['hasVideoResource'] ?? false;
$hasPostResources = !empty($this->request->data['PostResources']);
?>
<?= $this->App->viewStartComment() ?>
<div id="ActionFormWrapper">
    <?php if ($hasVideoResource && $hasPostResources): ?>
        <?php foreach ($this->request->data['PostResources'] as $resource): ?>
            <div class="col pt_10px feed_img_only_one mb_12px">
                <?php
                // TODO: currently, we have only video resource https://jira.goalous.com/browse/GL-6601
                // TODO: check if this is the video resource
                // TODO: move to another .ctp files
                $elementIdOfVideo = sprintf('video_stream_%d_%d_%d', $resource['id'], $this->request->data['Post']['id'], time());
                if ($resource['aspect_ratio'] > 0) {
                    $paddingTop = 100 / $resource['aspect_ratio'];
                } else {
                    $paddingTop = 100;
                }
                $paddingTop = ($paddingTop > 100) ? 100 : $paddingTop;
                ?>
                <div class="video-responsive-container" style="padding-top: <?= $paddingTop ?>%">
                    <video id="<?= $elementIdOfVideo ?>" class="video-js vjs-default-skin vjs-big-play-centered video-responsive" controls playsinline preload="none" poster="<?= $resource["thumbnail"] ?>">
                        <?php foreach ($resource['video_sources'] as $videoSource/** @var VideoSource $videoSource */): ?>
                            <source src="/api/v1/video_streams/<?= $resource['id'] ?>/source?type=<?= $videoSource->getType()->getValue() ?>" type="<?= $videoSource->getType()->getValue() ?>">
                        <?php endforeach; ?>
                    </video>
                </div>
                <script>feedVideoJs('<?= $elementIdOfVideo ?>')</script>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
    <?php if (empty($canActionGoals)): ?>
    <div class="panel panel-default">
        <?php if ($showGuidanceGoalCreate): ?>
        <div class="panel-body hide-on-guidance-goal-create-close">
            <button type="button" id="guidance-goal-create-close" class="close" aria-label="Close">
                <span aria-hidden="true">×</span>
            </button>
            <h3 class="text-center" style="font-weight: 400;">
                <?= __("Let's create Goal") ?>
            </h3>
        </div>
        <div class="panel-body hide-on-guidance-goal-create-close">
            <p class="text-center">
                <?= __("Now, create Goal, share it, and get together.<br />Let's be a little more specific<br />about what your project wants to accomplish.") ?>
            </p>
        </div>
        <?php endif; ?>
        <div class="panel-body">
            <a class="btn btn-primary btn-block" href="/goals/create/step1">
                <?= __("Create Goal") ?>
            </a>
        </div>
        <div class="panel-body">
            <p class="text-center">
                <a class="" href="/goals?keyword=&category=&progress=unachieved&term=present">
                    <?= __('View Goals created by members (%s)', $countCurrentTermGoalUnachieved) ?>
                </a>
            </p>
        </div>
        <?php if ($showGuidanceGoalCreate): ?>
        
        <div class="panel-body hide-on-guidance-goal-create-close">
            <p class="text-left">
                <?= __("Japanese") ?>
            </p>
            <div id="youtube_create_goal">
                <iframe type="text/html" width="640" height="360"
                        src="https://www.youtube.com/embed/gPCmJFeqPBo?autoplay=0&controls=1&rel=0&showinfo=0"
                        frameborder="0"></iframe>
            </div>
            <p class="text-left">
                <?= __("English") ?>
            </p>
            <div id="youtube_create_goal">
                <iframe type="text/html" width="640" height="360"
                        src="https://www.youtube.com/embed/dszew-4QAvA?autoplay=0&controls=1&rel=0&showinfo=0"
                        frameborder="0"></iframe>
            </div>
        </div>
        <div class="panel-body hide-on-guidance-goal-create-close">
            <p class="text-center">
                <?= __('See more <a href="%s" target="_blank">Goalous help</a>', 'https://intercom.help/goalous/') ?>
            </p>
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>
    <?php if (0 < count($canActionGoals)): ?>
    <div class="panel panel-default global-form" id="GlobalForms">
        <div class="post-panel-heading ptb_7px plr_11px">
            <!-- Nav tabs -->
            <ul class="feed-switch clearfix plr_0px" role="tablist" id="CommonFormTabs">
                <li class="switch-action  <?= $only_tab_post ? 'none' : ''; ?>">
                    <a href="#ActionForm" role="tab" data-toggle="tab" class="switch-action-anchor click-target-focus" target-id="CommonActionName">
                       <i class="fa fa-check-circle"></i><?= __("Action") ?>
                    </a>
                    <span class="switch-arrow"></span>
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
                    'class'         => 'form-feed-notify'
                ]); ?>
                <div class="post-panel-body plr_11px ptb_7px">
                    <?=
                    $this->Form->input('body', [
                        'id'          => 'CommonPostBody',
                        'label'       => false,
                        'type'        => 'textarea',
                        'wrap'        => 'soft',
                        'rows'        => 1,
                        'placeholder' => __("Write something..."),
                        'class'       => 'form-control tiny-form-text-change post-form feed-post-form box-align change-warning',
                        "required"    => true,
                        'maxlength'   => 10000,
                    ])
                    ?>

                    <?= $this->Form->hidden('site_info_url', ['id' => 'PostSiteInfoUrl']) ?>
                    <?php $this->Form->unlockField('Post.site_info_url') ?>
                    <?= $this->Form->hidden('redirect_url', ['id' => 'PostRedirectUrl']) ?>
                    <?php $this->Form->unlockField('Post.redirect_url') ?>
                    <?php if($is_edit_mode): ?>
                        <div id="PostOgpSiteInfo" class="edit-post-ogp-site-info"></div>
                    <?php else: ?>
                        <div id="PostOgpSiteInfo" class="post-ogp-site-info"></div>
                    <?php endif; ?>
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
                                    'id'       => 'PostSubmit'
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
                <?php $this->Form->unlockField('video_stream_id') ?>
                <?php $this->Form->unlockField('Post.file_id') ?>
                <?php $this->Form->unlockField('deleted_file_id') ?>

                <?= $this->Form->end() ?>

            </div>
        </div>
    </div>
    <?php endif; ?>
</div>
<?= $this->element('file_upload_form') ?>
<?= $this->App->viewEndComment() ?>
