<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 6/6/14
 * Time: 3:19 PM
 *
 * @var CodeCompletionView $this
 */
?>
<?= $this->App->viewStartComment()?>
<div class="modal fade" tabindex="-1" id="modal_change_email">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close font_33px close-design" data-dismiss="modal" aria-hidden="true">
                    <span class="close-icon">&times;</span></button>
                <h4 class="modal-title"><?= __("Change email address") ?></h4>
            </div>
            <?=
            $this->Form->create('User', [
                'inputDefaults' => [
                    'div'       => 'form-group',
                    'label'     => [
                        'class' => 'control-label'
                    ],
                    'wrapInput' => 'aaa',
                    'class'     => 'form-control modal_input-design'
                ],
                'url'           => ['controller' => 'users', 'action' => 'change_email'],
                'class'         => 'form-horizontal',
                'novalidate'    => true,
                'id'            => 'ChangeEmail',
            ]); ?>
            <div class="modal-body">
                <p><?= __("Finished to change, you'll get confirmation email.") ?></p>

                <p><?= __("Please open confirmation link on Email.") ?></p>
                <?=
                $this->Form->input('email', [
                    'label'                        => __("New email address"),
                    'placeholder'                  => __("tom@example.com"),
                    'data-bv-stringlength'         => 'true',
                    'data-bv-stringlength-max'     => 200,
                    'data-bv-stringlength-message' => __("It's over limit characters (%s).", 200),
                    "data-bv-notempty"             => "true",
                    'data-bv-emailaddress'         => "false",
                    "data-bv-callback"             => "true",
                    "data-bv-callback-message"     => " ",
                    "data-bv-callback-callback"    => "bvCallbackAvailableEmail",
                ]) ?>
                <?=
                $this->Form->input('password_request2', [
                    'label' => __("Input your password"),
                    'type'  => 'password',
                ]) ?>
            </div>
            <div class="modal-footer modal_pannel-footer">
                <?= $this->Form->submit(__("Send confirmation email"),
                    ['class' => 'btn btn-primary pull-right', 'disabled' => 'disabled']) ?>
                <div class="pull-right">
                    <button type="button" class="btn btn-link design-cancel mr_8px bd-radius_4px" data-dismiss="modal">
                        <?= __("Cancel") ?>
                    </button>
                </div>
            </div>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
<?php $this->append('script') ?>
<script type="text/javascript">
    $(document).ready(function () {
        $('#ChangeEmail').bootstrapValidator({
            live: 'enabled',

            fields: {
                "data[User][password_request2]": {
                    validators: {
                        stringLength: {
                            min: 8,
                            message: "<?=__('At least %2$d characters is required.', "", 8)?>"
                        },
                        notEmpty: {
                            message: "<?=__("変更する場合はパスワード入力が必要です。")?>"
                        }
                    }
                }
            }
        });

        // 登録可能な email の validate
        require(['validate'], function (validate) {
            window.bvCallbackAvailableEmail = validate.bvCallbackAvailableEmail;
        });
    });
</script>
<?php $this->end() ?>
<?= $this->App->viewEndComment()?>
