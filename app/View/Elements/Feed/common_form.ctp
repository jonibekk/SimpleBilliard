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
<div class="panel panel-default global-form">
    <div class="panel-heading">
        <!-- Nav tabs -->
        <ul class="nav nav-tabs" role="tablist">
            <li class="active"><a href="#PostForm" role="tab" data-toggle="tab"><?= __d('gl', "投稿") ?></a></li>
            <li><a href="#ActionForm" role="tab" data-toggle="tab"><?= __d('gl', "アクション") ?></a></li>
            <li><a href="#BadgeForm" role="tab" data-toggle="tab"><?= __d('gl', "バッジ") ?></a></li>
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
                    'class' => 'form-control',
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
                    'class'     => 'form-control tiny-form-text blank-disable',
                    'target_show_id'           => "PostFormFooter",
                    'target-id' => "PostSubmit",
                    "data-bv-notempty-message" => __d('validate', "何も入力されていません。"),
                ])
                ?>
                <div class="form-group">
                    <div class="fileinput_post_comment fileinput-new" data-provides="fileinput">
                        <div class="fileinput-preview thumbnail nailthumb-container" data-trigger="fileinput"
                             style="width: 50px; height: 50px;">
                        </div>
                        <div>
                        <span class="btn-file">
                            <?=
                            $this->Form->input('photo1',
                                               ['type'         => 'file',
                                                'label'        => false,
                                                'div'          => false,
                                                'css'          => false,
                                                'wrapInput'    => false,
                                                'errorMessage' => false,
                                                ''
                                               ]) ?>
                        </span>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="fileinput_post_comment fileinput-new" data-provides="fileinput">
                        <div class="fileinput-preview thumbnail nailthumb-container" data-trigger="fileinput"
                             style="width: 50px; height: 50px;">
                        </div>
                        <div>
                        <span class="btn-file">
                            <?=
                            $this->Form->input('photo2',
                                               ['type'         => 'file',
                                                'label'        => false,
                                                'div'          => false,
                                                'css'          => false,
                                                'wrapInput'    => false,
                                                'errorMessage' => false,
                                                ''
                                               ]) ?>
                        </span>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="fileinput_post_comment fileinput-new" data-provides="fileinput">
                        <div class="fileinput-preview thumbnail nailthumb-container" data-trigger="fileinput"
                             style="width: 50px; height: 50px;">
                        </div>
                        <div>
                        <span class="btn-file">
                            <?=
                            $this->Form->input('photo3',
                                               ['type'         => 'file',
                                                'label'        => false,
                                                'div'          => false,
                                                'css'          => false,
                                                'wrapInput'    => false,
                                                'errorMessage' => false,
                                                ''
                                               ]) ?>
                        </span>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="fileinput_post_comment fileinput-new" data-provides="fileinput">
                        <div class="fileinput-preview thumbnail nailthumb-container" data-trigger="fileinput"
                             style="width: 50px; height: 50px;">
                        </div>
                        <div>
                        <span class="btn-file">
                            <?=
                            $this->Form->input('photo4',
                                               ['type'         => 'file',
                                                'label'        => false,
                                                'div'          => false,
                                                'css'          => false,
                                                'wrapInput'    => false,
                                                'errorMessage' => false,
                                                ''
                                               ]) ?>
                        </span>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="fileinput_post_comment fileinput-new" data-provides="fileinput">
                        <div class="fileinput-preview thumbnail nailthumb-container" data-trigger="fileinput"
                             style="width: 50px; height: 50px;">
                        </div>
                        <div>
                        <span class="btn-file">
                            <?=
                            $this->Form->input('photo5',
                                               ['type'         => 'file',
                                                'label'        => false,
                                                'div'          => false,
                                                'css'          => false,
                                                'wrapInput'    => false,
                                                'errorMessage' => false,
                                                ''
                                               ]) ?>
                        </span>
                        </div>
                    </div>
                </div>
                <div class="" style="display: none" id="PostFormFooter">
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
