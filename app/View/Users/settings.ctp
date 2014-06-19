<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 6/18/14
 * Time: 5:40 PM
 *
 * @var CodeCompletionView $this
 */
?>
    <div class="row">
    <div class="col-xs-3 hidden-xxs">
        <div class="gl-sidebar-setting" role="complementary">
            <ul class="nav">
                <li class="active"><a href="#account"><?= __d('gl', "アカウント") ?></a></li>
                <li class=""><a href="#profile"><?= __d('gl', "プロフィール") ?></a></li>
                <li class=""><a href="#notification"><?= __d('gl', "通知") ?></a></li>
                <li class=""><a href="#link"><?= __d('gl', "リンク") ?></a></li>
            </ul>
        </div>
    </div>
    <div class="col-xs-9 col-xxs-12 gl-sections" role="main">
    <div id="account">
        <div class="panel panel-default">
            <div class="panel-heading"><?= __d('gl', "アカウント") ?></div>
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
                <?
                //姓と名は言語によって表示順を変える
                $last_name = $this->Form->input('last_name', [
                    'label'                    => __d('gl', "姓(ローマ字)"),
                    'placeholder'              => __d('gl', "姓"),
                    "pattern"                  => '^[a-zA-Z]+$',
                    "data-bv-regexp-message"   => __d('validate', "アルファベットのみで入力してください。"),
                    "data-bv-notempty-message" => __d('validate', "入力必須項目です。"),
                    'afterInput'               => '<span class="help-block">' . __d('gl',
                                                                                    "例) Suzuki") . '</span>'
                ]);
                $first_name = $this->Form->input('first_name', [
                    'label'                    => __d('gl', "名(ローマ字)"),
                    'placeholder'              => __d('gl', '名'),
                    "pattern"                  => '^[a-zA-Z]+$',
                    "data-bv-regexp-message"   => __d('validate', "アルファベットのみで入力してください。"),
                    "data-bv-notempty-message" => __d('validate', "入力必須項目です。"),
                    'afterInput'               => '<span class="help-block">' . __d('gl',
                                                                                    "例) Hiroshi") . '</span>'
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
                                                   'text'  => __d('gl', "Goalousの%sと%sに同意します。", $tosLink,
                                                                  $ppLink)],
                    'data-bv-notempty'         => true,
                    "data-bv-notempty-message" => __d('validate', "利用規約に同意してください。"),
                    'class'                    => false,
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
                    <div class="col-md-9 col-md-offset-3">
                        <?= $this->Form->submit(__d('gl', "新規登録"), ['class' => 'btn btn-primary']) ?>
                    </div>
                </div>
            </div>
            <?= $this->Form->end(); ?>
        </div>
    </div>
    <div id="profile">
        <div class="panel panel-default">
            <div class="panel-heading"><?= __d('gl', "プロフィールを入力してください") ?></div>
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
                    'novalidate'    => true,
                    'type'          => 'file',
                ]); ?>
                <?
                if (!$is_not_use_local_name) {
                    //ローカル名を使う国のみ表示
                    //姓と名は言語によって表示順を変える
                    $local_last_name = $this->Form->input('local_last_name', [
                        'label'       => __d('gl', "姓(母国語)"),
                        'placeholder' => __d('gl', "姓"),
                        'afterInput'  => '<span class="help-block">' . __d('gl', "例) 鈴木") . '</span>'
                    ]);
                    $local_first_name = $this->Form->input('local_first_name', [
                        'label'       => __d('gl', "名(母国語)"),
                        'placeholder' => __d('gl', '名'),
                        'afterInput'  => '<span class="help-block">' . __d('gl', "例) 太郎") . '</span>'
                    ]);
                    if ($me['last_first']) {
                        echo $local_last_name;
                        echo $local_first_name;
                    }
                    else {
                        echo $local_first_name;
                        echo $local_last_name;
                    }
                }
                ?>
                <?=
                $this->Form->input('gender_type',
                                   [
                                       'type'    => 'radio',
                                       'before'  => '<label class="col col-md-3 control-label">'
                                           . __d('gl', '性別') . '</label>',
                                       'legend'  => false,
                                       'options' => User::$TYPE_GENDER,
                                       'class'   => 'radio-inline'
                                   ])
                ?>
                <?=
                $this->Form
                    ->input('birth_day',
                            [
                                'monthNames' => [
                                    '01' => __d('gl', '1月'),
                                    '02' => __d('gl', '2月'),
                                    '03' => __d('gl', '3月'),
                                    '04' => __d('gl', '4月'),
                                    '05' => __d('gl', '5月'),
                                    '06' => __d('gl', '6月'),
                                    '07' => __d('gl', '7月'),
                                    '08' => __d('gl', '8月'),
                                    '09' => __d('gl', '9月'),
                                    '10' => __d('gl', '10月'),
                                    '11' => __d('gl', '11月'),
                                    '12' => __d('gl', '12月'),
                                ],
                                'class'      => 'form-control gl-inline-fix',
                                'label'      => __d('gl', '誕生日'),
                                'dateFormat' => 'YMD',
                                'empty'      => true,
                                'separator'  => ' / ',
                                'maxYear'    => date('Y'),
                                'minYear'    => '1910',
                                'wrapInput'  => 'col col-md-6 form-inline',
                            ]);
                ?>
                <?=
                $this->Form->input('hide_year_flg', [
                    'wrapInput' => 'col col-md-9 col-md-offset-3',
                    'type'      => 'checkbox',
                    'label'     => ['class' => null, 'text' => __d('gl', "生年を隠す。")],
                    'class'     => false,
                ])
                ?>
                <?=
                $this->Form->input('hometown', [
                    'label'      => __d('gl', "出身地"),
                    'afterInput' => '<span class="help-block">' . __d('gl', '例) 東京都') . '</span>'
                ]);
                ?>
                <div class="form-group">
                    <label for="" class="col col-md-3 control-label"><?= __d('gl', "プロフィール画像") ?></label>

                    <div class="col col-md-6">
                        <div class="fileinput fileinput-new" data-provides="fileinput">
                            <div class="fileinput-preview thumbnail nailthumb-container" data-trigger="fileinput"
                                 style="width: 150px; height: 150px;"></div>
                            <div>
                        <span class="btn btn-default btn-file">
                            <span class="fileinput-new">
                                <?=
                                __d('gl',
                                    "画像を選択") ?>
                            </span>
                            <span class="fileinput-exists"><?= __d('gl', "画像を再選択") ?></span>
                            <?=
                            $this->Form->input('photo',
                                               ['type'         => 'file',
                                                'label'        => false,
                                                'div'          => false,
                                                'css'          => false,
                                                'wrapInput'    => false,
                                                'errorMessage' => false
                                               ]) ?>
                        </span>
                            </div>
                        </div>
                        <span class="help-block"><?= __d('gl', '10MB以下') ?></span>

                        <div class="has-error">
                            <?=
                            $this->Form->error('photo', null,
                                               ['class' => 'help-block text-danger',
                                                'wrap'  => 'span'
                                               ]) ?>
                        </div>
                    </div>

                </div>
            </div>
            <div class="panel-footer">
                <div class="row">
                    <div class="col-md-9 col-md-offset-3">
                        <?=
                        $this->Form->submit(__d('gl', "プロフィールを登録"),
                                            ['class' => 'btn btn-primary', 'div' => false]) ?>
                        <?=
                        $this->Html->link(__d('gl', "スキップ"), ['controller' => 'teams', 'action' => 'add'],
                                          ['class' => 'btn btn-default', 'div' => false]) ?>
                        <?= $this->Form->end(); ?>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <div id="notification">
    </div>
    <div id="link">
    </div>
    </div>
    </div>
<? $this->append('script') ?>
    <script type="text/javascript">
        $(document).ready(function () {
            $('body').scrollspy({ target: '.gl-sidebar-setting' });
        });
    </script>
<? $this->end(); ?>