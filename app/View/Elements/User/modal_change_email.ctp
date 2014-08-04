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
<!-- START app/View/Elements/User/modal_change_email.ctp -->
<div class="modal fade" id="modal_change_email">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title"><?= __d('gl', "メールアドレスの変更") ?></h4>
            </div>
            <?=
            $this->Form->create('User', [
                'inputDefaults' => [
                    'div'       => 'form-group',
                    'label'     => [
                        'class' => 'col col-sm-3 control-label'
                    ],
                    'wrapInput' => 'col col-sm-6',
                    'class'     => 'form-control'
                ],
                'url'           => ['controller' => 'users', 'action' => 'change_email'],
                'class'         => 'form-horizontal',
                'novalidate'    => true,
                'id'            => 'ChangeEmail',
            ]); ?>
            <div class="modal-body">
                <p><?= __d('gl', "メールアドレスを保存後に、そのメールアドレス宛に認証用のメールが届きます。") ?></p>

                <p><?= __d('gl', "これは、あなたがそのメールアドレスの持ち主である事を確認する為のものです。") ?></p>
                <?=
                $this->Form->input('email', [
                    'label'       => __d('gl', "メールアドレス"),
                    'placeholder' => __d('gl', "hiroshi@example.com"),
                ])?>
                <hr>
                <?=
                $this->Form->input('password_request2', [
                    'label' => __d('gl', "パスワード入力"),
                    'type'  => 'password',
                ])?>
            </div>
            <div class="modal-footer">
                <?= $this->Form->submit(__d('gl', "変更を保存"), ['class' => 'btn btn-primary']) ?>
            </div>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
<? $this->append('script') ?>
<script type="text/javascript">
    $(document).ready(function () {
        $('#ChangeEmail').bootstrapValidator({
            live: 'enabled',
            feedbackIcons: {
                valid: 'fa fa-check',
                invalid: 'fa fa-times',
                validating: 'fa fa-refresh'
            },
            fields: {
                "data[User][email]": {
                    validators: {
                        notEmpty: {
                            message: '<?=__d('validate', "入力必須項目です。")?>'
                        },
                        emailAddress: {
                            message: '<?=__d('validate', "メールアドレスが正しくありません。")?>'
                        }
                    }
                },
                "data[User][password_request2]": {
                    validators: {
                        stringLength: {
                            min: 8,
                            message: '<?=__d('validate', '%2$d文字以上で入力してください。',"",8)?>'
                        },
                        notEmpty: {
                            message: '<?=__d('validate', "変更する場合はパスワード入力が必要です。")?>'
                        }
                    }
                }
            }
        });
    });
</script>
<? $this->end() ?>
<!-- END app/View/Elements/User/modal_change_email.ctp -->
