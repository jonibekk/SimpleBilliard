<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 7/4/14
 * Time: 10:45 AM
 *
 * @var CodeCompletionView $this
 * @var                    $current_circle
 */
?>
<!-- START app/View/Elements/Feed/common_form.ctp -->
<div class="panel panel-default global-form">
    <div class="post-panel-heading ptb_7px plr_11px">
        <!-- Nav tabs -->
        <ul class="feed-switch clearfix plr_0px" role="tablist">
            <li class="switch-post active"><a href="#PostForm" role="tab" data-toggle="tab"
                                              class="switch-post-anchor"><i
                        class="fa fa-comment-o"></i><?= __d('gl', "投稿") ?></a><span class="switch-arrow"></span></li>
            <li class="switch-action"><a href="#ActionForm" role="tab" data-toggle="tab"
                                         class="switch-action-anchor"><i
                        class="fa fa-star-o"></i><?= __d('gl', "アクション") ?></a><span class="switch-arrow"></span></li>
            <li class="switch-badge"><a href="#BadgeForm" role="tab" data-toggle="tab"
                                        class="switch-badge-anchor develop--forbiddenLink"><i
                        class="fa fa-heart-o"></i><?= __d('gl', "バッジ") ?></a><span class="switch-arrow"></span></li>
        </ul>
    </div>
    <!-- Tab panes -->
    <div class="tab-content">
        <div class="tab-pane fade in active" id="PostForm">
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
                            <li>
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
                                        ['id' => 'select2PostCircleMember', 'value' => $current_circle ? "circle_" . $current_circle['Circle']['id'] : "public", 'style' => "width: 100%",]) ?>
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
        <div class="tab-pane fade" id="ActionForm">
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
                    'label'          => false,
                    'type'           => 'textarea',
                    'wrap'           => 'soft',
                    'rows'           => 1,
                    'required'       => true,
                    'placeholder'    => __d('gl', "今日やったアクションを共有しよう！"),
                    'class'          => 'form-control tiny-form-text blank-disable post-form feed-post-form box-align change-warning',
                    'target_show_id' => "CommonActionFormFooter",
                    'target-id'      => "CommonActionSubmit",
                    "required"       => false
                ])
                ?>
                <div class="row form-group m_0px" id="CommonActionFormImage">
                    <ul class="col input-images post-images">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <li>
                            <?= $this->element('Feed/photo_upload',
                                               ['type' => 'action_result', 'index' => $i, 'submit_id' => 'CommonActionSubmit']) ?>
                            </li><?php endfor ?>
                    </ul>
                    <span class="help-block" id="CommonAction__Photo_ValidateMessage"></span>
                </div>
            </div>
            <div class="panel-body post-share-range-panel-body" id="">
                <?=
                $this->Form->input('goal_id', [
                    'label'    => __d('gl', "ゴール"),
                    'required' => true,
                    'options'  => $goal_list_for_action_option,
                ])
                ?>
            </div>
            <div class="panel-body post-share-range-panel-body" id="CommonActionFormShare">
                <div class="col col-xxs-12 col-xs-12 post-share-range-list" id="CommonActionShareInputWrap">
                    <?=
                    $this->Form->hidden('share',
                                        ['id' => 'select2ActionCircleMember', 'value' => $current_circle ? "circle_" . $current_circle['Circle']['id'] : "public", 'style' => "width: 100%",]) ?>
                    <?php $this->Form->unlockField('ActionResult.share') ?>
                    <?php $this->Form->unlockField('socket_id') ?>
                </div>
            </div>
            <div class="post-panel-footer">
                <div class="font_12px none" id="CommonActionFormFooter">
                    <div class="row form-horizontal form-group post-share-range" id="CommonActionShare">
                        <?=
                        $this->Form->submit(__d('gl', "アクション登録"),
                                            ['class' => 'btn btn-primary pull-right post-submit-button', 'id' => 'CommonActionSubmit', 'disabled' => 'disabled']) ?>
                    </div>
                </div>
            </div>
            <?= $this->Form->end() ?>
        </div>
        <div class="tab-pane fade" id="BadgeForm">
            badge
        </div>
    </div>
</div>
<!-- END app/View/Elements/Feed/common_form.ctp -->
