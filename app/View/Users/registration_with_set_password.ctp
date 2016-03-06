<?php
/**
 * User Registration by batch set up.
 * Created by PhpStorm.
 * User: daikihirakata
 * Date: 2014/06/01
 * Time: 0:19
 *
 * @var CodeCompletionView $this
 */
?>
<!-- START app/View/Users/registration_with_set_password.ctp -->
<div class="row">
    <div class="col-sm-8 col-sm-offset-2">
        <div class="panel panel-default">
            <div class="panel-heading"><?= __("User Authentication") ?></div>
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
                <p><?= __("Welcome to Goalous!") ?></p>

                <p><?= __("Let's start to sign up Goalous.") ?></p>

                <p><?= __("Enter your email address and create your password.") ?></p>

                <?=
                $this->Form->input('Email.email', [
                    'label'                        => __("Email Address"),
                    'data-bv-emailaddress-message' => __("メールアドレスが正しくありません。"),
                    "data-bv-notempty-message"     => __("入力必須項目です。"),
                    'required'                     => true
                ]) ?>

                <?=
                $this->Form->input('password', [
                    'label'                    => __("Create a password_hash"),
                    'placeholder'              => __('Use at least 8 characters and use a mix of capital characters, small characters and numbers. Symbols are not allowed.'),
                    "data-bv-notempty-message" => __("入力必須項目です。"),
                    'type'                     => 'password',
                ]) ?>
                <?=
                $this->Form->input('password_confirm', [
                    'label'                    => __("Confirm your password"),
                    "data-bv-notempty-message" => __("入力必須項目です。"),
                    'type'                     => 'password',
                ]) ?>
                <?=
                //タイムゾーン設定の為のローカル時刻をセット
                $this->Form->input('local_date', [
                    'label' => false,
                    'div'   => false,
                    'style' => 'display:none;',
                    'id'    => 'InitLocalDate',
                ]);

                ?>
            </div>
            <div class="panel-footer">
                <div class="form-group">
                    <div class="col-sm-9 col-sm-offset-3">
                        <?= $this->Form->submit(__("Registration"), ['class' => 'btn btn-primary']) ?>
                    </div>
                </div>
            </div>
            <?= $this->Form->end(); ?>
        </div>
    </div>
</div>
<?php $this->append('script'); ?>
<script type="text/javascript">
    $(document).ready(function () {
        //ユーザ登録時にローカル時間をセットする
        $('input#InitLocalDate').val(getLocalDate());
    });
</script>
<?php $this->end(); ?>
<!-- END app/View/Users/registration_with_set_password.ctp -->
