<?php
/**
 * メールアドレスの再認証画面
 * Created by PhpStorm.
 * User: daikihirakata
 * Date: 2014/06/01
 * Time: 0:19
 *
 * @var CodeCompletionView $this
 */
?>
<!-- START app/View/Users/token_resend.ctp -->
<div class="row">
    <div class="col-sm-8 col-sm-offset-2">
        <div class="panel panel-default">
            <div class="panel-heading"><?= __("メールアドレスの再認証") ?></div>
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
                'class'         => 'form-horizontal validate',
                'novalidate'    => true
            ]); ?>
            <div class="panel-body">
                <p><?= __("Goalousに登録しているメールアドレスを入力して送信してください。") ?></p>

                <p><?= __("Goalousからメールアドレス認証用のURLを送信いたします。") ?></p>
                <?=
                $this->Form->input('email', [
                    'label'                        => __("メールアドレス"),
                    'data-bv-emailaddress-message' => __("メールアドレスが正しくありません。"),
                    "data-bv-notempty-message"     => __("入力必須項目です。"),
                    'required'                     => true
                ]) ?>
            </div>
            <div class="panel-footer">
                <div class="form-group">
                    <div class="col-sm-9 col-sm-offset-3">
                        <?= $this->Form->submit(__("送信"), ['class' => 'btn btn-primary']) ?>
                    </div>
                </div>
            </div>
            <?= $this->Form->end(); ?>
        </div>
    </div>
</div>
<!-- END app/View/Users/token_resend.ctp -->
