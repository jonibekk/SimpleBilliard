<?php /**
 * パスワード登録画面
 *
 * @var CodeCompletionView $this
 * @var                    $lastFirst
 * @var                    $email
 */
?>
<?= $this->App->viewStartComment()?>
<div class="row">
    <div class="panel panel-default panel-signup">
        <div class="panel-heading signup-title"><?= __("Set your password") ?></div>
        <img src="/img/signup/password.png"  class="signup-header-image" />
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
            'placeholder'              => __('********'),
            "data-bv-notempty-message" => __("Input is required."),
            "data-bv-notempty"         => "true",
            'required'                 => false,
            'type'                     => 'password',
            'maxlength'                => 50,
        ]) ?>
        <div class="signup-description mod-small"><?= __("Use 8 or more characters including at least one number.") ?></div>
        <div class="submit signup-btn">
            <?= $this->Form->button(__('Join Team') . ' <i class="fa fa-angle-right"></i>',
                [
                    'type'     => 'submit',
                    'class'    => 'btn btn-primary signup-invite-submit-button',
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
                            regexp: /^(?=.*[0-9])(?=.*[a-zA-Z])[0-9a-zA-Z\!\@\#\$\%\^\&\*\(\)\_\-\+\=\{\}\[\]\|\:\;\<\>\,\.\?\/]{0,}$/,
                            message: cake.message.validate.e
                        }
                    }
                }
            }
        });
    });
</script>
<?php $this->end(); ?>

<?= $this->App->viewEndComment()?>
