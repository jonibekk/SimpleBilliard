<?php
/**
 * @var View    $this
 * @var array   $me
 * @var boolean $is_not_use_local_name
 */
?>
<div class="row">
    <div class="col-md-8 col-md-offset-2">
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
                    'class' => 'form-horizontal',
                    'novalidate'    => true
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
                                'label'      => __d('global', '誕生日'),
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
            </div>
            <div class="panel-footer">
                <div class="row">
                    <div class="col-md-9 col-md-offset-3">
                        <?=
                        $this->Form->submit(__d('gl', "プロフィールを登録"),
                                            ['class' => 'btn btn-primary', 'div' => false]) ?>
                        <?=
                        $this->Html->link(__d('gl', "スキップ"), ['action' => 'add_profile'],
                                          ['class' => 'btn btn-default', 'div' => false]) ?>
                        <?= $this->Form->end(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
