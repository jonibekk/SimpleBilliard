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
                'inputDefaults' => [
                    'div'       => 'form-group',
                    'label'     => false,
                    'wrapInput' => '',
                    'class'     => 'form-control'
                ],
                'class'         => '',
                'novalidate'    => true,
            ]); ?>
            <div class="panel-body">
                <?=
                $this->Form->input('body', [
                    'label'          => false,
                    'type'           => 'textarea',
                    'rows'           => 1,
                    'placeholder'    => __d('gl', "何か投稿しよう"),
                    'class'          => 'form-control tiny-form-text',
                    'target_show_id' => "PostFormFooter",
                ])
                ?>
            </div>
            <div class="panel-footer" style="display: none" id="PostFormFooter">
            <?= $this->Form->submit(__d('gl', "投稿する"), ['class' => 'btn btn-primary pull-right']) ?>
                <div class="clearfix"></div>
            </div>
            <?= $this->Form->end() ?>
        </div>
        <div class="tab-pane fade" id="ActionForm">
        </div>
        <div class="tab-pane fade" id="BadgeForm">
        </div>
    </div>

</div>
