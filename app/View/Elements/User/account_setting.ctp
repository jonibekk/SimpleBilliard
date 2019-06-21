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
 * @var array                       $translation_languages
 * @var bool                        $team_can_translate
 */
?>
<?= $this->App->viewStartComment() ?>
<div class="panel panel-default">
    <div class="panel-heading"><?= __("Account") ?></div>
    <?=
    $this->Form->create('User', [
        'inputDefaults' => [
            'div'   => 'form-group',
            'label' => [
                'class' => 'col col-sm-3 control-label form-label'
            ],
            'class' => 'form-control setting_input-design'
        ],
        'class'         => 'form-horizontal',
        'novalidate'    => true,
        'id'            => 'UserAccountForm',
    ]); ?>
    <div class="panel-body user-setting-panel-body">
        <div class="form-group">
            <label for="PrimaryEmailEmail" class="col col-sm-3 control-label form-label"><?= __("Email") ?></label>

            <div class="col col-sm-6">
                <p class="form-control-static"><?= h($me['PrimaryEmail']['email']) ?></p>

                <?php if (!empty($not_verified_email)): ?>
                    <p class="form-control-static">
                        <a href="#" rel="tooltip"
                           title="<?= __("Email address can't be changed because of the authentication.") ?>">
                            <?= __("Change email address") ?>
                        </a>
                    </p>
                    <div class="alert alert-warning fade in">
                        <p><?=
                            __('%s Authentication waiting currently.',
                                "<b>" . $not_verified_email['Email']['email'] . "</b>") ?></p>

                        <p><?= __('Confirm the email sent to this email address.') ?></p>
                        <a href="#" data-toggle="modal" data-target="#modal_delete_email">
                            <?= __("Cancel changing the email address") ?>
                        </a>
                    </div>

                <?php else: ?>
                    <p class="form-control-static">
                        <a href="#" data-toggle="modal" data-target="#modal_change_email">
                            <?= __("Change email address") ?>
                        </a>
                    </p>
                <?php endif ?>
            </div>
        </div>
        <?=
        $this->Form->input('update_email_flg', [
            'wrapInput' => 'col col-sm-9 col-sm-offset-3',
            'label'     => ['class' => null, 'text' => __("I receive the news and updates by email from Goalous.")],
            'class'     => false,
        ]) ?>
        <hr>
        <?=
        $this->Form->input('language', [
            'label'     => __("Language"),
            'type'      => 'select',
            'options'   => $language_list,
            'wrapInput' => 'user-setting-lang-select-wrap col col-sm-6'
        ]) ?>
        <hr>
        <?php if (!empty($team_can_translate)) {
            echo $this->Form->input('TeamMember.0.default_translation_language', [
                'label'     => __("Translation Language"),
                'type'      => 'select',
                'options'   => $translation_languages,
                'wrapInput' => 'user-setting-lang-select-wrap col col-sm-6'
            ]);
            echo "<hr>";
        }
        ?>
        <?=
        $this->Form->input('timezone', [
            'label'     => __("Timezone"),
            'type'      => 'select',
            'options'   => $timezones,
            'wrapInput' => 'user-setting-timezone-select-wrap col col-sm-6'
        ])
        ?>
        <hr>
        <?php if (!empty($my_teams)) {
            echo $this->Form->input('default_team_id', [
                'label'     => __("Default Team"),
                'type'      => 'select',
                'options'   => $my_teams,
                'wrapInput' => 'user-setting-default-team-select-wrap col col-sm-6'
            ]);
            echo "<hr>";
        }
        ?>
        <div class="form-group">
            <label for="UserPassword" class="col col-sm-3 control-label form-label"><?= __("Password") ?></label>

            <div class="col col-sm-6">
                <p class="form-control-static">
                    <a href="#" data-toggle="modal" data-target="#modal_change_password">
                        <?= __("Change password") ?>
                    </a>
                </p>
            </div>
        </div>
        <hr>
        <div class="form-group">
            <label for="2FA" class="col col-sm-3 control-label form-label"><?= __("2-Step Verification") ?></label>

            <div class="col col-sm-6">
                <p class="form-control-static">
                    <?php if (Hash::get($this->request->data, 'User.2fa_secret')): ?>
                        <a href="#"
                           data-url="<?= $this->Html->url([
                               'controller' => 'users',
                               'action'     => 'ajax_get_modal_2fa_delete'
                           ]) ?>"
                           class="modal-ajax-get"><?= __("Disable") ?></a>
                    <?php else: ?>
                        <a href="#"
                           data-url="<?= $this->Html->url([
                               'controller' => 'users',
                               'action'     => 'ajax_get_modal_2fa_register'
                           ]) ?>"
                           class="modal-ajax-get"><?= __("Enable") ?></a>
                    <?php endif; ?>
                </p>
                <?php if (Hash::get($this->request->data, 'User.2fa_secret')): ?>
                    <p class="form-control-static">
                        <a href="<?= $this->Html->url([
                            'controller' => 'users',
                            'action'     => 'ajax_get_modal_recovery_code'
                        ]) ?>"
                           id="ShowRecoveryCodeButton"><?= __("Show Recovery codes") ?></a>
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

      fields: {}
    });
  });
</script>
<?php $this->end() ?>
<?= $this->element('User/modal_change_password') ?>
<?= $this->element('User/modal_change_email') ?>
<?php if (!empty($not_verified_email)) {
    echo $this->element('User/modal_delete_email',
        [
            'email'    => $not_verified_email['Email']['email'],
            'email_id' => $not_verified_email['Email']['id']
        ]);
}
?>
<?= $this->App->viewEndComment() ?>
