<?php /**
 * ユーザ登録画面
 *
 * @var CodeCompletionView $this
 * @var                    $last_first
 * @var                    $email
 */
?>
<!-- START app/View/Users/register.ctp -->
<div class="row">
    <div class="col-sm-8 col-sm-offset-2">
        <div class="panel panel-default">
            <div class="panel-heading"><?= __d('gl', "新しいアカウントを作成") ?></div>
            <?=
            $this->Form->create('User', [
                'inputDefaults' => [
                    'div'       => 'form-group',
                    'label'     => [
                        'class' => 'col col-sm-3 control-label form-label'
                    ],
                    'wrapInput' => 'col col-sm-6',
                    'class'     => 'form-control register_input-design'
                ],
                'class'         => 'form-horizontal validate',
                'novalidate'    => true
            ]); ?>
            <div class="panel-body register-panel-body">
                <?php //姓と名は言語によって表示順を変える
                $last_name = $this->Form->input('last_name', [
                    'label'                    => __d('gl', "姓(ローマ字)"),
                    'placeholder'              => __d('gl', "例) Suzuki"),
                    "pattern"                  => '^[a-zA-Z]+$',
                    "data-bv-regexp-message"   => __d('validate', "アルファベットのみで入力してください。"),
                    "data-bv-notempty-message" => __d('validate', "入力必須項目です。"),
                ]);
                $first_name = $this->Form->input('first_name', [
                    'label'                    => __d('gl', "名(ローマ字)"),
                    'placeholder'              => __d('gl', "例) Hiroshi"),
                    "pattern"                  => '^[a-zA-Z]+$',
                    "data-bv-regexp-message"   => __d('validate', "アルファベットのみで入力してください。"),
                    "data-bv-notempty-message" => __d('validate', "入力必須項目です。"),
                ]);
                if ($last_first) {
                    echo $last_name;
                    echo $first_name;
                }
                else {
                    echo $first_name;
                    echo $last_name;
                }
                ?>
                <hr>
                <?php if (isset($email)): ?>
                    <div class="form-group">
                        <label for="" class="col col-sm-3 control-label form-label"><?= __d('gl', "メール") ?></label>

                        <div class="col col-sm-6">
                            <p class="form-control-static"><?= h($email) ?></p>
                        </div>
                    </div>
                    <?=
                    $this->Form->hidden('Email.0.email', ['value' => $email]) ?>
                <?php else: ?>
                    <?=
                    $this->Form->input('Email.0.email', [
                        'label'                     => __d('gl', "メール"),
                        'placeholder'               => __d('gl', "hiroshi@example.com"),
                        "data-bv-notempty"          => "false",
                        'data-bv-emailaddress'      => "false",
                        "data-bv-callback"          => "true",
                        "data-bv-callback-message"  => " ",
                        "data-bv-callback-callback" => "bvCallbackEmail",
                    ]) ?>
                <?php endif; ?>

                <?=
                $this->Form->input('update_email_flg', [
                    'wrapInput' => 'col col-sm-9 col-sm-offset-3',
                    'label'     => ['class' => null, 'text' => __d('gl', "Goalousからのメールによるニュースや更新情報などを受け取る。")],
                    'class'     => false,
                    'default'   => true,
                ]) ?>
                <hr>
                <?=
                $this->Form->input('password', [
                    'label'                    => __d('gl', "パスワードを作成"),
                    'placeholder'              => __d('gl', '8文字以上'),
                    "data-bv-notempty-message" => __d('validate', "入力必須項目です。"),
                    'type'                     => 'password',
                ]) ?>
                <?=
                $this->Form->input('password_confirm', [
                    'label'                    => __d('gl', "パスワードを再入力"),
                    "data-bv-notempty-message" => __d('validate', "入力必須項目です。"),
                    'type'                     => 'password',
                ]) ?>
                <hr>
                <?php $tosLink = $this->Html->link(__d('gl', '利用規約'), '#modal-tos',
                                                   ['class' => 'link', 'data-toggle' => "modal"]);

                $ppLink = $this->Html->link(__d('gl', 'プライバシーポリシー'), '#modal-pp',
                                            ['class' => 'link', 'data-toggle' => "modal"]);
                echo $this->Form->input('agree_tos', [
                    'wrapInput' => 'col col-sm-9 col-sm-offset-3',
                    'type'      => 'checkbox',
                    'label'     => ['class' => null,
                                    'text'  => __d('gl', "Goalousの%sと%sに同意します。", $tosLink, $ppLink)],
                    'class'     => 'validate-checkbox'
                ]);
                //タイムゾーン設定の為のローカル時刻をセット
                echo $this->Form->input('local_date', [
                    'label' => false,
                    'div'   => false,
                    'style' => 'display:none;',
                    'id'    => 'InitLocalDate',
                ]);
                ?>
            </div>
            <div class="panel-footer">
                <div class="row">
                    <div class="col-sm-9 col-sm-offset-3">
                        <?= $this->Form->submit(__d('gl', "新規登録"),
                                                ['class' => 'btn btn-primary', 'disabled' => 'disabled']) ?>
                    </div>
                </div>
            </div>
            <?= $this->Form->end(); ?>
        </div>
    </div>
</div>
<?= $this->element('modal_tos') ?>
<?= $this->element('modal_privacy_policy') ?>
<?php $this->append('script'); ?>
<script type="text/javascript">
    $(document).ready(function () {
        //ユーザ登録時にローカル時間をセットする
        $('input#InitLocalDate').val(getLocalDate());
    });

    // 'email' の validation
    var bvCallbackEmailTimer = null;
    var bvCallbackEmailResults = {};
    var bvCallbackEmail = function (email, validator, $field) {
        var field = $field.attr('name');

        // 簡易チェックをして通ったものだけサーバ側で確認する
        var parts = email.split('@');
        if (!(parts.length >= 2 && parts[parts.length - 1].indexOf('.') != -1)) {
            validator.updateMessage(field, "callback", '<?= __d('validate', "メールアドレスが正しくありません。") ?>');
            return false;
        }

        // 既にサーバ側でチェック済の場合
        if (bvCallbackEmailResults[email] !== undefined) {
            validator.updateMessage(field, "callback", bvCallbackEmailResults[email]["message"]);
            return bvCallbackEmailResults[email]["status"] == validator.STATUS_VALID;
        }

        // キー連打考慮して時間差実行
        clearTimeout(bvCallbackEmailTimer);
        bvCallbackEmailTimer = setTimeout(function () {
            // 読込み中のメッセージ表示
            validator.updateMessage(field, "callback",
                '<i class="fa fa-refresh fa-spin mr_8px"></i><?= __d('validate', "メールアドレス確認中...") ?>');
            validator.updateStatus(field, validator.STATUS_INVALID, "callback");

            $.ajax({
                type: 'GET',
                url: cake.url.validate_email,
                data: {
                    email: email
                }
            })
                .done(function (res) {
                    if (res.valid) {
                        bvCallbackEmailResults[email] = {
                            status: validator.STATUS_VALID,
                            message: " "
                        };
                    }
                    else {
                        bvCallbackEmailResults[email] = {
                            status: validator.STATUS_INVALID,
                            message: res.message
                        };
                    }
                })
                .fail(function () {
                    bvCallbackEmailResults[email] = {
                        status: validator.STATUS_INVALID,
                        message: cake.message.notice.d
                    };
                })
                .always(function () {
                    validator.updateMessage(field, "callback", bvCallbackEmailResults[email]["message"]);
                    validator.updateStatus(field, bvCallbackEmailResults[email]["status"], "callback");
                });
        }, 300);
        return false;
    };
</script>
<?php $this->end(); ?>
<!-- END app/View/Users/register.ctp -->
