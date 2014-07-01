<?php
/**
 * @var View    $this
 * @var array   $me
 * @var boolean $is_not_use_local_name
 * @var string  $language_name
 */
?>
<div class="row">
    <div class="col-sm-8 col-sm-offset-2">
        <div class="panel panel-default">
            <div class="panel-heading"><?= __d('gl', "プロフィールを入力してください") ?></div>
            <div class="panel-body">
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
                    'class'         => 'form-horizontal',
                    'novalidate'    => true,
                    'type'          => 'file',
                ]); ?>
                <?
                if (!$is_not_use_local_name) {
                    //ローカル名を使う国のみ表示
                    //姓と名は言語によって表示順を変える
                    $local_last_name = $this->Form->input('LocalName.0.last_name', [
                        'label'       => __d('gl', "姓(%s)", $language_name),
                        'placeholder' => __d('gl', "例) 鈴木"),
                    ]);
                    $local_first_name = $this->Form->input('LocalName.0.first_name', [
                        'label'       => __d('gl', "名(%s)", $language_name),
                        'placeholder' => __d('gl', "例) 太郎"),
                    ]);
                    if ($me['last_first']) {
                        echo $local_last_name;
                        echo $local_first_name;
                    }
                    else {
                        echo $local_first_name;
                        echo $local_last_name;
                    }
                    echo $this->Form->hidden('LocalName.0.language',
                                             ['value' => $this->Session->read('Auth.User.language')]);
//                    echo $this->Form->hidden('LocalName.0.user_id',
//                                             ['value' => $this->Session->read('Auth.User.id')]);
                    echo "<hr>";
                }
                ?>
                <?=
                $this->Form->input('gender_type',
                                   [
                                       'type'    => 'radio',
                                       'before'  => '<label class="col col-sm-3 control-label">'
                                           . __d('gl', '性別') . '</label>',
                                       'legend'  => false,
                                       'options' => User::$TYPE_GENDER,
                                       'class'   => 'radio-inline'
                                   ])
                ?>
                <hr>
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
                                'wrapInput'  => 'col col-sm-6 form-inline',
                            ]);
                ?>
                <?=
                $this->Form->input('hide_year_flg', [
                    'wrapInput' => 'col col-sm-9 col-sm-offset-3',
                    'type'      => 'checkbox',
                    'label'     => ['class' => null, 'text' => __d('gl', "生年を隠す。")],
                    'class'     => false,
                ])
                ?>
                <hr>
                <?=
                $this->Form->input('hometown', [
                    'label'       => __d('gl', "出身地"),
                    'placeholder' => __d('gl', '例) 東京都'),
                ]);
                ?>
                <hr>
                <div class="form-group">
                    <label for="" class="col col-sm-3 control-label"><?= __d('gl', "プロフィール画像") ?></label>

                    <div class="col col-sm-6">
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
                    <div class="col-sm-9 col-sm-offset-3">
                        <?= $this->Form->hidden('id', ['value' => $this->Session->read('Auth.User.id')]) ?>
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
</div>
