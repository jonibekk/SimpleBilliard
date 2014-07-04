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
<!-- Nav tabs -->
<ul class="nav nav-tabs" role="tablist">
    <li class="active"><a href="#PostForm" role="tab" data-toggle="tab"><?= __d('gl', "投稿") ?></a></li>
    <li><a href="#ActionForm" role="tab" data-toggle="tab"><?= __d('gl', "アクション") ?></a></li>
    <li><a href="#BadgeForm" role="tab" data-toggle="tab"><?= __d('gl', "バッジ") ?></a></li>
</ul>
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
            'id'            => 'UserAccountForm',
        ]); ?>
        <?=
        $this->Form->input('body', [
            'label' => false,
            'type'  => 'textarea',
        ])
        ?>
        <?= $this->Form->submit(__d('gl', "投稿する"), ['class' => 'btn btn-primary pull-right']) ?>
        <?= $this->Form->end() ?>
    </div>
    <div class="tab-pane fade" id="ActionForm">
    </div>
    <div class="tab-pane fade" id="BadgeForm">
    </div>
</div>