<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 6/6/14
 * Time: 3:19 PM
 *
 * @var View $this
 */
?>
<!-- START app/View/Elements/User/modal_change_password.ctp -->
<div class="modal fade" id="modal_change_password">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close font_33px close-design" data-dismiss="modal" aria-hidden="true">
                    <span class="close-icon">&times;</span></button>
                <h4 class="modal-title"><?= __d('gl', "パスワードの変更") ?></h4>
            </div>
            <?=
            $this->Form->create('User', [
                'inputDefaults' => [
                    'div'       => 'form-group',
                    'label'     => [
                        'class' => 'col col-sm-3 control-label'
                    ],
                    'wrapInput' => 'col col-sm-6',
                    'class' => 'form-control modal_input-design'
                ],
                'url'           => ['action' => 'change_password'],
                'class'         => 'form-horizontal',
                'novalidate'    => true,
                'id'            => 'ChangePassword',
            ]); ?>
            <div class="modal-body">
                <?=
                $this->Form->input('old_password', [
                    'label'    => __d('gl', "現在のパスワード"),
                    'type'     => 'password',
                    'required' => true,
                ])?>
                <?=
                $this->Form->input('password', [
                    'label'       => __d('gl', "新しいパスワード"),
                    'placeholder' => __d('gl', '8文字以上'),
                    'type'        => 'password',
                    'required'    => true,
                ])?>
                <?=
                $this->Form->input('password_confirm', [
                    'label'    => __d('gl', "パスワードを再入力"),
                    'type'     => 'password',
                    'required' => true,
                ])?>
                <?= $this->Form->hidden('id', ['value' => $this->Session->read('Auth.User.id')]) ?>
            </div>
            <div class="modal-footer modal_pannel-footer">
                <?= $this->Form->submit(__d('gl', "変更を保存"),
                                        ['class' => 'btn btn-primary pull-right', 'disabled' => 'disabled']) ?>
                <button type="button" class="btn btn-link design-cancel margin-right-8px" data-dismiss="modal">
                    <?= __d('gl', "キャンセル") ?>
                </button>
            </div>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
<? $this->append('script') ?>
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
                        stringLength: {
                            min: 8,
                            message: '<?=__d('validate', '%2$d文字以上で入力してください。',"",8)?>'
                        },
                        notEmpty: {
                            message: '<?=__d('validate', "入力必須項目です。")?>'
                        }
                    }
                },
                "data[User][password]": {
                    validators: {
                        stringLength: {
                            min: 8,
                            message: '<?=__d('validate', '%2$d文字以上で入力してください。',"",8)?>'
                        },
                        notEmpty: {
                            message: '<?=__d('validate', "入力必須項目です。")?>'
                        }
                    }
                },
                "data[User][password_confirm]": {
                    validators: {
                        identical: {
                            field: "data[User][password]",
                            message: '<?=__d('validate', "パスワードが一致しません。")?>'
                        },
                        notEmpty: {
                            message: '<?=__d('validate', "入力必須項目です。")?>'
                        }
                    }
                }
            }
        });
    });
</script>

<? $this->end() ?>
<!-- END app/View/Elements/User/modal_change_password.ctp -->
