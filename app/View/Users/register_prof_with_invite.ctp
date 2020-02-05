<?php /**
 * ユーザ登録画面
 *
 * @var CodeCompletionView $this
 * @var                    $email
 * @var                    $team_name
 */
?>
<?= $this->App->viewStartComment() ?>
<div class="row">
    <div class="panel panel-default panel-signup">
        <div class="panel-heading signup-title"><?= __('Join the Goalous team for "%s"?', $team_name) ?></div>
        <img src="/img/signup/user.png" className="signup-header-image"/>
        <div class="signup-description">
            <?= __('Your name will displayed along with your goals and posts in Goalous') ?>
        </div>
        <?=
        $this->Form->create('User', [
            'inputDefaults' => [
                'div'       => 'form-group signup_input_error',
                'label'     => false,
                'wrapInput' => false,
                'class'     => 'form-control signup_input-design'
            ],
            'class'         => 'form-horizontal',
            // 'novalidate'    => true,
            'id'            => 'UserProf',
        ]); ?>
        <div class="panel-heading signup-itemtitle"><?= __('Your name') ?></div>
        <?php
        $last_name = $this->Form->input('last_name', [
            'placeholder'                  => __("last name (eg. Smith)"),
            "pattern"                      => User::USER_NAME_REGEX_JAVASCRIPT,
            "data-bv-regexp-message"       => __("It includes restricted strings. Allowed characters are only alphanumeric, space and apostrophe."),
            "data-bv-notempty"             => "true",
            "data-bv-notempty-message"     => __("Input is required."),
            'data-bv-stringlength'         => 'true',
            'data-bv-stringlength-max'     => 128,
            'data-bv-stringlength-message' => __("It's over limit characters (%s).", 128),
            'required'                     => true
        ]);
        $first_name = $this->Form->input('first_name', [
            'placeholder'                  => __("first name (eg. John)"),
            "pattern"                      => User::USER_NAME_REGEX_JAVASCRIPT,
            "data-bv-regexp-message"       => __("It includes restricted strings. Allowed characters are only alphanumeric, space and apostrophe."),
            "data-bv-notempty"             => "true",
            "data-bv-notempty-message"     => __("Input is required."),
            'data-bv-stringlength'         => 'true',
            'data-bv-stringlength-max'     => 128,
            'data-bv-stringlength-message' => __("It's over limit characters (%s).", 128),
            'required'                     => true
        ]);

        // This column is required by alphabet.
        // So, even in japanese, fixed this order.
        echo $first_name;
        echo $last_name;
        ?>

        <?= $this->Form->input('update_email_flg', [
            'wrapInput' => 'signup-invitation-checkbox-email-flg',
            'type'      => 'checkbox',
            'label'     => [
                'class' => null,
                'text'  => __("I want to receive news and updates by email from Goalous.")
            ],
            'class'     => '',
            'checked'   => 'checked'
        ]);
        ?>

        <div class="panel-heading signup-itemtitle"><?= __('Your date of birth') ?></div>
        <?=
        $this->Form
            ->input('birth_day',
                [
                    'monthNames'               => [
                        '01' => __('Jan'),
                        '02' => __('Feb'),
                        '03' => __('Mar'),
                        '04' => __('Apr'),
                        '05' => __('May'),
                        '06' => __('Jun'),
                        '07' => __('Jul'),
                        '08' => __('Aug'),
                        '09' => __('Sep'),
                        '10' => __('Oct'),
                        '11' => __('Nov'),
                        '12' => __('Dec'),
                    ],
                    'class'                    => 'form-control inline-fix signup_input-design',
                    'label'                    => false,
                    'dateFormat'               => 'YMD',
                    'empty'                    => true,
                    'separator'                => ' / ',
                    'maxYear'                  => date('Y'),
                    'minYear'                  => '1910',
                    'wrapInput'                => 'form-inline signup_inputs-inline',
                    "data-bv-notempty"         => "true",
                    "data-bv-notempty-message" => __("Input is required."),

                ]);
        ?>
        <div class="<?= $birthday_class?> signup-invalid-message-box"><small class="help-block"><?= __('You must be at least 16 years old to register Goalous.')?></small></div>

        <?php $tosLink = $this->Html->link(__('Terms of Use'),
            '/terms',
            [
                'target'  => "_blank",
                'onclick' => "window.open(this.href,'_system');return false;",
                'class'   => 'signup-privacy-policy-link',
            ]
        );

        $ppLink = $this->Html->link(__('Privacy Policy'),
            '/privacy_policy',
            [
                'target'  => "_blank",
                'onclick' => "window.open(this.href,'_system');return false;",
                'class'   => 'signup-privacy-policy-link',
            ]
        );
        echo $this->Form->input('agree_tos', [
            'wrapInput' => 'signup-invitation-agree-tos',
            'type'      => 'checkbox',
            'label'     => [
                'class' => 'invitation-signup-privacy-policy-label',
                'text'  => __("I agree to %s and %s of Goalous.", $tosLink, $ppLink)
            ],
            'class'     => 'validate-checkbox',
            'required'  => true,
            'checked'   => true
        ]);
        //タイムゾーン設定の為のローカル時刻をセット
        echo $this->Form->input('local_date', [
            'label' => false,
            'div'   => false,
            'style' => 'display:none;',
            'id'    => 'InitLocalDate',
        ]);
        ?>
        <div class="submit signup-btn">
            <?= $this->Form->button(__('Next') . ' <i class="fa fa-angle-right"></i>',
                [
                    'type'     => 'submit',
                    'class'    => 'btn btn-primary signup-invite-submit-button',
                    // 'disabled' => 'disabled'
                ]) ?>
        </div>

        <?= $this->Form->end(); ?>
    </div>
</div>
<?php $this->append('script'); ?>
<script type="text/javascript">
    $(document).ready(function () {
        //ユーザ登録時にローカル時間をセットする
        $('input#InitLocalDate').val(getLocalDate());
    //
    //     $('#UserProf').bootstrapValidator({
    //         fields: {
    //             "validate-checkbox": {
    //                 selector: '.validate-checkbox',
    //                 validators: {
    //                     choice: {
    //                         min: 1,
    //                         max: 1,
    //                         message: cake.message.validate.d
    //                     }
    //                 }
    //             }
    //         }
    //     });
    //
    //     // 登録可能な email の validate
    //     require(['validate'], function (validate) {
    //         window.bvCallbackAvailableEmail = validate.bvCallbackAvailableEmail;
    //     });
    });
</script>
<?php $this->end(); ?>
<?= $this->App->viewEndComment() ?>
