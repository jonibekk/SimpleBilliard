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
                        'label'                        => __d('gl', "メール"),
                        'placeholder'                  => __d('gl', "hiroshi@example.com"),
                        'data-bv-emailaddress-message' => __d('validate', "メールアドレスが正しくありません。"),
                        "data-bv-notempty-message"     => __d('validate', "入力必須項目です。"),
                        "data-bv-blank"                => "true",
                        "data-bv-onsuccess"            => "bvEmailOnSuccess",
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

    // 'email' の bootstrap validate が成功した後に呼ばれ、サーバ側の validation を行う
    var bvEmailTimer = null;
    var bvEmailResults = {};
    var bvEmailOnSuccess = function (e, data) {
        var field = data.element[0].name;
        var email = data.element[0].value;

        // 既にチェック済の場合
        if (bvEmailResults[email] !== undefined) {
            data.bv.updateMessage(field, "blank", bvEmailResults[email]["message"]);
            if (bvEmailResults[email]["status"] == data.bv.STATUS_INVALID) {
                data.bv.updateStatus(field, bvEmailResults[email]["status"], "blank");
            }
            return;
        }

        // キー連打考慮して時間差実行
        clearTimeout(bvEmailTimer);
        bvEmailTimer = setTimeout(function () {
            // 読込み中のメッセージ表示
            data.bv.updateMessage(field, "blank",
                '<i class="fa fa-refresh fa-spin mr_8px"></i><?= __d('validate', "メールアドレス確認中...") ?>');
            data.bv.updateStatus(field, data.bv.STATUS_INVALID, "blank");

            $.ajax({
                type: 'GET',
                url: cake.url.validate_email,
                data: {
                    email: email
                }
            })
                .done(function (res) {
                    if (res.valid) {
                        bvEmailResults[email] = {
                            status: data.bv.STATUS_VALID,
                            message: " "
                        };
                    }
                    else {
                        bvEmailResults[email] = {
                            status: data.bv.STATUS_INVALID,
                            message: res.message
                        };
                    }
                })
                .fail(function () {
                    bvEmailResults[email] = {
                        status: data.bv.STATUS_INVALID,
                        message: cake.message.notice.d
                    };
                })
                .always(function () {
                    // ajax レスポンスが返ってきた時点で 他のエラーが出ていなければメッセージ更新する
                    if ($(data.element[0]).parent()
                            .find('.help-block[data-bv-for="' + field + '"]:visible')
                            .not("[data-bv-validator=blank]").size() == 0) {
                        data.bv.updateMessage(field, "blank", bvEmailResults[email]["message"]);
                        data.bv.updateStatus(field, bvEmailResults[email]["status"], "blank");
                    }
                    // 他のエラーが出ている場合、読込み中メッセージを消す
                    else {
                        data.bv.updateMessage(field, "blank", " ");
                    }
                });
        }, 300);
    };
</script>
<?php $this->end(); ?>
<!-- END app/View/Users/register.ctp -->
