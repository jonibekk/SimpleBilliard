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
<div class="modal fade" id="modal_change_password">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close font_33px close-design" data-dismiss="modal" aria-hidden="true">
                    <span class="close-icon">&times;</span></button>
                <h4 class="modal-title"><?= __("Change password") ?></h4>
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
                'url'           => ['action' => 'change_password'],
                'class'         => 'form-horizontal',
                'novalidate'    => true,
                'id'            => 'ChangePassword',
            ]); ?>
            <div class="modal-body">
                <?=
                $this->Form->input('old_password', [
                    'label'     => __("Current password"),
                    'type'      => 'password',
                    'required'  => true,
                    'maxlength' => 50,
                ]) ?>
                <?=
                $this->Form->input('password', [
                    'label'       => __("New password"),
                    'placeholder' => __('Use at least 8 characters and use a mix of capital characters, small characters and numbers. Symbols are not allowed.'),
                    'type'        => 'password',
                    'required'    => true,
                    'maxlength'   => 50,
                ]) ?>
                <?=
                $this->Form->input('password_confirm', [
                    'label'     => __("Confirm your password"),
                    'type'      => 'password',
                    'required'  => true,
                    'maxlength' => 50,
                ]) ?>
                <?= $this->Form->hidden('id', ['value' => $this->Session->read('Auth.User.id')]) ?>
            </div>
            <div class="modal-footer modal_pannel-footer">
                <?= $this->Form->submit(__("Save changes"),
                    ['class' => 'btn btn-primary pull-right', 'disabled' => 'disabled']) ?>
                <button type="button" class="btn btn-link design-cancel mr_8px bd-radius_4px" data-dismiss="modal">
                    <?= __("Cancel") ?>
                </button>
            </div>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
<?php $this->append('script') ?>
<script type="text/javascript">
    $(document).ready(function () {
        $('#ChangePassword').bootstrapValidator({
            live: 'enabled',

            fields: {
                "data[User][old_password]": {
                    validators: {
                        notEmpty: {
                            message: "<?=__("Input is required.")?>"
                        }
                    }
                },
                "data[User][password]": {
                    validators: {
                        stringLength: {
                            min: 8,
                            message: "<?=__('%1$d文字以上で%2$d文字以下で入力してください。', 8, 50)?>"
                        },
                        notEmpty: {
                            message: "<?=__("Input is required.")?>"
                        },
                        regexp: {
                            regexp: /^(?=.*[0-9])(?=.*[a-zA-Z])[0-9a-zA-Z\!\@\#\$\%\^\&\*\(\)\_\-\+\=\{\}\[\]\|\:\;\<\>\,\.\?\/]{0,}$/,
                            message: cake.message.validate.e
                        }
                    }
                },
                "data[User][password_confirm]": {
                    validators: {
                        identical: {
                            field: "data[User][password]",
                            message: "<?=__("Both of passwords are not same.")?>"
                        },
                        notEmpty: {
                            message: "<?=__("Input is required.")?>"
                        }
                    }
                }
            }
        });
    });
</script>

<?php $this->end() ?>
<?= $this->App->viewEndComment()?>
