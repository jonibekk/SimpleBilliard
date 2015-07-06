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
                        class="fa fa-star-o"></i><?= __d('gl', "アクション") ?></a><span class="switch-arrow"></span></li>
            <li class="switch-post"><a href="#PostForm" role="tab" data-toggle="tab"
                                       class="switch-post-anchor click-target-focus"
                                       target-id="CommonPostBody"><i
                        class="fa fa-comment-o"></i><?= __d('gl', "投稿") ?></a><span class="switch-arrow"></span></li>
        </ul>
    </div>
    <!-- Tab panes -->
    <div class="tab-content">
        <div class="tab-pane fade" id="ActionForm">
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
                    <?=
                    $this->Form->input('name', [
                        'id'                       => 'CommonActionName',
                        'label'                    => false,
                        'type'                     => 'textarea',
                        'wrap'                     => 'soft',
                        'rows'                     => 1,
                        'required'                 => true,
                        'placeholder'              => __d('gl', "アクションの説明を書く"),
                        'class'                    => 'form-control blank-disable post-form feed-post-form box-align change-warning',
                        'target-id'                => "CommonActionSubmit",
                        "required"                 => true,
                        'data-bv-notempty-message' => __d('validate', "入力必須項目です。"),
                    ])
                    ?>
                    <div class="row form-group m_0px" id="CommonActionFormImage">
                        <ul class="col input-images post-images">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                            <li id="WrapPhotoForm_Action_<?= $i ?>">
                                <?= $this->element('Feed/photo_upload',
                                                   ['type' => 'action_result', 'index' => $i, 'submit_id' => 'CommonActionSubmit']) ?>
                                </li><?php endfor ?>
                        </ul>
                        <span class="help-block" id="ActionResult__Photo_ValidateMessage"></span>
                    </div>
                </div>
                <div class="panel-body post-share-range-panel-body" id="">
                    <?=
                    $this->Form->input('goal_id', [
                        'label'                    => __d('gl', "ゴール"),
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
                <div class="panel-body post-share-range-panel-body none" id="WrapKrSelectOnActionForm">
                    <?=
                    $this->Form->input('key_result_id', [
                        'label'    => __d('gl', "出したい成果(オプション)"),
                        'required' => false,
                        'id'       => 'KrSelectOnActionForm',
                        'options'  => [null => __d('gl', '出したい成果を選択する')],
                    ])
                    ?>
                </div>
                <div class="panel-body post-share-range-panel-body" id="CommonActionFormShare">
                    <label for="KrSelectOnActionForm"><?= __d('gl', "通知先を追加") ?></label>

                    <div class="col col-xxs-12 col-xs-12 post-share-range-list" id="CommonActionShareInputWrap">
                        <?=
                        $this->Form->hidden('share',
                                            ['id' => 'select2ActionCircleMember', 'value' => "coach,followers,collaborators", 'style' => "width: 100%",]) ?>
                        <?php $this->Form->unlockField('ActionResult.share') ?>
                        <?php $this->Form->unlockField('socket_id') ?>
                    </div>
                </div>
                <div class="post-panel-footer">
                    <div class="font_12px" id="CommonActionFormFooter">
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

        <div class="tab-pane fade" id="PostForm">
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
                    'class'          => 'form-control tiny-form-text blank-disable post-form feed-post-form box-align change-warning',
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
            </div>
            <?php if (isset($this->request->params['circle_id'])) {
                $display = "block";
            }
            else {
                $display = "none";
            }
            ?>
            <div class="panel-body post-share-range-panel-body" id="PostFormShare">
                <div class="col col-xxs-12 col-xs-12 post-share-range-list" id="PostShareInputWrap">
                    <?=
                    $this->Form->hidden('share',
                                        ['id' => 'select2PostCircleMember', 'value' => $current_circle && !$current_circle['Circle']['team_all_flg'] ? "circle_" . $current_circle['Circle']['id'] : "public", 'style' => "width: 100%",]) ?>
                    <?php $this->Form->unlockField('Post.share') ?>
                    <?php $this->Form->unlockField('socket_id') ?>
                </div>
            </div>
            <div class="post-panel-footer">
                <div class="font_12px none" id="PostFormFooter">
                    <a href="#" class="target-show-target-click link-red" target-id="PostFormImage"
                       click-target-id="Post__Photo_1">
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
<!-- END app/View/Elements/Feed/common_form.ctp -->
