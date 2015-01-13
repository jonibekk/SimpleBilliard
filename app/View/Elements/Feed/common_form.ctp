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
                                         class="switch-action-anchor develop--forbiddenLink"><i
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
            ]); ?>
            <div class="post-panel-body plr_11px ptb_7px">
                <?=
                $this->Form->input('body', [
                    'label'                    => false,
                    'type'                     => 'textarea',
                    'wrap'                     => 'soft',
                    'rows'                     => 1,
                    'required'                 => true,
                    'placeholder'              => __d('gl', "何か投稿しよう"),
                    'class'                    => 'form-control tiny-form-text blank-disable post-form feed-post-form box-align',
                    'target_show_id'           => "PostFormFooter",
                    'target-id'                => "PostSubmit",
                    "data-bv-notempty-message" => __d('validate', "何も入力されていません。"),
                ])
                ?>
                <div class="row form-group m_0px none" id="PostFormImage">
                    <ul class="col input-images post-images">
                        <? for ($i = 1; $i <= 5; $i++): ?>
                            <li>
                            <?= $this->element('Feed/photo_upload',
                                               ['type' => 'post', 'index' => $i, 'submit_id' => 'PostSubmit']) ?>
                            </li><? endfor ?>
                    </ul>
                </div>
            </div>
            <?
            if (isset($this->request->params['circle_id'])) {
                $display = "block";
            }
            else {
                $display = "none";
            }
            ?>
            <div class="panel-body post-share-range-panel-body" id="PostFormShare" style="display: <?= $display ?>">
                <div class="col col-xxs-12 col-xs-12 post-share-range-list" id="PostShareInputWrap">
                    <?=
                    $this->Form->hidden('share',
                                        ['id' => 'select2PostCircleMember', 'value' => $current_circle ? "circle_" . $current_circle['Circle']['id'] : "public", 'style' => "width: 100%",]) ?>
                    <? $this->Form->unlockField('Post.share') ?>
                </div>
            </div>
            <div class="post-panel-footer">
                <div class="font_12px none" id="PostFormFooter">
                    <a href="#" class="target-show-target-click link-red" target-id="PostFormImage"
                       click-target-id="Post__Photo_1">
                        <button type="button" class="btn pull-left photo-up-btn" data-toggle="tooltip"
                                data-placement="bottom"
                                title="画像を追加する"><i class="fa fa-camera post-camera-icon"></i>
                        </button>
                    </a>

                    <div class="row form-horizontal form-group post-share-range" id="PostShare">
                        <?=
                        $this->Form->submit(__d('gl', "投稿する"),
                                            ['class' => 'btn btn-primary pull-right post-submit-button', 'id' => 'PostSubmit', 'disabled' => 'disabled']) ?>

                        <button type="button" class="btn btn-link post-share-range-button pull-right bd-radius_4px"
                                id="ChangeShareSelect2" target-id="PostShareInputWrap" show-target-id="PostFormShare">
                            <i class="fa fa-plus link-red post-icon font_normal"></i>&nbsp;<?=
                            __d('gl',
                                "共有範囲") ?>
                        </button>
                        <div class="clearfix"></div>
                    </div>
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
