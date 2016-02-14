<?php
/**
 * ログイン画面
 * Created by PhpStorm.
 * User: daikihirakata
 * Date: 2014/06/01
 * Time: 0:19
 *
 * @var CodeCompletionView $this
 */
?>
<!-- START app/View/Users/login.ctp -->
<div class="row">
    <div class="col-sm-8 col-sm-offset-2">
        <div class="panel panel-default">
            <div class="panel-heading"><?= __d('gl', "Goalousにログイン！") ?></div>
            <div class="panel-body login-panel-body">
                <div id="RequireCookieAlert" class="alert alert-danger" style="display:none">
                    <?= __d('gl', "Cookieを有効にしてください") ?>
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
                    'label' => __d('gl', "メールアドレス")
                ]) ?>

                <?php //TODO For disabling autocomplete from the browser end ?>
                <input type="text" style="display: none">

                <?=
                $this->Form->input('password', [
                    'label'    => __d('gl', "パスワード"),
                    'type'     => 'password',
                    'required' => false,
                    'value'    => ''
                ]) ?>

                <?= $this->Form->hidden('installation_id', [
                    'id'    => 'installation_id',
                    'value' => 'no_value'
                ]) ?>
                <?php $this->Form->unlockField('User.installation_id') ?>
                <div class="form-group">
                    <div class="col-sm-9 col-sm-offset-3">
                        <?=
                        $this->Form->submit(__d('gl', "ログイン"),
                                            ['class' => 'btn btn-primary' /*, 'disabled'=>'disabled'*/]) ?>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-5 col-sm-offset-3">
                        <?php
                        $options = ['class' => 'link'];
                        if ($is_mb_app) {
                            $options = ['class' => 'link', 'target' => '_blank', 'onclick' => "window.open(this.href,'_system');return false;"];
                        }
                        ?>
                        <?=
                        $this->Html->link(__d('gl', 'パスワードを忘れた場合はこちら'), ['action' => 'password_reset'], $options) ?>
                    </div>
                    <div class="col-sm-4">
                        <?=
                        $this->Html->link(__d('gl', '新規ユーザ登録はこちら'), ['action' => 'register'], $options) ?>
                    </div>
                </div>
                <?= $this->Form->end(); ?>
            </div>
        </div>
    </div>
</div>
<!-- END app/View/Users/login.ctp -->
