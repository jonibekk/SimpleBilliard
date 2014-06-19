<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 6/19/14
 * Time: 2:39 PM
 *
 * @var CodeCompletionView $this
 * @var                    $last_first
 * @var array              $me
 * @var boolean            $is_not_use_local_name
 */
?>
    <div class="panel panel-default">
        <div class="panel-heading"><?= __d('gl', "アカウント") ?></div>
        <?=
        $this->Form->create('User', [
            'inputDefaults' => [
                'div'       => 'form-group',
                'label'     => [
                    'class' => 'col col-md-3 control-label'
                ],
                'wrapInput' => 'col col-md-6',
                'class'     => 'form-control'
            ],
            'class'         => 'form-horizontal',
            'novalidate'    => true,
            'id'            => 'UserAccountForm',
        ]); ?>
        <div class="panel-body">
            <?=
            $this->Form->input('Email.0.email', [
                'label'                        => __d('gl', "メール"),
                'placeholder'                  => __d('gl', "hiroshi@example.com"),
                'data-bv-emailaddress-message' => __d('validate', "メールアドレスが正しくありません。"),
                "data-bv-notempty-message"     => __d('validate', "入力必須項目です。"),
            ])?>
            <?=
            $this->Form->input('update_email_flg', [
                'wrapInput' => 'col col-md-9 col-md-offset-3',
                'label'     => ['class' => null, 'text' => __d('gl', "Goalousからのメールによるニュースや更新情報などを受け取る。")],
                'class'     => false,
                'default'   => true,
            ])?>
            <?=
            $this->Form->input('password', [
                'label'                    => __d('gl', "パスワードを作成"),
                'placeholder'              => __d('gl', '8文字以上'),
                "data-bv-notempty-message" => __d('validate', "入力必須項目です。"),
                'type'                     => 'password',
            ])?>
            <?=
            $this->Form->input('password_confirm', [
                'label'                    => __d('gl', "パスワードを再入力"),
                "data-bv-notempty-message" => __d('validate', "入力必須項目です。"),
                'type'                     => 'password',
            ])?>
        </div>
        <div class="panel-footer">
            <?= $this->Form->submit(__d('gl', "更新"), ['class' => 'btn btn-primary pull-right']) ?>
            <div class="clearfix"></div>
        </div>
        <?= $this->Form->end(); ?>
    </div>
<? $this->append('script') ?>
    <script type="text/javascript">
        $(document).ready(function () {
            $('#UserAccountForm').bootstrapValidator({
                live: 'enabled',
                feedbackIcons: {
                    valid: 'fa fa-check',
                    invalid: 'fa fa-times',
                    validating: 'fa fa-refresh'
                },
                fields: {
                    "data[User][password]": {
                        validators: {
                            stringLength: {
                                min: 8,
                                message: '<?=__d('validate', '%2$d文字以上で入力してください。',"",8)?>'
                            }
                        }
                    },
                    "data[User][password_confirm]": {
                        validators: {
                            identical: {
                                field: "data[User][password]",
                                message: '<?=__d('validate', "パスワードが一致しません。")?>'
                            }
                        }
                    }
                }
            });
        });
    </script>
<? $this->end() ?>