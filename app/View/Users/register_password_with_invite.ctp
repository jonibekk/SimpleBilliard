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
    <div class="col-sm-8 col-sm-offset-2">
        <div class="panel panel-default">
            <div class="panel-heading"><?= __("Set your password") ?></div>
            <?=
            $this->Form->create('User', [
                'inputDefaults' => [
                    'div'       => 'form-group',
                    'label'     => [
                        'class' => 'col col-sm-3 control-label form-label'
                    ],
                    'wrapInput' => 'col col-sm-6',
                    'class'     => 'form-control register_input-design'
                ],
                'class'         => 'form-horizontal validate',
                'novalidate'    => true
            ]); ?>
            <div class="panel-body register-panel-body">
                <div class="form-group">
                    <?= __('Choose a password for login to Goalous') ?>
                </div>
                <?=
                $this->Form->input('password', [
                    'label'                    => __("Password"),
                    'placeholder'              => __('Use at least 8 characters and use a mix of capital characters, small characters and numbers. Symbols are not allowed.'),
                    "data-bv-notempty-message" => __("Input is required."),
                    'type'                     => 'password',
                    'maxlength'                => 50,
                ]) ?>
            </div>
            <div class="panel-footer">
                <div class="row">
                    <div class="col-sm-9 col-sm-offset-3">
                        <?= $this->Form->button(__('Join Team') . ' <i class="fa fa-angle-right"></i>',
                            ['type' => 'submit', 'class' => 'btn btn-primary', 'disabled' => 'disabled']) ?>
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

        // 登録可能な email の validate
        require(['validate'], function (validate) {
            window.bvCallbackAvailableEmail = validate.bvCallbackAvailableEmail;
        });
    });


</script>
<?php $this->end(); ?>
<!-- END app/View/Users/register_password_with_invite.ctp -->
