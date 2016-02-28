<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 6/19/14
 * Time: 2:41 PM
 *
 * @var CodeCompletionView $this
 * @var boolean            $last_first
 * @var string             $language_name
 * @var array              $me
 * @var boolean            $is_not_use_local_name
 */
?>
<!-- START app/View/Elements/User/profile_setting.ctp -->
<div id="profile">
    <div class="panel panel-default">
        <div class="panel-heading"><?= __("プロフィール") ?></div>
        <?=
        $this->Form->create('User', [
            'inputDefaults' => [
                'div'       => 'form-group',
                'label'     => [
                    'class' => 'col col-sm-3 control-label form-label'
                ],
                'wrapInput' => 'col col-sm-6',
                'class'     => 'form-control setting_input-design'
            ],
            'class'         => 'form-horizontal',
            'novalidate'    => true,
            'type'          => 'file',
            'id'            => 'ChangeProfileForm'
        ]); ?>
        <div class="panel-body profile-setting-panel-body">
            <?php if (!$is_not_use_local_name) {
                //ローカル名を使う国のみ表示
                //姓と名は言語によって表示順を変える
                $local_last_name = $this->Form->input('LocalName.0.last_name', [
                    'label'                        => __("姓(%s)", $language_name),
                    'placeholder'                  => __("例) 鈴木"),
                    'required'                     => false,
                    'data-bv-stringlength'         => 'true',
                    'data-bv-stringlength-max'     => 128,
                    'data-bv-stringlength-message' => __("最大文字数(%s)を超えています。", 128),
                ]);
                $local_first_name = $this->Form->input('LocalName.0.first_name', [
                    'label'                        => __("名(%s)", $language_name),
                    'placeholder'                  => __("例) 太郎"),
                    'required'                     => false,
                    'data-bv-stringlength'         => 'true',
                    'data-bv-stringlength-max'     => 128,
                    'data-bv-stringlength-message' => __("最大文字数(%s)を超えています。", 128),
                ]);
                if ($me['User']['last_first']) {
                    echo $local_last_name;
                    echo $local_first_name;
                }
                else {
                    echo $local_first_name;
                    echo $local_last_name;
                }
                echo $this->Form->hidden('LocalName.0.language',
                                         ['value' => $this->request->data['User']['language']]);
                echo "<hr>";
            }
            ?>
            <?php //姓と名は言語によって表示順を変える
            $last_name = $this->Form->input('last_name', [
                'label'                        => __("姓(ローマ字)"),
                'placeholder'                  => __("例) Suzuki"),
                "pattern"                      => '^[a-zA-Z]+$',
                "data-bv-regexp-message"       => __("アルファベットのみで入力してください。"),
                "data-bv-notempty-message"     => __("入力必須項目です。"),
                'data-bv-stringlength'         => 'true',
                'data-bv-stringlength-max'     => 128,
                'data-bv-stringlength-message' => __("最大文字数(%s)を超えています。", 128),
            ]);
            $first_name = $this->Form->input('first_name', [
                'label'                        => __("名(ローマ字)"),
                'placeholder'                  => __("例) Hiroshi"),
                "pattern"                      => '^[a-zA-Z]+$',
                "data-bv-regexp-message"       => __("アルファベットのみで入力してください。"),
                "data-bv-notempty-message"     => __("入力必須項目です。"),
                'data-bv-stringlength'         => 'true',
                'data-bv-stringlength-max'     => 128,
                'data-bv-stringlength-message' => __("最大文字数(%s)を超えています。", 128),
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
            <?=
            $this->Form->input('gender_type',
                               [
                                   'type'    => 'radio',
                                   'before'  => '<label class="col col-sm-3 control-label form-label">'
                                       . __('Gender') . '</label>',
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
                                '01' => __('1月'),
                                '02' => __('2月'),
                                '03' => __('3月'),
                                '04' => __('4月'),
                                '05' => __('5月'),
                                '06' => __('6月'),
                                '07' => __('7月'),
                                '08' => __('8月'),
                                '09' => __('9月'),
                                '10' => __('10月'),
                                '11' => __('11月'),
                                '12' => __('12月'),
                            ],
                            'class'      => 'form-control inline-fix setting_input-design',
                            'label'      => __('Birthday'),
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
                'label'     => ['class' => null, 'text' => __("生年を隠す。")],
                'class'     => false,
            ])
            ?>
            <hr>
            <?=
            $this->Form->input('hometown', [
                'label'                        => __("出身地"),
                'placeholder'                  => __('例) 東京都'),
                'required'                     => false,
                'data-bv-stringlength'         => 'true',
                'data-bv-stringlength-max'     => 128,
                'data-bv-stringlength-message' => __("最大文字数(%s)を超えています。", 128),
            ]);
            ?>
            <hr>
            <div class="form-group">
                <label for="" class="col col-sm-3 control-label form-label"><?= __("プロフィール画像") ?></label>

                <div class="col col-sm-6">
                    <div class="fileinput fileinput-new" data-provides="fileinput">
                        <div class="fileinput-preview thumbnail nailthumb-container" data-trigger="fileinput"
                             style="width: 150px; height: 150px;">
                            <?=
                            $this->Upload->uploadImage($this->request->data, 'User.photo',
                                                       ['style' => 'x_large']) ?>
                        </div>
                        <div>
                        <span class="btn btn-default btn-file">
                            <span class="fileinput-new">
                                <?=
                                __(
                                    "画像を選択") ?>
                            </span>
                            <span class="fileinput-exists"><?= __("画像を再選択") ?></span>
                            <?=
                            $this->Form->input('photo',
                                               ['type'         => 'file',
                                                'label'        => false,
                                                'div'          => false,
                                                'css'          => false,
                                                'wrapInput'    => false,
                                                'errorMessage' => false,
                                                'required'     => false
                                               ]) ?>
                        </span>
                            <span class="help-block inline-block font_11px"><?= __('10MB以下') ?></span>
                        </div>
                    </div>

                    <div class="has-error">
                        <?=
                        $this->Form->error('photo', null,
                                           ['class' => 'help-block text-danger',
                                            'wrap'  => 'span'
                                           ]) ?>
                    </div>
                </div>

            </div>
            <hr>
            <div class="form-group">
                <div class="col col-sm-3 control-label form-label">
                    <label for="UserComment" class=""><?= __("自己紹介") ?></label>

                    <div class="label-addiction"><?= __("チーム内限定で共有されます。") ?></div>
                </div>
                <div class="col col-sm-6">
                    <?php if (isset($this->request->data['TeamMember'][0]['id'])): ?>
                        <?= $this->Form->hidden('TeamMember.0.id',
                                                ['value' => $this->request->data['TeamMember'][0]['id']]) ?>
                    <?php endif; ?>
                    <?=
                    $this->Form->input('TeamMember.0.comment',
                                       ['label'                        => false,
                                        'div'                          => false,
                                        'wrapInput'                    => false,
                                        'css'                          => false,
                                        'data-bv-stringlength'         => 'true',
                                        'data-bv-stringlength-max'     => 2000,
                                        'data-bv-stringlength-message' => __("最大文字数(%s)を超えています。", 2000),
                                        'value'                        => (isset($this->request->data['TeamMember'][0]['comment']) && !empty($this->request->data['TeamMember'][0]['comment']))
                                            ? $this->request->data['TeamMember'][0]['comment']
                                            : __(
                                                  "【今、チームに貢献できることは？】\n\n【これからチームで実現してみたいことは？(具体的に)】\n\n【その他】\n\n")]
                    )
                    ?>
                    <a href="#" class="target-show-this-del link-dark-gray" target-id="CommentHelp"><?= __(
                                                                                                            "例文を表示") ?></a>
                <span class="help-block inline-block font_11px" id="CommentHelp" style="display: none">
                    <?= __("
■ 例文１（技術者向け）<br>
【今、チームに貢献できることは？】<br>
スリムな開発環境の構築。オートスケール環境の構築。Git支援。<br><br>
【これからチームで実現してみたいことは？(具体的に)】<br>
iOS,Androidで100万ダウンロードされるアプリを開発する。<br><br>
================================================<br>
■ 例文2（技術者以外向け）<br>
【今、チームに貢献できることは？】<br>
社内の風通しを良くする事。<br><br>
【これからチームで実現してみたいことは？(具体的に)】<br>
地域活性化に貢献できるサービスを作り社会貢献をする事。
<br>
") ?>
                </span>
                </div>
            </div>
        </div>
        <div class="panel-footer setting_pannel-footer">
            <?= $this->Form->submit(__("変更を保存"), ['class' => 'btn btn-primary pull-right']) ?>
            <div class="clearfix"></div>
        </div>
        <?= $this->Form->end(); ?>
    </div>
</div>
<?php $this->append('script') ?>
<script type="text/javascript">
    $(document).ready(function () {
        $('#ChangeProfileForm').bootstrapValidator({
            live: 'enabled',
            feedbackIcons: {
                valid: 'fa fa-check',
                invalid: 'fa fa-times',
                validating: 'fa fa-refresh'
            },
            fields: {
                "data[User][photo]": {
                    feedbackIcons: 'false',
                    validators: {
                        file: {
                            extension: 'jpeg,jpg,png,gif',
                            type: 'image/jpeg,image/png,image/gif',
                            maxSize: 10485760,   // 10mb
                            message: "<?=__("10MB以下かつJPG、PNG、GIFのいずれかの形式を選択して下さい。")?>"
                        }
                    }
                },
                "data[User][password]": {
                    validators: {
                        stringLength: {
                            min: 8,
                            message: "<?=__('%2$d文字以上で入力してください。',"",8)?>"
                        }
                    }
                },
                "data[User][password_confirm]": {
                    validators: {
                        identical: {
                            field: "data[User][password]",
                            message: "<?=__("パスワードが一致しません。")?>"
                        }
                    }
                }
            }
        });
    });
</script>
<?php $this->end() ?>
<!-- END app/View/Elements/User/profile_setting.ctp -->
