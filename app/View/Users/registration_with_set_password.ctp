<?php
/**
 * User Registration by batch set up.
 * Created by PhpStorm.
 * User: daikihirakata
 * Date: 2014/06/01
 * Time: 0:19
 *
 * @var $this View
 */
?>
<!-- START app/View/Users/registration_with_set_password.ctp -->
<div class="row">
    <div class="col-sm-8 col-sm-offset-2">
        <div class="panel panel-default">
            <div class="panel-heading"><?= __d('gl', "ユーザ認証") ?></div>
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
                <p><?= __d('gl', "Goalousへようこそ！") ?></p>

                <p><?= __d('gl', "Goalousへのユーザ登録を行います。") ?></p>

                <p><?= __d('gl', "招待を受けたメールアドレスの入力とパスワードの登録をお願いします。") ?></p>

                <?=
                $this->Form->input('Email.email', [
                    'label'                        => __d('gl', "メールアドレス"),
                    'data-bv-emailaddress-message' => __d('validate', "メールアドレスが正しくありません。"),
                    "data-bv-notempty-message"     => __d('validate', "入力必須項目です。"),
                    'required'                     => true
                ]) ?>

                <?=
                $this->Form->input('password', [
                    'label'                    => __d('gl', "パスワードを作成"),
                    'placeholder'              => __d('gl', '8文字以上'),
                    "data-bv-notempty-message" => __d('validate', "入力必須項目です。"),
                    'type'                     => 'password',
                ]) ?>
                <?=
                $this->Form->input('password_confirm', [
                    'label'                    => __d('gl', "パスワードを再入力"),
                    "data-bv-notempty-message" => __d('validate', "入力必須項目です。"),
                    'type'                     => 'password',
                ]) ?>
            </div>
            <div class="panel-footer">
                <div class="form-group">
                    <div class="col-sm-9 col-sm-offset-3">
                        <?= $this->Form->submit(__d('gl', "登録"), ['class' => 'btn btn-primary']) ?>
                    </div>
                </div>
            </div>
            <?= $this->Form->end(); ?>
        </div>
    </div>
</div>
<!-- END app/View/Users/registration_with_set_password.ctp -->
