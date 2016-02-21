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
<!-- START app/View/Elements/User/modal_change_password.ctp -->
<div class="modal fade" id="modal_change_password">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close font_33px close-design" data-dismiss="modal" aria-hidden="true">
                    <span class="close-icon">&times;</span></button>
                <h4 class="modal-title"><?= __d('app', "パスワードの変更") ?></h4>
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
                    'label'     => __d('app', "現在のパスワード"),
                    'type'      => 'password',
                    'required'  => true,
                    'maxlength' => 50,
                ]) ?>
                <?=
                $this->Form->input('password', [
                    'label'       => __d('app', "新しいパスワード"),
                    'placeholder' => __d('app', '8文字以上'),
                    'type'        => 'password',
                    'required'    => true,
                    'maxlength'   => 50,
                ]) ?>
                <?=
                $this->Form->input('password_confirm', [
                    'label'     => __d('app', "パスワードを再入力"),
                    'type'      => 'password',
                    'required'  => true,
                    'maxlength' => 50,
                ]) ?>
                <?= $this->Form->hidden('id', ['value' => $this->Session->read('Auth.User.id')]) ?>
            </div>
            <div class="modal-footer modal_pannel-footer">
                <?= $this->Form->submit(__d('app', "変更を保存"),
                                        ['class' => 'btn btn-primary pull-right', 'disabled' => 'disabled']) ?>
                <button type="button" class="btn btn-link design-cancel mr_8px bd-radius_4px" data-dismiss="modal">
                    <?= __d('app', "キャンセル") ?>
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
            feedbackIcons: {
                valid: 'fa fa-check',
                invalid: 'fa fa-times',
                validating: 'fa fa-refresh'
            },
            fields: {
                "data[User][old_password]": {
                    validators: {
                        notEmpty: {
                            message: "<?=__d('validate', "入力必須項目です。")?>"
                        }
                    }
                },
                "data[User][password]": {
                    validators: {
                        stringLength: {
                            min: 8,
                            message: "<?=__d('validate', '%1$d文字以上で%2$d文字以下で入力してください。',8,50)?>"
                        },
                        notEmpty: {
                            message: "<?=__d('validate', "入力必須項目です。")?>"
                        },
                        regexp: {
                            regexp: /^(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])[0-9a-zA-Z]{0,}$/,
                            message: cake.message.validate.e
                        }
                    }
                },
                "data[User][password_confirm]": {
                    validators: {
                        identical: {
                            field: "data[User][password]",
                            message: "<?=__d('validate', "パスワードが一致しません。")?>"
                        },
                        notEmpty: {
                            message: "<?=__d('validate', "入力必須項目です。")?>"
                        }
                    }
                }
            }
        });
    });
</script>

<?php $this->end() ?>
<!-- END app/View/Elements/User/modal_change_password.ctp -->
