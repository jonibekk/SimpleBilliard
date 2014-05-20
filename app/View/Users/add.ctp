<?
/**
 * ユーザ登録画面
 *
*@var $this View
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
                        'wrapInput' => 'col col-md-9',
                        'class'     => 'form-control'
                    ],
                    'class'         => 'form-horizontal'
                ]); ?>
                <?
                //姓と名は言語によって表示順を変える
                $last_name = $this->Form->input('last_name', [
                    'label'       => __d('gl', "姓(ローマ字)"),
                    'placeholder' => __d('gl', "姓"),
                    'afterInput'  => '<span class="help-block">' . __d('gl', "例) Suzuki") . '</span>'
                ]);
                $first_name = $this->Form->input('first_name', [
                    'label'       => __d('gl', "名(ローマ字)"),
                    'placeholder' => __d('gl', '名'),
                    'afterInput'  => '<span class="help-block">' . __d('gl', "例) Hiroshi") . '</span>'
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
                $this->Form->input('Email.email', [
                    'label'       => __d('gl', "メール"),
                    'placeholder' => __d('gl', "hiroshi@example.com"),
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
                    'label'       => __d('gl', "パスワードを作成"),
                    'placeholder' => __d('gl', '8文字以上'),
                ])?>
                <?=
                $this->Form->input('password_confirm', [
                    'label' => __d('gl', "パスワードを再入力"),
                ])?>
                <?
                $tosLink = $this->Html->link(__d('gl', '利用規約'), '#modal-tos',
                                             ['class' => 'link', 'data-toggle' => "modal"]);

                $ppLink = $this->Html->link(__d('gl', 'プライバシーポリシー'), '#modal-pp',
                                            ['class' => 'link', 'data-toggle' => "modal"]);
                echo $this->Form->input('tos', [
                    'wrapInput' => 'col col-md-9 col-md-offset-3',
                    'type'      => 'checkbox',
                    'label'     => ['class' => null,
                                    'text'  => __d('gl', "Goalousの%sと%sに同意します。", $tosLink, $ppLink)],
                    'class'     => false,
                ]);
                ?>
                <div class="form-group">
                    <div class="col-md-9 col-md-offset-3">
                        <?= $this->Form->submit(__d('gl', "キャンセル"), ['class' => 'btn btn-default', 'div' => false]); ?>
                        <?= $this->Form->submit(__d('gl', "新規登録"), ['class' => 'btn btn-primary', 'div' => false]); ?>
                    </div>
                </div>
                <?= $this->Form->end(); ?>
            </div>
        </div>
    </div>
</div>
