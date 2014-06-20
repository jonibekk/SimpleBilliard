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
                    <p class="form-control-static"><?= $this->request->data['PrimaryEmail']['email'] ?></p>
                </div>
            </div>
            <?=
            $this->Form->input('update_email_flg', [
                'wrapInput' => 'col col-sm-9 col-sm-offset-3',
                'label'     => ['class' => null, 'text' => __d('gl', "Goalousからのメールによるニュースや更新情報などを受け取る。")],
                'class'     => false,
            ])?>
            <?=
            $this->Form->input('language', [
                'label'   => __d('gl', "言語"),
                'type'    => 'select',
                'options' => $language_list,
            ])?>
            <?=
            $this->Form->input('auto_language_flg', [
                'wrapInput' => 'col col-sm-9 col-sm-offset-3',
                'label'     => ['class' => null, 'text' => __d('gl', "自動的に言語を設定する。")],
                'class'     => false,
            ])?>
            <?=
            $this->Form->input('romanize_flg', [
                'wrapInput' => 'col col-sm-9 col-sm-offset-3',
                'label'     => ['class' => null, 'text' => __d('gl', "自分の名前を強制的にローマ字表記にする。")],
                'class'     => false,
            ])?>
            <?=
            $this->Form->input('timezone', [
                'label'   => __d('gl', "タイムゾーン"),
                'type'    => 'select',
                'options' => $timezones,
            ])
            ?>
            <div class="form-group">
                <label for="UserPassword" class="col col-sm-3 control-label"><?= __d('gl', "パスワード") ?></label>

                <div class="col col-sm-6">
                </div>
            </div>
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