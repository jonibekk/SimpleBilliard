<?php
/**
 * ログイン画面
 * Created by PhpStorm.
 * User: daikihirakata
 * Date: 2014/06/01
 * Time: 0:19
 *
 * @var $this View
 */
?>
<div class="row">
    <div class="col-md-8 col-md-offset-2">
        <div class="panel panel-default">
            <div class="panel-heading"><?= __d('gl', "Goalousにログイン！") ?></div>
            <div class="panel-body">
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
                    'novalidate'    => true
                ]); ?>
                <?=
                $this->Form->input('email', [
                    'label' => __d('gl', "メールアドレス"),
                ])?>
                <?=
                $this->Form->input('password', [
                    'label'    => __d('gl', "パスワード"),
                    'type'     => 'password',
                    'required' => false,
                ])?>
                <div class="form-group">
                    <div class="col-md-9 col-md-offset-3">
                        <?= $this->Form->submit(__d('gl', "ログイン"), ['class' => 'btn btn-primary']) ?>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-5 col-md-offset-3">
                        <?=
                        $this->Html->link(__d('gl', 'パスワードを忘れた場合はこちら'), ['action' => 'password_reset'],
                                          ['class' => 'link']) ?>
                    </div>
                    <div class="col-md-4">
                        <?=
                        $this->Html->link(__d('gl', '新規ユーザ登録はこちら'), ['action' => 'register'],
                                          ['class' => 'link']) ?>
                    </div>
                </div>
                <?= $this->Form->end(); ?>
            </div>
        </div>
    </div>
</div>
