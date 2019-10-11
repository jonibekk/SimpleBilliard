<?= $this->App->viewStartComment()?>
<div class="row">
    <div class="col-sm-8 col-sm-offset-2">
        <div class="panel panel-default">
            <div class="panel-heading"><?= __("Login Goalous!") ?></div>
            <div class="panel-body login-panel-body">
                <div id="RequireCookieAlert" class="alert alert-danger" style="display:none">
                    <?= __("Please enable cookie.") ?>
                </div>
                <?=
                $this->Form->create('User', [
                    'inputDefaults' => [
                        'div'       => 'form-group',
                        'label'     => [
                            'class' => 'col col-sm-3 control-label form-label'
                        ],
                        'wrapInput' => 'col col-sm-6',
                        'class'     => 'form-control login_input-design disable-change-warning'
                    ],
                    'class'         => 'form-horizontal login-form',
                    'novalidate'    => true
                ]); ?>
                <?=
                $this->Form->input('email', [
                    'label' => __("Email Address")
                ]) ?>

                <input type="text" style="display: none">

                <?=
                $this->Form->input('password', [
                    'label'    => __("Password"),
                    'type'     => 'password',
                    'required' => false,
                    'value'    => ''
                ]) ?>

                <?= $this->Form->hidden('installation_id', [
                    'id'    => 'installation_id',
                    'value' => 'no_value'
                ]) ?>
                <?= $this->Form->hidden('app_version', [
                    'id'    => 'app_version',
                    'value' => 'no_value'
                ]) ?>
                <?php $this->Form->unlockField('User.installation_id') ?>
                <?php $this->Form->unlockField('User.app_version') ?>
                <div class="form-group">
                    <div class="col-sm-9 col-sm-offset-3">
                        <?= __("By clicking <q>I Agree. Login.</q> below, you are agreeing to the <a href='/terms' target='_blank'>Terms&nbsp;of&nbsp;Service</a> and the <a href='/privacy_policy' target='_blank'>Privacy&nbsp;Policy</a>.")?>
                        <?=
                        $this->Form->submit(__("I Agree. Login."),
                            ['class' => 'btn btn-primary' /*, 'disabled'=>'disabled'*/]) ?>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-5 col-sm-offset-3">
                        <?php
                        $options = ['class' => 'link'];
                        if ($is_mb_app) {
                            $options = ['class'   => 'link',
                                        'target'  => '_blank',
                                        'onclick' => "window.open(this.href,'_system');return false;"
                            ];
                        }
                        ?>
                    </div>
                </div>
                <?= $this->Form->end(); ?>
            </div>
        </div>
    </div>
</div>
<?= $this->App->viewEndComment()?>
