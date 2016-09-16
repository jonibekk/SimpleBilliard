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
            'div'                          => ['style' => 'overflow: hidden']
        ]) ?>

        <div className="submit">
            <?= $this->Form->button(__('Next') . ' <i class="fa fa-angle-right"></i>',
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
    });
</script>
<?php $this->end(); ?>
<?= $this->App->viewEndComment()?>
