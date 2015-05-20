<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 6/6/14
 * Time: 3:19 PM
 *
 * @var CodeCompletionView $this
 * @var                    $url_2fa
 */
?>

<style type="text/css">
    .two_fa_step_box {
        border-bottom-style: dashed;
        border-bottom-width: thin;
        margin-bottom: 18px;
        margin-top: 5px;
        padding: 18px;
    }
    .two_fa_title {

    }
    .two_fa_app_download_link {
        color: #6495ED;
    }
</style>

<!-- START app/View/Elements/User/modal_2fa_register.ctp -->
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close font_33px close-design" data-dismiss="modal" aria-hidden="true"><span
                    class="close-icon">&times;</span></button>
            <h4 class="modal-title"><?= __d('gl', "２段階認証設定") ?></h4>
        </div>
        <?=
        $this->Form->create('User', [
            'url'           => ['controller' => 'users', 'action' => 'register_2fa'],
            'inputDefaults' => [
                'div'       => 'form-group',
                'label'     => [
                    'class' => 'col col-sm-3 modal-label pr_12px'
                ],
                'wrapInput' => 'col col-sm-6',
                'class'     => 'form-control'
            ],
            'class'         => 'form-horizontal',
            'novalidate'    => true,
        ]); ?>
        <div class="modal-body" style="max-height: none">
            <p>STEP 1 ></p>
            <div class="two_fa_step_box">
                <label for="" ><?= __d('gl', "アプリをインストールする") ?></label>
                <p><?= __d('gl', "Google認証システム") ?></p>
                <p>
                    Android <a class="two_fa_app_download_link"href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2" target="_blank"><?= __d('gl', "こちらからダウンロード") ?></a>
                </p>
                <p>
                    IOS <a class="two_fa_app_download_link" href="http://itunes.apple.com/us/app/google-authenticator/id388497605" target="_blank"><?= __d('gl', "こちらからダウンロード") ?></a>
                </p>
            </div>

            <p>STEP 2 ></p>
            <div class="two_fa_step_box">
                <label for="" ><?= __d('gl', "バーコードをスキャンする") ?></label>
                <p><?= __d('gl', "アプリを起動し、カメラを使用してバーコードをスキャンします。") ?></p>
                <?= $this->Html->image($url_2fa) ?>
            </div>

            <p>STEP 3 ></p>
            <div class="two_fa_step_box">
                <label for="" ><?= __d('gl', "確認コードを入力する") ?></label>
                <p><?= __d('gl', "バーコードをスキャンしたら、アプリで生成された6桁の確認コードを入力します。") ?></p>
            <?=
            $this->Form->input('2fa_code',
                               ['label'                    => false,
                                'placeholder'              => __d('gl', "code"),
                                "data-bv-notempty-message" => __d('validate', "入力必須項目です。"),
                                'required'                 => true,
                               ]) ?>
            </div>
        </div>
        <div class="modal-footer">
            <?=
            $this->Form->submit(__d('gl', "保存"),
                                ['class' => 'btn btn-primary pull-right', 'div' => false,]) ?>
            <?= $this->Form->end(); ?>
        </div>
    </div>
</div>
<!-- END app/View/Elements/User/modal_2fa_register.ctp -->
