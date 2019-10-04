<?php
/**
 * emailアドレス入力画面
 *
 * @var CodeCompletionView $this
 */
?>
<?= $this->App->viewStartComment()?>
<div class="row">
    <div class="panel panel-default panel-signup">
        <div class="panel-heading signup-title"><?= __('Create a new team') ?></div>

        <?=
        $this->Form->create('Email', [
            'inputDefaults' => [
                'label'     => [
                    'class' => 'panel-heading signup-itemtitle item−email'
                ],
                'wrapInput' => 'col col-sm-6 signup-email-input-wrap',
                'class'     => 'form-control signup_input-design'
            ],
            'class'         => 'form-horizontal validate',
            'novalidate'    => true
        ]); ?>

        <?=
        $this->Form->input('email', [
            'label'                        => __("Your email address"),
            'placeholder'                  => 'you@yourdomain.com',
            "data-bv-notempty"             => "true",
            "data-bv-notempty-message"     => __("Email address is empty."),
            'data-bv-emailaddress'         => "false",
            "data-bv-callback"             => "true",
            "data-bv-callback-message"     => " ",
            "data-bv-callback-callback"    => "bvCallbackAvailableEmailNotVerified",
            'data-bv-stringlength'         => 'true',
            'data-bv-stringlength-max'     => 200,
            'data-bv-stringlength-message' => __("It's over limit characters (%s).", 200),
            'required'                     => false,
            'type'                         => 'email',
            'div'                          => ['style' => 'overflow: hidden']
        ]) ?>
        <label>
            <?= __('By clicking <q>I agree. Continue.</q> below, you are agreeing to the <a href="/terms?backBtn=true" target="_blank">Terms of Service</a> and the <a href="/privacy_policy?backBtn=true" target="_blank">Privacy Policy</a>.');?>
        </label>
        <div className="submit">
            <?= $this->Form->button(__('I agree. Continue.'),
                [
                    'type'     => 'submit',
                    'class'    => 'btn btn-primary signup-email-submit-button',
                    'disabled' => 'disabled',
                    'escape'   => false
                ]) ?>
        </div>
      <?= $this->Form->end(); ?>
    </div>
</div>
<?php $this->append('script'); ?>
<script type="text/javascript">
    $(document).ready(function () {
        // 登録可能な email の validate
        require(['validate'], function (validate) {
            window.bvCallbackAvailableEmailNotVerified = validate.bvCallbackAvailableEmailNotVerified;
        });
        if ($('input[name="data[Email][email]"]').val().length > 0) {
            $('.signup-email-submit-button').attr('disabled', false);
        }
    });
</script>
<!-- Goalous-Mar2018-spot-GoogleSearch -->
<script type="text/javascript">
/* <![CDATA[ */
var google_conversion_id = 956754448;
var google_conversion_label = "Z1J_CJ7HhH4QkNSbyAM";
var google_remarketing_only = false;
/* ]]> */
</script>
<script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js">
</script>
<noscript>
<div style="display:inline;">
<img height="1" width="1" style="border-style:none;" alt="" src="//www.googleadservices.com/pagead/conversion/956754448/?label=Z1J_CJ7HhH4QkNSbyAM&amp;guid=ON&amp;script=0"/>
</div>
</noscript>
<?php $this->end(); ?>
<?= $this->App->viewEndComment()?>
