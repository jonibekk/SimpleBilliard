<?php /**
 * パスワード登録画面
 *
 * @var CodeCompletionView $this
 * @var                    $last_first
 * @var                    $email
 */
?>
<!-- START app/View/Users/register_password_with_invite.ctp -->
<div class="row">
    <div class="panel panel-default panel-signup">
        <div class="panel-heading signup-title"><?= __("Set your password") ?></div>
        <div class="signup-description"><?= __('Choose a password for login to Goalous') ?></div>
        <?=
        $this->Form->create('User', [
            'inputDefaults' => [
                'div'       => 'form-group signup_input_error',
                'label'     => false,
                'wrapInput' => false,
                'class'     => 'form-control signup_input-design'
            ],
            'class'         => 'form-horizontal',
            'novalidate'    => true,
            'id'            => 'UserPassword',
        ]); ?>
        <?=
        $this->Form->input('password', [
            'placeholder'              => __('Use at least 8 characters and use a mix of capital characters, small characters and numbers. Symbols are not allowed.'),
            "data-bv-notempty-message" => __("Input is required."),
            "data-bv-notempty"         => "true",
            'required'                 => false,
            'type'                     => 'password',
            'maxlength'                => 50,
        ]) ?>

        <div class="signup-description mod-small">8文字以上。 アルファベット大文字、小文字、数字が混在している必要があります。記号は使えません。</div>
        <div class="submit signup-btn">
            <?= $this->Form->button(__('Join Team') . ' <i class="fa fa-angle-right"></i>',
                [
                    'type'     => 'submit',
                    'class'    => 'btn btn-primary signup-btn-submit',
                    'disabled' => 'disabled'
                ]) ?>
        </div>
        <?= $this->Form->end(); ?>
    </div>
</div>
<?php $this->append('script'); ?>
<script type="text/javascript">
    $(document).ready(function () {
        $('#UserPassword').bootstrapValidator({
            fields: {
                "data[User][password]": {
                    validators: {
                        stringLength: {
                            min: 8,
                            message: cake.message.validate.a
                        },
                        regexp: {
                            regexp: /^(?=.*[0-9])(?=.*[a-zA-Z])(?=.*[\!\@\#\$\%\^\&\*\(\)\_\-\+\=\{\}\[\]\|\:\;\<\>\,\.\?\/])[0-9a-zA-Z\!\@\#\$\%\^\&\*\(\)\_\-\+\=\{\}\[\]\|\:\;\<\>\,\.\?\/]{0,}$/,
                            message: cake.message.validate.e
                        }
                    }
                }
            }
        });
    });
</script>
<?php $this->end(); ?>

<!-- END app/View/Users/register_password_with_invite.ctp -->
