<?php
/**
 * @var CodeCompletionView $this
 * @var array              $me
 * @var boolean            $is_not_use_local_name
 * @var string             $language_name
 */
?>
<?= $this->App->viewStartComment() ?>
<div class="row add-profile">
    <div class="col-sm-8 col-sm-offset-2">
        <div class="panel panel-default">
            <div class="panel-heading"><?= __("Enter your profile.") ?></div>
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
                <?php if (!$is_not_use_local_name) {
                    //ローカル名を使う国のみ表示
                    //姓と名は言語によって表示順を変える
                    $local_last_name = $this->Form->input('LocalName.0.last_name', [
                        'label'       => __("Last Name(%s)", $language_name),
                        'placeholder' => __("eg. Jobs"),
                        'required'    => false,
                    ]);
                    $local_first_name = $this->Form->input('LocalName.0.first_name', [
                        'label'       => __("First Name(%s)", $language_name),
                        'placeholder' => __("eg. Bruce"),
                        'required'    => false,
                    ]);
                    if ($me['last_first']) {
                        echo $local_last_name;
                        echo $local_first_name;
                    } else {
                        echo $local_first_name;
                        echo $local_last_name;
                    }
                    echo $this->Form->hidden('LocalName.0.language',
                        ['value' => $this->Session->read('Auth.User.language')]);
                }
                ?>
                <?php //姓と名は言語によって表示順を変える
                $last_name = $this->Form->input('last_name', [
                    'label'                    => __("Last Name"),
                    'placeholder'              => __("last name (eg. Smith)"),
                    "pattern"                  => User::USER_NAME_REGEX,
                    "data-bv-regexp-message"   => __("It includes restricted strings. Allowed characters are only alphanumeric, space and apostrophe."),
                    "data-bv-notempty-message" => __("Input is required."),
                ]);
                $first_name = $this->Form->input('first_name', [
                    'label'                    => __("First Name"),
                    'placeholder'              => __("first name (eg. John)"),
                    "pattern"                  => User::USER_NAME_REGEX,
                    "data-bv-regexp-message"   => __("It includes restricted strings. Allowed characters are only alphanumeric, space and apostrophe."),
                    "data-bv-notempty-message" => __("Input is required."),
                ]);
                if ($me['last_first']) {
                    echo $last_name;
                    echo $first_name;
                } else {
                    echo $first_name;
                    echo $last_name;
                }
                ?>
                <hr>
                <?=
                $this->Form->input('gender_type',
                    [
                        'type'    => 'radio',
                        'before'  => '<label class="col col-sm-3 control-label mr_5px profile-radio-label">'
                            . __('Gender') . '</label>',
                        'legend'  => false,
                        'options' => User::$TYPE_GENDER,
                        'class'   => 'radio-inline profile-radio-inline'
                    ])
                ?>
                <hr>
                <?=
                $this->Form
                    ->input('birth_day',
                        [
                            'monthNames' => [
                                '01' => __('Jan'),
                                '02' => __('Feb'),
                                '03' => __('Mar'),
                                '04' => __('Apr'),
                                '05' => __('May'),
                                '06' => __('Jun'),
                                '07' => __('Jul'),
                                '08' => __('Aug'),
                                '09' => __('Sep'),
                                '10' => __('Oct'),
                                '11' => __('Nov'),
                                '12' => __('Dec'),
                            ],
                            'class'      => 'form-control inline-fix',
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
                    'label'     => ['class' => null, 'text' => __("Hide birth year")],
                    'class'     => false,
                ])
                ?>
                <hr>
                <?=
                $this->Form->input('hometown', [
                    'label'       => __("Birthplace"),
                    'placeholder' => __('eg. Tokyo'),
                    'required'    => false,
                ]);
                ?>
                <hr>
                <div class="form-group">
                    <label for="" class="col col-sm-3 control-label mr_5px">
                        <?= __("Profile Image") ?>
                    </label>

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
                                <?= __("Select an image") ?>
                            </span>
                            <span class="fileinput-exists"><?= __("Reselect an image") ?></span>
                            <?=
                            $this->Form->input('photo',
                                [
                                    'type'         => 'file',
                                    'label'        => false,
                                    'div'          => false,
                                    'css'          => false,
                                    'style'        => 'x_large',
                                    'wrapInput'    => false,
                                    'errorMessage' => false,
                                    'required'     => false
                                ]) ?>
                        </span>
                            </div>
                        </div>
                        <span class="help-block font_11px inline-block"><?= __('Smaller than 10MB') ?></span>

                        <div class="has-error">
                            <?=
                            $this->Form->error('photo', null,
                                [
                                    'class' => 'help-block text-danger',
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
                        $this->Form->submit(__("Register the profile"),
                            ['class' => 'btn btn-primary', 'div' => false]) ?>
                        <?php //招待の場合のスキップはホーム、そうじゃない場合はチーム作成
                        $skip_link = isset($this->request->params['named']['invite_token']) ? "/" : [
                            'controller' => 'teams',
                            'action'     => 'add'
                        ];
                        echo $this->Html->link(__("Skip"), $skip_link,
                            ['class' => 'btn btn-default', 'div' => false]);
                        ?>
                    </div>
                </div>
            </div>
            <?= $this->Form->end(); ?>
        </div>
    </div>
</div>
<?php $this->append('script') ?>
<script type="text/javascript">
    $(document).ready(function () {
        $('#ProfileForm').bootstrapValidator({
            live: 'enabled',
            fields: {
                "data[User][photo]": {
                    validators: {
                        file: {
                            extension: 'jpeg,jpg,png,gif',
                            type: 'image/jpeg,image/png,image/gif',
                            maxSize: 10485760,   // 10mb
                            message: "<?=__("10MB or less, and Please select one of the formats of JPG or PNG and GIF.")?>"
                        }
                    }
                }
            }
        });
    });
</script>
<?php $this->end() ?>
<?= $this->App->viewEndComment() ?>
