<?php
/**
 * emailアドレス入力画面
 *
 * @var CodeCompletionView $this
 */
?>
<!-- START app/View/Signup/email.ctp -->
<div class="row">
    <div class="col-sm-8 col-sm-offset-2">
        <div class="panel panel-default">
            <div class="panel-heading"><?= __('Create a new team') ?></div>
            <?=
            $this->Form->create('Email', [
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
                <?=
                $this->Form->input('email', [
                    'label'                        => __("Your email address"),
                    'placeholder'                  => 'you@yourdomain.com',
                    "data-bv-notempty"             => "true",
                    'data-bv-emailaddress'         => "false",
                    "data-bv-callback"             => "true",
                    "data-bv-callback-message"     => " ",
                    "data-bv-callback-callback"    => "bvCallbackAvailableEmailNotVerified",
                    'data-bv-stringlength'         => 'true',
                    'data-bv-stringlength-max'     => 200,
                    'data-bv-stringlength-message' => __("It's over limit characters (%s).", 200),
                    'required'                     => false,
                ]) ?>
            </div>
            <div class="panel-footer">
                <div class="row">
                    <div class="col-sm-9 col-sm-offset-3">
                        <?= $this->Form->button(__('Next') . ' <i class="fa fa-angle-right"></i>',
                            [
                                'type'     => 'submit',
                                'class'    => 'btn btn-primary',
                                'disabled' => 'disabled',
                                'escape'   => false
                            ]) ?>
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
        // 登録可能な email の validate
        require(['validate'], function (validate) {
            window.bvCallbackAvailableEmailNotVerified = validate.bvCallbackAvailableEmailNotVerified;
        });
    });
</script>
<?php $this->end(); ?>
<!-- END app/View/Signup/email.ctp -->
