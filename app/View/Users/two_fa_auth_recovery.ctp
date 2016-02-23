<?php
/**
 * ２段階認証 リカバリコード入力画面
 */
?>
<!-- START app/View/Users/two_fa_auth_recovery.ctp -->
<div class="row">
    <div class="col-sm-8 col-sm-offset-2">
        <div class="panel panel-default">
            <div class="panel-heading"><?= __("リカバリーコードを入力する") ?></div>
            <div class="panel-body login-panel-body">
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
                <p class="mb_12px"><?= __("アカウントにログインできない場合は、リカバリーコードを使用してログインできます。") ?></p>

                <?=
                $this->Form->input('recovery_code', [
                    'label' => __("コード")
                ]) ?>

                <div class="form-group">
                    <div class="col-sm-9 col-sm-offset-3">
                        <?=
                        $this->Form->submit(__("コードを送信"), ['class' => 'btn btn-primary']) ?>
                    </div>
                </div>
                <?= $this->Form->end(); ?>
            </div>
        </div>
    </div>
</div>
<!-- END app/View/Users/two_fa_auth_recovery.ctp -->
