<?php
/**
 * パスワードリセット画面
 * Created by PhpStorm.
 * User: daikihirakata
 * Date: 2014/06/01
 * Time: 0:19
 *
 * @var CodeCompletionView $this
 */
?>
<!-- START app/View/Users/password_reset.ctp -->
<div class="row">
    <div class="col-sm-8 col-sm-offset-2">
        <div class="panel panel-default">
            <div class="panel-heading"><?= __("パスワードの再設定") ?></div>
            <?=
            $this->Form->create('User', [
                'inputDefaults' => [
                    'div'       => 'form-group',
                    'label'     => [
                        'class' => 'col col-sm-3 control-label'
                    ],
                    'wrapInput' => 'col col-sm-6',
                    'class'     => 'form-control'
                ],
                'class'         => 'form-horizontal validate',
                'novalidate'    => true
            ]); ?>
            <div class="panel-body reset-password-panel-body">
                <?=
                $this->Form->input('password', [
                    'label'                    => __("パスワードを作成"),
                    'placeholder'              => __('Use at least 8 characters and use a mix of capital characters, small characters and numbers. Symbols are not allowed.'),
                    "data-bv-notempty-message" => __("入力必須項目です。"),
                    'type'                     => 'password',
                ]) ?>
                <?=
                $this->Form->input('password_confirm', [
                    'label'                    => __("Comfirm your password"),
                    "data-bv-notempty-message" => __("入力必須項目です。"),
                    'type'                     => 'password',
                ]) ?>
            </div>
            <div class="panel-footer">
                <div class="form-group">
                    <div class="col-sm-9 col-sm-offset-3">
                        <?= $this->Form->submit(__("パスワードを設定"), ['class' => 'btn btn-primary']) ?>
                    </div>
                </div>
            </div>
            <?= $this->Form->end(); ?>
        </div>
    </div>
</div>
<!-- END app/View/Users/password_reset.ctp -->
