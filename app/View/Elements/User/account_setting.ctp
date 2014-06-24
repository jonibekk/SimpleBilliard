<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 6/19/14
 * Time: 2:39 PM
 *
 * @var CodeCompletionView          $this
 * @var                    $last_first
 * @var array              $me
 * @var boolean            $is_not_use_local_name
 * @var array              $language_list
 * @var array                       $timezones
 * @var array                       $not_verified_email
 */
?>
    <div class="panel panel-default">
        <div class="panel-heading"><?= __d('gl', "アカウント") ?></div>
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
            'class'         => 'form-horizontal',
            'novalidate'    => true,
            'id'            => 'UserAccountForm',
        ]); ?>
        <div class="panel-body">
            <div class="form-group">
                <label for="PrimaryEmailEmail" class="col col-sm-3 control-label"><?= __d('gl', "メール") ?></label>

                <div class="col col-sm-6">
                    <p class="form-control-static"><?= h($me['PrimaryEmail']['email']) ?></p>

                    <? if (!empty($not_verified_email)): ?>
                        <p class="form-control-static">
                            <a href="#" rel="tooltip" title="<?= __d('gl', "認証待ちのメールアドレスが存在するため、変更はできません。") ?>">
                                <?= __d('gl', "メールアドレスを変更する") ?>
                            </a>
                        </p>
                        <div class="alert alert-warning fade in">
                            <p><?=
                                __d('gl', '現在、%sの認証待ちです。',
                                    "<b>" . $not_verified_email['Email']['email'] . "</b>") ?></p>

                            <p><?= __d('gl', 'このメールアドレスに送られた確認用のメールをご確認ください。') ?></p>
                            <a href="#" data-toggle="modal" data-target="#modal_delete_email">
                                <?= __d('gl', "メールアドレスの変更をキャンセルする") ?>
                            </a>
                        </div>

                    <? else: ?>
                        <p class="form-control-static">
                            <a href="#" data-toggle="modal" data-target="#modal_change_email">
                                <?= __d('gl', "メールアドレスを変更する") ?>
                            </a>
                        </p>
                    <?
                    endif ?>
                </div>
            </div>
            <?=
            $this->Form->input('update_email_flg', [
                'wrapInput' => 'col col-sm-9 col-sm-offset-3',
                'label'     => ['class' => null, 'text' => __d('gl', "Goalousからのメールによるニュースや更新情報などを受け取る。")],
                'class'     => false,
            ])?>
            <hr>
            <?=
            $this->Form->input('language', [
                'label'   => __d('gl', "言語"),
                'type'    => 'select',
                'options' => $language_list,
            ])?>
            <hr>
            <?=
            $this->Form->input('timezone', [
                'label'   => __d('gl', "タイムゾーン"),
                'type'    => 'select',
                'options' => $timezones,
            ])
            ?>
            <hr>
            <div class="form-group">
                <label for="UserPassword" class="col col-sm-3 control-label"><?= __d('gl', "パスワード") ?></label>

                <div class="col col-sm-6">
                    <p class="form-control-static">
                        <a href="#" data-toggle="modal" data-target="#modal_change_password"><?=
                            __d('gl',
                                "パスワードを変更する") ?></a>
                    </p>
                </div>
            </div>
            <hr>
            <?=
            $this->Form->input('password_request', [
                'label' => __d('gl', "パスワード入力"),
                'type'  => 'password',
            ])?>
        </div>
        <div class="panel-footer">
            <?= $this->Form->submit(__d('gl', "変更を保存"), ['class' => 'btn btn-primary pull-right']) ?>
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
                    },
                    "data[User][password_request]": {
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
<?= $this->element('User/modal_change_password') ?>
<?= $this->element('User/modal_change_email') ?>
<?
if (!empty($not_verified_email)) {
    echo $this->element('User/modal_delete_email',
                        ['email'    => $not_verified_email['Email']['email'],
                         'email_id' => $not_verified_email['Email']['id']]);
}
?>