<?php
/**
 * メールアドレスの再認証画面
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
            <div class="panel-heading"><?= __d('gl', "メールアドレスの再認証") ?></div>
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
                'class'         => 'form-horizontal validate',
                'novalidate'    => true
            ]); ?>
            <div class="panel-body">
                <p><?= __d('gl', "Goalousに登録しているメールアドレスを入力して送信してください。") ?></p>

                <p><?= __d('gl', "Goalousからメールアドレス認証用のURLを送信いたします。") ?></p>
                <?=
                $this->Form->input('email', [
                    'label'                        => __d('gl', "メールアドレス"),
                    'data-bv-emailaddress-message' => __d('validate', "メールアドレスが正しくありません。"),
                    "data-bv-notempty-message"     => __d('validate', "入力必須項目です。"),
                    'required'                     => true
                ])?>
            </div>
            <div class="panel-footer">
                <div class="form-group">
                    <div class="col-md-9 col-md-offset-3">
                        <?= $this->Form->submit(__d('gl', "送信"), ['class' => 'btn btn-primary']) ?>
                    </div>
                </div>
            </div>
            <?= $this->Form->end(); ?>
        </div>
    </div>
</div>