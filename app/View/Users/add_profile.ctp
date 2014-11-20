<?php
/**
 * @var View    $this
 * @var array   $me
 * @var boolean $is_not_use_local_name
 * @var string  $language_name
 */
?>
<!-- START app/View/Users/add_profile.ctp -->
<div class="row add-profile">
    <div class="col-sm-8 col-sm-offset-2">
        <div class="panel panel-default">
            <div class="panel-heading"><?= __d('gl', "プロフィールを入力してください") ?></div>
            <?=
            $this->Form->create('User', [
                'inputDefaults' => [
                    'div'       => 'form-group',
                    'label'     => [
                        'class' => 'col col-sm-3 control-label mr_5px'
                    ],
                    'wrapInput' => 'col col-sm-6',
                    'class'     => 'form-control'
                ],
                'class'         => 'form-horizontal',
                'novalidate'    => true,
                'type'          => 'file',
                'id'            => 'ProfileForm',
            ]); ?>
            <div class="panel-body">
                <?
                if (!$is_not_use_local_name) {
                    //ローカル名を使う国のみ表示
                    //姓と名は言語によって表示順を変える
                    $local_last_name = $this->Form->input('LocalName.0.last_name', [
                        'label'    => __d('gl', "姓(%s)", $language_name),
                        'placeholder' => __d('gl', "例) 鈴木"),
                        'required' => false,
                    ]);
                    $local_first_name = $this->Form->input('LocalName.0.first_name', [
                        'label'    => __d('gl', "名(%s)", $language_name),
                        'placeholder' => __d('gl', "例) 太郎"),
                        'required' => false,
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
                }
                ?>
                <?
                //姓と名は言語によって表示順を変える
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
                if ($me['last_first']) {
                    echo $last_name;
                    echo $first_name;
                }
                else {
                    echo $first_name;
                    echo $last_name;
                }
                ?>
                <hr>
                <?=
                $this->Form->input('gender_type',
                                   [
                                       'type'    => 'radio',
                                       'before' => '<label class="col col-sm-3 control-label mr_5px profile-radio-label">'
                                           . __d('gl', '性別') . '</label>',
                                       'legend'  => false,
                                       'options' => User::$TYPE_GENDER,
                                       'class'  => 'radio-inline profile-radio-inline'
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
                                'class'      => 'form-control inline-fix',
                                'label'      => __d('gl', '誕生日'),
                                'dateFormat' => 'YMD',
                                'empty'      => true,
                                'separator'  => ' / ',
                                'maxYear'    => date('Y'),
                                'minYear'    => '1910',
                                'wrapInput' => 'col col-sm-6 form-inline',
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
                    <label for="" class="col col-sm-3 control-label mr_5px"><?= __d('gl', "プロフィール画像") ?></label>

                    <div class="col col-sm-6">
                        <div class="fileinput fileinput-new" data-provides="fileinput">
                            <div class="fileinput-preview thumbnail nailthumb-container photo-design"
                                 data-trigger="fileinput"
                                 style="width: 150px; height: 150px;">
                                <i class="fa fa-plus photo-plus-large"></i>
                            </div>
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
                                                'style' => 'x_large',
                                                'wrapInput'    => false,
                                                'errorMessage' => false
                                               ]) ?>
                        </span>
                            </div>
                        </div>
                        <span class="help-block font_11px disp_ib"><?= __d('gl', '10MB以下') ?></span>

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
                        <?
                        //招待の場合のスキップはホーム、そうじゃない場合はチーム作成
                        $skip_link = isset($this->request->params['named']['invite_token']) ? "/" : ['controller' => 'teams', 'action' => 'add'];
                        echo $this->Html->link(__d('gl', "スキップ"), $skip_link,
                                               ['class' => 'btn btn-default', 'div' => false]);
                        ?>
                    </div>
                </div>
            </div>
            <?= $this->Form->end(); ?>
        </div>
    </div>
</div>
<? $this->append('script') ?>
<script type="text/javascript">
    $(document).ready(function () {
        $('#ProfileForm').bootstrapValidator({
            live: 'enabled',
            feedbackIcons: {
                valid: 'fa fa-check',
                invalid: 'fa fa-times',
                validating: 'fa fa-refresh'
            },
            fields: {
                "data[User][photo]": {
                    enabled: false
                }
            }
        });
    });
</script>
<? $this->end() ?>
<!-- END app/View/Users/add_profile.ctp -->
