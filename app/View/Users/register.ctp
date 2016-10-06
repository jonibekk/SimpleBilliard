<?php /**
 * ユーザ登録画面
 *
 * @var CodeCompletionView $this
 * @var                    $last_first
 * @var                    $email
 */
?>
<?= $this->App->viewStartComment() ?>
<div class="row">
    <div class="col-sm-8 col-sm-offset-2">
        <div class="panel panel-default">
            <div class="panel-heading"><?= __("Create a new account") ?></div>
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
                <?php //姓と名は言語によって表示順を変える
                $last_name = $this->Form->input('last_name', [
                    'label'                        => __("Last Name"),
                    'placeholder'                  => __("eg. Armstrong"),
                    "pattern"                      => User::USER_NAME_REGEX,
                    "data-bv-regexp-message"       => __("It includes restricted strings. Allowed characters are only alphanumeric, space and apostrophe."),
                    "data-bv-notempty-message"     => __("Input is required."),
                    'data-bv-stringlength'         => 'true',
                    'data-bv-stringlength-max'     => 128,
                    'data-bv-stringlength-message' => __("It's over limit characters (%s).", 128),
                ]);
                $first_name = $this->Form->input('first_name', [
                    'label'                        => __("First Name"),
                    'placeholder'                  => __("eg. Harry"),
                    "pattern"                      => User::USER_NAME_REGEX,
                    "data-bv-regexp-message"       => __("It includes restricted strings. Allowed characters are only alphanumeric, space and apostrophe."),
                    "data-bv-notempty-message"     => __("Input is required."),
                    'data-bv-stringlength'         => 'true',
                    'data-bv-stringlength-max'     => 128,
                    'data-bv-stringlength-message' => __("It's over limit characters (%s).", 128),
                ]);
                if ($last_first) {
                    echo $last_name;
                    echo $first_name;
                } else {
                    echo $first_name;
                    echo $last_name;
                }
                ?>
                <hr>
                <?php if (isset($email)): ?>
                    <div class="form-group">
                        <label for="" class="col col-sm-3 control-label form-label"><?= __("Email") ?></label>

                        <div class="col col-sm-6">
                            <p class="form-control-static"><?= h($email) ?></p>
                        </div>
                    </div>
                    <?=
                    $this->Form->hidden('Email.0.email', ['value' => $email]) ?>
                <?php else: ?>
                    <?=
                    $this->Form->input('Email.0.email', [
                        'label'                        => __("Email"),
                        'placeholder'                  => __("tom@example.com"),
                        "data-bv-notempty"             => "true",
                        'data-bv-emailaddress'         => "false",
                        "data-bv-callback"             => "true",
                        "data-bv-callback-message"     => " ",
                        "data-bv-callback-callback"    => "bvCallbackAvailableEmail",
                        'data-bv-stringlength'         => 'true',
                        'data-bv-stringlength-max'     => 200,
                        'data-bv-stringlength-message' => __("It's over limit characters (%s).", 200),
                    ]) ?>
                <?php endif; ?>

                <?=
                $this->Form->input('update_email_flg', [
                    'wrapInput' => 'col col-sm-9 col-sm-offset-3',
                    'label'     => [
                        'class' => null,
                        'text'  => __("I receive the news and updates by email from Goalous.")
                    ],
                    'class'     => false,
                    'default'   => true,
                ]) ?>
                <hr>
                <?=
                $this->Form->input('password', [
                    'label'                    => __("Create a password"),
                    'placeholder'              => __('Use at least 8 characters and use a mix of capital characters, small characters and numbers. Symbols are not allowed.'),
                    "data-bv-notempty-message" => __("Input is required."),
                    'type'                     => 'password',
                    'maxlength'                => 50,
                ]) ?>
                <?=
                $this->Form->input('password_confirm', [
                    'label'                    => __("Confirm your password"),
                    "data-bv-notempty-message" => __("Input is required."),
                    'type'                     => 'password',
                    'maxlength'                => 50,
                ]) ?>
                <hr>
                <?php $tosLink = $this->Html->link(__('Terms of Use'),
                    [
                        'controller' => 'pages',
                        'action'     => 'display',
                        'pagename'   => 'terms',
                    ],
                    [
                        'target'  => "_blank",
                        'onclick' => "window.open(this.href,'_system');return false;",
                        'class'   => 'link',
                    ]
                );

                $ppLink = $this->Html->link(__('Privacy Policy'),
                    [
                        'controller' => 'pages',
                        'action'     => 'display',
                        'pagename'   => 'privacy_policy',
                    ],
                    [
                        'target'  => "_blank",
                        'onclick' => "window.open(this.href,'_system');return false;",
                        'class'   => 'link',
                    ]
                );
                echo $this->Form->input('agree_tos', [
                    'wrapInput' => 'col col-sm-9 col-sm-offset-3',
                    'type'      => 'checkbox',
                    'label'     => [
                        'class' => null,
                        'text'  => __("I agree to %s and %s of Goalous.", $tosLink, $ppLink)
                    ],
                    'class'     => 'validate-checkbox'
                ]);
                //タイムゾーン設定の為のローカル時刻をセット
                echo $this->Form->input('local_date', [
                    'label' => false,
                    'div'   => false,
                    'style' => 'display:none;',
                    'id'    => 'InitLocalDate',
                ]);
                ?>
            </div>
            <div class="panel-footer">
                <div class="row">
                    <div class="col-sm-9 col-sm-offset-3">
                        <?= $this->Form->submit(__("New registration"),
                            ['class' => 'btn btn-primary', 'disabled' => 'disabled']) ?>
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
<?= $this->App->viewEndComment() ?>
