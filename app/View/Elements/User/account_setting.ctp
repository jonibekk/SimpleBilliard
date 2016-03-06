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
    <div class="panel-heading"><?= __("アカウント") ?></div>
    <?=
    $this->Form->create('User', [
        'inputDefaults' => [
            'div'       => 'form-group',
            'label'     => [
                'class' => 'col col-sm-3 control-label form-label'
            ],
            'class'     => 'form-control setting_input-design'
        ],
        'class'         => 'form-horizontal',
        'novalidate'    => true,
        'id'            => 'UserAccountForm',
    ]); ?>
    <div class="panel-body user-setting-panel-body">
        <div class="form-group">
            <label for="PrimaryEmailEmail" class="col col-sm-3 control-label form-label"><?= __("メール") ?></label>

            <div class="col col-sm-6">
                <p class="form-control-static"><?= h($me['PrimaryEmail']['email']) ?></p>

                <?php if (!empty($not_verified_email)): ?>
                    <p class="form-control-static">
                        <a href="#" rel="tooltip" title="<?= __("認証待ちのメールアドレスが存在するため、変更はできません。") ?>">
                            <?= __("メールアドレスを変更する") ?>
                        </a>
                    </p>
                    <div class="alert alert-warning fade in">
                        <p><?=
                            __('現在、%sの認証待ちです。',
                                "<b>" . $not_verified_email['Email']['email'] . "</b>") ?></p>

                        <p><?= __('このメールアドレスに送られた確認用のメールをご確認ください。') ?></p>
                        <a href="#" data-toggle="modal" data-target="#modal_delete_email">
                            <?= __("メールアドレスの変更をキャンセルする") ?>
                        </a>
                    </div>

                <?php else: ?>
                    <p class="form-control-static">
                        <a href="#" data-toggle="modal" data-target="#modal_change_email">
                            <?= __("メールアドレスを変更する") ?>
                        </a>
                    </p>
                <?php endif ?>
            </div>
        </div>
        <?=
        $this->Form->input('update_email_flg', [
            'wrapInput' => 'col col-sm-9 col-sm-offset-3',
            'label'     => ['class' => null, 'text' => __("Goalousからのメールによるニュースや更新情報などを受け取る。")],
            'class'     => false,
        ]) ?>
        <hr>
        <?=
        $this->Form->input('language', [
            'label'   => __("言語"),
            'type'    => 'select',
            'options' => $language_list,
            'wrapInput' => 'user-setting-lang-select-wrap col col-sm-6'
        ]) ?>
        <hr>
        <?=
        $this->Form->input('timezone', [
            'label'   => __("Timezone"),
            'type'    => 'select',
            'options' => $timezones,
            'wrapInput' => 'user-setting-timezone-select-wrap col col-sm-6'
        ])
        ?>
        <hr>
        <?php if (!empty($my_teams)) {
            echo $this->Form->input('default_team_id', [
                'label'   => __("デフォルトチーム"),
                'type'    => 'select',
                'options' => $my_teams,
                'wrapInput' => 'user-setting-default-team-select-wrap col col-sm-6'
            ]);
            echo "<hr>";
        }
        ?>
        <div class="form-group">
            <label for="UserPassword" class="col col-sm-3 control-label form-label"><?= __("パスワード") ?></label>

            <div class="col col-sm-6">
                <p class="form-control-static">
                    <a href="#" data-toggle="modal" data-target="#modal_change_password">
                    <?= __("パスワードを変更する") ?>
                    </a>
                </p>
            </div>
        </div>
        <hr>
        <div class="form-group">
            <label for="2FA" class="col col-sm-3 control-label form-label"><?= __("2段階認証") ?></label>

            <div class="col col-sm-6">
                <p class="form-control-static">
                    <?php if (viaIsSet($this->request->data['User']['2fa_secret'])): ?>
                        <a href="<?= $this->Html->url(['controller' => 'users', 'action' => 'ajax_get_modal_2fa_delete']) ?>"
                           class="modal-ajax-get"><?= __("解除する") ?></a>
                    <?php else: ?>
                        <a href="<?= $this->Html->url(['controller' => 'users', 'action' => 'ajax_get_modal_2fa_register']) ?>"
                           class="modal-ajax-get"><?= __("設定する") ?></a>
                    <?php endif; ?>
                </p>
                <?php if (viaIsSet($this->request->data['User']['2fa_secret'])): ?>
                <p class="form-control-static">
                    <a href="<?= $this->Html->url(['controller' => 'users',
                                                   'action'     => 'ajax_get_modal_recovery_code']) ?>"
                       id="ShowRecoveryCodeButton"><?= __("リカバリーコードを表示") ?></a>
                </p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="panel-footer setting_pannel-footer">
        <?= $this->Form->submit(__("Save changes"), ['class' => 'btn btn-primary pull-right']) ?>
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
