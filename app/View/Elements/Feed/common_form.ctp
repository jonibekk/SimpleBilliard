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
 */
?>
<!-- START app/View/Elements/Feed/common_form.ctp -->
<div class="panel panel-default global-form">
    <div class="post-panel-heading ptb_7px plr_11px">
        <!-- Nav tabs -->
        <ul class="feed-switch clearfix plr_0px" role="tablist" id="CommonFormTabs">
            <li class="switch-action"><a href="#ActionForm" role="tab" data-toggle="tab"
                                         class="switch-action-anchor click-target-focus"
                                         target-id="CommonActionName"><i
                        class="fa fa-check-circle"></i><?= __d('gl', "アクション") ?></a><span class="switch-arrow"></span>
            </li>
            <li class="switch-post"><a href="#PostForm" role="tab" data-toggle="tab"
                                       class="switch-post-anchor click-target-focus"
                                       target-id="CommonPostBody"><i
                        class="fa fa-comment-o"></i><?= __d('gl', "投稿") ?></a><span class="switch-arrow"></span></li>
        </ul>
    </div>
    <!-- Tab panes -->
    <div class="tab-content">
        <div class="tab-pane fade upload-file-drop-area" id="ActionForm"  data-preview-area-id="ActionUploadFilePreview"
             data-submit-form-id="CommonActionDisplayForm">
            <?php if (count($goal_list_for_action_option) == 1): ?>
                <div class="post-panel-body plr_11px ptb_7px">
                    <div class="alert alert-warning" role="alert">
                        <?= __d('gl', '今期のゴールがありません。') ?>
                        <a href="<?= $this->Html->url(['controller' => 'goals', 'action' => 'add']) ?>"
                           class="alert-link"><?= __d('gl', 'ゴールを作成する') ?></a>
                    </div>
                </div>
            <?php else: ?>
                <?= $this->Form->create('ActionResult', [
                    'url'           => ['controller' => 'goals', 'action' => 'add_completed_action'],
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
                       class="target-show-target-click btn btn-link btn-lightGray bd-radius_4px click-this-remove"
                       target-id="CommonActionFormImage,CommonActionSubmit,WrapActionFormName,WrapCommonActionGoal,CommonActionFooter,CommonActionFormShowOptionLink"
                       click-target-id="ActionResult__Photo_1">
                        <i class="fa fa-camera"></i>
                        <?= __d('gl', "画像を選択しよう！") ?>
                    </a>

                    <div class="row form-group m_0px none" id="CommonActionFormImage">
                        <ul class="col input-images post-images">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                            <li id="WrapPhotoForm_Action_<?= $i ?>">
                                <?= $this->element('Feed/photo_upload',
                                                   ['type' => 'action_result', 'index' => $i, 'submit_id' => 'CommonActionSubmit']) ?>
                                </li><?php endfor ?>
                        </ul>
                        <span class="help-block" id="ActionResult__Photo_ValidateMessage"></span>

                        <div id="ActionUploadFilePreview" class="post-upload-file-preview"></div>
                        <?php $this->Form->unlockField('file_id') ?>
                    </div>
                </div>
                <div id="WrapActionFormName" class="panel-body action-form-panel-body none">
                    <?=
                    $this->Form->input('name', [
                        'id'                       => 'CommonActionName',
                        'label'                    => false,
                        'type'                     => 'textarea',
                        'wrap'                     => 'soft',
                        'rows'                     => 1,
                        'required'                 => true,
                        'placeholder'              => __d('gl', "アクションを説明しよう"),
                        'class'                    => 'form-control change-warning',
                        "required"                 => true,
                        'data-bv-notempty-message' => __d('validate', "入力必須項目です。"),
                    ])
                    ?>
                </div>

                <div class="panel-body action-form-panel-body form-group none" id="WrapCommonActionGoal">
                    <div class="input-group">
                        <span class="input-group-addon" id=""><i class="fa fa-flag"></i></span>
                        <?=
                        $this->Form->input('goal_id', [
                            'label'                    => false,
                            'div'                      => false,
                            'required'                 => true,
                            'data-bv-notempty-message' => __d('validate', "入力必須項目です。"),
                            'class'                    => 'form-control change-next-select-with-value',
                            'id'                       => 'GoalSelectOnActionForm',
                            'options'                  => $goal_list_for_action_option,
                            'target-id'                => 'KrSelectOnActionForm',
                            'toggle-target-id'         => 'WrapKrSelectOnActionForm',
                            'ajax-url'                 => $this->Html->url(['controller' => 'goals', 'action' => 'ajax_get_kr_list', 'goal_id' => ""]),
                        ])
                        ?>
                    </div>
                </div>
                <div class="panel-body action-form-panel-body none" id="WrapKrSelectOnActionForm">
                    <div class="input-group">
                        <span class="input-group-addon" id=""><i class="fa fa-key"></i></span>
                        <?=
                        $this->Form->input('key_result_id', [
                            'label'    => false,
                            'div'      => false,
                            'required' => false,
                            'id'       => 'KrSelectOnActionForm',
                            'options'  => [null => __d('gl', '出したい成果を選択する(オプション)')],
                        ])
                        ?>
                    </div>
                </div>
                <a href="#" class="link-dark-gray target-show click-this-remove none" target-id="ActionFormOptionFields"
                   id="CommonActionFormShowOptionLink">
                    <div class="panel-body action-form-panel-body font_11px font_lightgray"
                         id="CommonActionFormShare">
                        <p class="text-center"><?= __d('gl', "オプションを表示") ?></p>

                        <p class="text-center"><i class="fa fa-chevron-down"></i></p>
                    </div>
                </a>
                <div id="ActionFormOptionFields" class="none">
                    <div class="panel-body action-form-panel-body" id="CommonActionFormShare">
                        <div class="col col-xxs-12 col-xs-12 post-share-range-list" id="CommonActionShareInputWrap">
                            <?=
                            $this->Form->hidden('share',
                                                ['id' => 'select2ActionCircleMember', 'value' => "", 'style' => "width: 100%",]) ?>
                            <?php $this->Form->unlockField('ActionResult.share') ?>
                            <?php $this->Form->unlockField('socket_id') ?>
                        </div>
                    </div>
                </div>
                <div class="post-panel-footer none" id="CommonActionFooter">
                    <div class="font_12px" id="CommonActionFormFooter">
                        <a href="#" id="ActionUploadFileButton" class="link-red">
                            <button type="button" class="btn pull-left photo-up-btn"><i
                                    class="fa fa-camera post-camera-icon"></i>
                            </button>
                        </a>

                        <div class="row form-horizontal form-group post-share-range" id="CommonActionShare">
                            <?=
                            $this->Form->submit(__d('gl', "アクション登録"),
                                                ['class' => 'btn btn-primary pull-right post-submit-button', 'id' => 'CommonActionSubmit', 'disabled' => 'disabled']) ?>
                        </div>
                    </div>
                </div>
                <?= $this->Form->end() ?>
            <?php endif; ?>
        </div>

        <div class="tab-pane fade upload-file-drop-area" id="PostForm" data-preview-area-id="PostUploadFilePreview"
             data-submit-form-id="PostDisplayForm">
            <?=
            $this->Form->create('Post', [
                'url'           => ['controller' => 'posts', 'action' => 'add'],
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
                    'id'             => 'CommonPostBody',
                    'label'          => false,
                    'type'           => 'textarea',
                    'wrap'           => 'soft',
                    'rows'           => 1,
                    'required'       => true,
                    'placeholder'    => __d('gl', "何か投稿しよう"),
                    'class'          => 'form-control tiny-form-text-change blank-disable post-form feed-post-form box-align change-warning',
                    'target_show_id' => "PostFormFooter",
                    'target-id'      => "PostSubmit",
                    "required"       => false
                ])
                ?>
                <div class="row form-group m_0px none" id="PostFormImage">
                    <ul class="col input-images post-images">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                        <li id="WrapPhotoForm_Post_<?= $i ?>">
                            <?= $this->element('Feed/photo_upload',
                                               ['type' => 'post', 'index' => $i, 'submit_id' => 'PostSubmit']) ?>
                            </li><?php endfor ?>
                    </ul>
                    <span class="help-block" id="Post__Photo_ValidateMessage"></span>
                </div>
                <div id="PostUploadFilePreview" class="post-upload-file-preview"></div>
                <?php $this->Form->unlockField('file_id') ?>
            </div>
            <?php if (isset($this->request->params['circle_id'])) {
                $display = "block";
            }
            else {
                $display = "none";
            }
            ?>
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
                        'style' => "width: 100%;"]) ?>
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
                <?php $this->Form->unlockField('socket_id') ?>
            </div>
            <div class="post-panel-footer">
                <div class="font_12px none" id="PostFormFooter">
                    <a href="#" id="PostUploadFileButton" class="link-red">
                        <button type="button" class="btn pull-left photo-up-btn"><i
                                class="fa fa-camera post-camera-icon"></i>
                        </button>
                    </a>

                    <div class="row form-horizontal form-group post-share-range" id="PostShare">
                        <?=
                        $this->Form->submit(__d('gl', "投稿する"),
                                            ['class' => 'btn btn-primary pull-right post-submit-button', 'id' => 'PostSubmit', 'disabled' => 'disabled']) ?>
                    </div>
                </div>
            </div>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
<?= $this->element('file_upload_form') ?>
<!-- END app/View/Elements/Feed/common_form.ctp -->
