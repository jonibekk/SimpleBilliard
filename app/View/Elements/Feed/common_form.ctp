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
    <div class="panel-heading">
        <!-- Nav tabs -->
        <ul class="nav nav-tabs" role="tablist">
            <li class="active"><a href="#PostForm" role="tab" data-toggle="tab"><?= __d('gl', "投稿") ?></a></li>
            <li><a href="#ActionForm" role="tab" data-toggle="tab" class="develop--forbiddenLink"><?=
                    __d('gl',
                        "アクション") ?></a>
            </li>
            <li><a href="#BadgeForm" role="tab" data-toggle="tab" class="develop--forbiddenLink"><?=
                    __d('gl',
                        "バッジ") ?></a>
            </li>
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
                'type'          => 'file',
                'novalidate'    => true,
            ]); ?>
            <div class="panel-body">
                <?=
                $this->Form->input('body', [
                    'label'                    => false,
                    'type'                     => 'textarea',
                    'rows'                     => 1,
                    'required'                 => true,
                    'placeholder'              => __d('gl', "何か投稿しよう"),
                    'class'                    => 'form-control tiny-form-text blank-disable',
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
                <div class="" style="display: none" id="PostFormFooter">
                    <a href="#" class="target-show-this-del" target-id="PostFormImage"><i class="fa fa-file-o"></i>&nbsp;<?=
                        __d('gl',
                            "画像を追加する") ?>
                    </a>

                    <div class="row form-horizontal form-group" id="PostShare">
                        <label class="col col-sm-2 control-label"><?= __d('gl', '共有範囲') ?></label>

                        <div class="col col-sm-7">
                            <?=
                            $this->Form->hidden('share',
                                                ['id' => 'select2PostCircleMember', 'value' => $current_circle ? "circle_" . $current_circle['Circle']['id'] : "public", 'style' => "width: 100%",]) ?>
                            <? $this->Form->unlockField('Post.share') ?>
                        </div>
                    </div>
                    <?=
                    $this->Form->submit(__d('gl', "投稿する"),
                                        ['class' => 'btn btn-primary pull-right', 'id' => 'PostSubmit', 'disabled' => 'disabled']) ?>
                    <div class="clearfix"></div>
                </div>
            </div>
            <?= $this->Form->end() ?>
        </div>
        <div class="tab-pane fade" id="ActionForm">
        </div>
        <div class="tab-pane fade" id="BadgeForm">
        </div>
    </div>
</div>
<!-- END app/View/Elements/Feed/common_form.ctp -->
