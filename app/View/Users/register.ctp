<?
/**
 * ユーザ登録画面
 *
 * @var $this View
 * @var $last_first
 */
?>
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading"><?= __d('gl', "新しいアカウントを作成") ?></div>
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
                        'class'         => 'form-horizontal validate',
                        'novalidate'    => true
                    ]); ?>
                    <?
                    //姓と名は言語によって表示順を変える
                    $last_name = $this->Form->input('last_name', [
                        'label'                    => __d('gl', "姓(ローマ字)"),
                        'placeholder'              => __d('gl', "姓"),
                        "pattern"                  => '^[a-zA-Z]+$',
                        "data-bv-regexp-message"   => __d('validate', "アルファベットのみで入力してください。"),
                        "data-bv-notempty-message" => __d('validate', "入力必須項目です。"),
                        'afterInput'               => '<span class="help-block">' . __d('gl', "例) Suzuki") . '</span>'
                    ]);
                    $first_name = $this->Form->input('first_name', [
                        'label'                    => __d('gl', "名(ローマ字)"),
                        'placeholder'              => __d('gl', '名'),
                        "pattern"                  => '^[a-zA-Z]+$',
                        "data-bv-regexp-message"   => __d('validate', "アルファベットのみで入力してください。"),
                        "data-bv-notempty-message" => __d('validate', "入力必須項目です。"),
                        'afterInput'               => '<span class="help-block">' . __d('gl', "例) Hiroshi") . '</span>'
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
                    <?=
                    $this->Form->input('Email.0.email', [
                        'label'                        => __d('gl', "メール"),
                        'placeholder'                  => __d('gl', "hiroshi@example.com"),
                        'data-bv-emailaddress-message' => __d('validate', "メールアドレスが正しくありません。"),
                        "data-bv-notempty-message"     => __d('validate', "入力必須項目です。"),
                    ])?>
                    <?=
                    $this->Form->input('update_email_flg', [
                        'wrapInput' => 'col col-md-9 col-md-offset-3',
                        'label'     => ['class' => null, 'text' => __d('gl', "Goalousからのメールによるニュースや更新情報などを受け取る。")],
                        'class'     => false,
                        'default'   => true,
                    ])?>
                    <?=
                    $this->Form->input('password', [
                        'label'                    => __d('gl', "パスワードを作成"),
                        'placeholder'              => __d('gl', '8文字以上'),
                        "data-bv-notempty-message" => __d('validate', "入力必須項目です。"),
                        'type'                     => 'password',
                    ])?>
                    <?=
                    $this->Form->input('password_confirm', [
                        'label'                    => __d('gl', "パスワードを再入力"),
                        "data-bv-notempty-message" => __d('validate', "入力必須項目です。"),
                        'type'                     => 'password',
                    ])?>
                    <?
                    $tosLink = $this->Html->link(__d('gl', '利用規約'), '#modal-tos',
                                                 ['class' => 'link', 'data-toggle' => "modal"]);

                    $ppLink = $this->Html->link(__d('gl', 'プライバシーポリシー'), '#modal-pp',
                                                ['class' => 'link', 'data-toggle' => "modal"]);
                    echo $this->Form->input('agree_tos', [
                        'wrapInput'                => 'col col-md-9 col-md-offset-3',
                        'type'                     => 'checkbox',
                        'label'                    => ['class' => null,
                                                       'text'  => __d('gl', "Goalousの%sと%sに同意します。", $tosLink, $ppLink)],
                        'data-bv-notempty'         => true,
                        "data-bv-notempty-message" => __d('validate', "利用規約に同意してください。"),
                        'class'                    => false,
                    ]);
                    ?>
                    <hr>
                    <div class="form-group">
                        <div class="col-md-9 col-md-offset-3">
                            <?= $this->Form->submit(__d('gl', "新規登録"), ['class' => 'btn btn-primary']) ?>
                        </div>
                    </div>
                    <?= $this->Form->end(); ?>
                </div>
            </div>
        </div>
    </div>
<?= $this->element('modal_tos') ?>
<?= $this->element('modal_privacy_policy') ?>