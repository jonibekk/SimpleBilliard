<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 6/19/14
 * Time: 2:39 PM
 *
 * @var CodeCompletionView          $this
 * @var                             $last_first
 * @var array                       $me
 * @var boolean                     $is_not_use_local_name
 * @var array                       $language_list
 * @var array                       $timezones
 * @var array                       $not_verified_email
 * @var                             $my_teams
 */
?>
<!-- START app/View/Elements/User/account_setting.ctp -->
<div class="panel panel-default">
    <div class="panel-heading"><?= __d('gl', "アカウント") ?></div>
    <?=
    $this->Form->create('User', [
        'inputDefaults' => [
            'div'       => 'form-group',
            'label'     => [
                'class' => 'col col-sm-3 control-label form-label'
            ],
            'wrapInput' => 'col col-sm-6',
            'class'     => 'form-control setting_input-design'
        ],
        'class'         => 'form-horizontal',
        'novalidate'    => true,
        'id'            => 'UserAccountForm',
    ]); ?>
    <div class="panel-body user-setting-panel-body">
        <div class="form-group">
            <label for="PrimaryEmailEmail" class="col col-sm-3 control-label form-label"><?= __d('gl', "メール") ?></label>

            <div class="col col-sm-6">
                <p class="form-control-static"><?= h($me['PrimaryEmail']['email']) ?></p>

                <?php if (!empty($not_verified_email)): ?>
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

                <?php else: ?>
                    <p class="form-control-static">
                        <a href="#" data-toggle="modal" data-target="#modal_change_email">
                            <?= __d('gl', "メールアドレスを変更する") ?>
                        </a>
                    </p>
                <?php endif ?>
            </div>
        </div>
        <?=
        $this->Form->input('update_email_flg', [
            'wrapInput' => 'col col-sm-9 col-sm-offset-3',
            'label'     => ['class' => null, 'text' => __d('gl', "Goalousからのメールによるニュースや更新情報などを受け取る。")],
            'class'     => false,
        ]) ?>
        <hr>
        <?=
        $this->Form->input('language', [
            'label'   => __d('gl', "言語"),
            'type'    => 'select',
            'options' => $language_list,
        ]) ?>
        <hr>
        <?=
        $this->Form->input('timezone', [
            'label'   => __d('gl', "タイムゾーン"),
            'type'    => 'select',
            'options' => $timezones,
        ])
        ?>
        <hr>
        <?php if (!empty($my_teams)) {
            echo $this->Form->input('default_team_id', [
                'label'   => __d('gl', "デフォルトチーム"),
                'type'    => 'select',
                'options' => $my_teams,
            ]);
            echo "<hr>";
        }
        ?>
        <div class="form-group">
            <label for="UserPassword" class="col col-sm-3 control-label form-label"><?= __d('gl', "パスワード") ?></label>

            <div class="col col-sm-6">
                <p class="form-control-static">
                    <a href="#" data-toggle="modal" data-target="#modal_change_password"><?=
                        __d('gl',
                            "パスワードを変更する") ?></a>
                </p>
            </div>
        </div>
        <div class="form-group">
            <label for="2FA" class="col col-sm-3 control-label form-label"><?= __d('gl', "２要素認証") ?></label>

            <div class="col col-sm-6">
                <p class="form-control-static">
                    <?php if (viaIsSet($this->request->data['User']['2fa_secret'])): ?>
                        <a href="<?= $this->Html->url(['controller' => 'users', 'action' => 'ajax_get_modal_2fa_delete']) ?>"
                           class="modal-ajax-get"><?= __d('gl', "解除する") ?></a>
                    <?php else: ?>
                        <a href="<?= $this->Html->url(['controller' => 'users', 'action' => 'ajax_get_modal_2fa_register']) ?>"
                           class="modal-ajax-get"><?= __d('gl', "設定する") ?></a>
                    <?php endif; ?>
                </p>
            </div>
        </div>
    </div>
    <div class="panel-footer setting_pannel-footer">
        <?= $this->Form->submit(__d('gl', "変更を保存"), ['class' => 'btn btn-primary pull-right']) ?>
        <div class="clearfix"></div>
    </div>
    <?= $this->Form->end(); ?>
</div>
<?php $this->append('script') ?>
<script type="text/javascript">
    $(document).ready(function () {
        $('#UserAccountForm').bootstrapValidator({
            live: 'enabled',
            feedbackIcons: {
                valid: 'fa fa-check',
                invalid: 'fa fa-times',
                validating: 'fa fa-refresh'
            },
            fields: {}
        });
    });
</script>
<?php $this->end() ?>
<?= $this->element('User/modal_change_password') ?>
<?= $this->element('User/modal_change_email') ?>
<?php if (!empty($not_verified_email)) {
    echo $this->element('User/modal_delete_email',
                        ['email'    => $not_verified_email['Email']['email'],
                         'email_id' => $not_verified_email['Email']['id']]);
}
?>
<!-- END app/View/Elements/User/account_setting.ctp -->
