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
    <div class="panel-heading post-panel-heading">
        <!-- Nav tabs -->
        <ul class="gl-feed-switch clearfix" role="tab-list">
            <li class="switch-post active"><a href="#PostForm" role="tab" data-toggle="tab"
                                              class="switch-post-anchor"><i
                        class="fa fa-comment-o"></i><?= __d('gl', "投稿") ?></a><span class="switch-arrow"></span></li>
            <li class="switch-action"><a href="#ActionForm" role="tab" data-toggle="tab"
                                         class="switch-action-anchor develop--forbiddenLink-"><i
                        class="fa fa-star-o"></i><?= __d('gl', "アクション") ?></a><span class="switch-arrow"></span></li>
            <li class="switch-badge"><a href="#BadgeForm" role="tab" data-toggle="tab"
                                        class="switch-badge-anchor develop--forbiddenLink-"><i
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
            ]); ?>
            <div class="panel-body post-panel-body">
            <?=
                $this->Form->input('body', [
                    'label'                    => false,
                    'type'                     => 'textarea',
                    'rows'                     => 1,
                    'required'                 => true,
                    'placeholder'              => __d('gl', "・何か投稿しよう"),
                'class' => 'form-control click-show blank-disable post-form feed-post-form',
                'target_show_id'           => "PostFormFooter",
                    'target-id'                => "PostSubmit",
                    "data-bv-notempty-message" => __d('validate', "何も入力されていません。"),
                ])
                ?>
                <div class="row form-group gl-no-margin" id="PostFormImage" style="display: none">
                    <ul class="col gl-input-images">
                        <? for ($i = 1; $i <= 5; $i++): ?>
                            <li>
                            <?= $this->element('Feed/photo_upload',
                                               ['type' => 'post', 'index' => $i, 'submit_id' => 'PostSubmit']) ?>
                            </li><? endfor ?>
                    </ul>
                </div>
                <div class="font-size_12" style="display: none" id="PostFormFooter">
                    <span class="border-gray">
                        <a href="#" class="target-show-this-del border-none link-red" target-id="PostFormImage"><i
                                class="fa fa-picture-o link-red post-icon"></i>&nbsp;<?=
                            __d('gl',
                                "画像を追加する") ?>
                        </a>
                    </span>

                    <div class="row form-horizontal form-group post-share-range" id="PostShare">
                        <label class="col col-sm-2 control-label post-share-range-label border-gray">
                            <a href="#" id="ChangeShareSelect2" target-id="PostShareInputWrap"
                               class="border-none link-red font-weight_normal">
                                <i class="fa fa-plus link-red post-icon font-weight_normal"></i>&nbsp;<?= __d('gl', '共有範囲') ?>
                            </a>
                        </label>
                        <div class="col col-sm-10 post-share-range-list click-height-up blur-height-reset"
                             after-height="170px" id="PostShareInputWrap">
                            <?=
                            $this->Form->hidden('share',
                                                ['id' => 'select2PostCircleMember', 'value' => $current_circle ? "circle_" . $current_circle['Circle']['id'] : "public", 'style' => "width: 100%",]) ?>
                            <? $this->Form->unlockField('Post.share') ?>
                        </div>
                    </div>
                    <?=
                    $this->Form->submit(__d('gl', "投稿する"),
                                        ['class' => 'btn btn-primary pull-right post-share-range-buttom', 'id' => 'PostSubmit', 'disabled' => 'disabled']) ?>
                    <div class="clearfix"></div>
                </div>
            </div>
            <?= $this->Form->end() ?>
        </div>
        <div class="tab-pane fade" id="ActionForm">
            action
        </div>
        <div class="tab-pane fade" id="BadgeForm">
            badge
        </div>
    </div>
</div>
<!-- END app/View/Elements/Feed/common_form.ctp -->
