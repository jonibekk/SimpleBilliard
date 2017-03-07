<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 6/19/14
 * Time: 2:41 PM
 *
 * @var CodeCompletionView $this
 * @var boolean            $lastFirst
 * @var string             $language_name
 * @var array              $me
 * @var boolean            $is_not_use_local_name
 */
?>
<?= $this->App->viewStartComment() ?>
<div id="profile">
    <div class="panel panel-default">
        <div class="panel-heading"><?= __("Profile") ?></div>
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
                    'label'                        => __("Last Name(%s)", $language_name),
                    'placeholder'                  => __("eg. Jobs"),
                    'required'                     => false,
                    'data-bv-stringlength'         => 'true',
                    'data-bv-stringlength-max'     => 128,
                    'data-bv-stringlength-message' => __("It's over limit characters (%s).", 128),
                ]);
                $local_first_name = $this->Form->input('LocalName.0.first_name', [
                    'label'                        => __("First Name(%s)", $language_name),
                    'placeholder'                  => __("eg. Bruce"),
                    'required'                     => false,
                    'data-bv-stringlength'         => 'true',
                    'data-bv-stringlength-max'     => 128,
                    'data-bv-stringlength-message' => __("It's over limit characters (%s).", 128),
                ]);
                if ($me['User']['last_first']) {
                    echo $local_last_name;
                    echo $local_first_name;
                } else {
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
                'label'                        => __("Last Name"),
                'placeholder'                  => __("last name (eg. Smith)"),
                "pattern"                      => User::USER_NAME_REGEX,
                "data-bv-regexp-message"       => __("It includes restricted strings. Allowed characters are only alphanumeric, space and apostrophe."),
                "data-bv-notempty-message"     => __("Input is required."),
                'data-bv-stringlength'         => 'true',
                'data-bv-stringlength-max'     => 128,
                'data-bv-stringlength-message' => __("It's over limit characters (%s).", 128),
            ]);
            $first_name = $this->Form->input('first_name', [
                'label'                        => __("First Name"),
                'placeholder'                  => __("first name (eg. John)"),
                "pattern"                      => User::USER_NAME_REGEX,
                "data-bv-regexp-message"       => __("It includes restricted strings. Allowed characters are only alphanumeric, space and apostrophe."),
                "data-bv-notempty-message"     => __("Input is required."),
                'data-bv-stringlength'         => 'true',
                'data-bv-stringlength-max'     => 128,
                'data-bv-stringlength-message' => __("It's over limit characters (%s).", 128),
            ]);
            if ($lastFirst) {
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
                'label'     => ['class' => null, 'text' => __("Hide birth year")],
                'class'     => false,
            ])
            ?>
            <hr>
            <?=
            $this->Form->input('hometown', [
                'label'                        => __("Birthplace"),
                'placeholder'                  => __('eg. Tokyo'),
                'required'                     => false,
                'data-bv-stringlength'         => 'true',
                'data-bv-stringlength-max'     => 128,
                'data-bv-stringlength-message' => __("It's over limit characters (%s).", 128),
            ]);
            ?>
            <hr>
            <div class="form-group">
                <label for="" class="col col-sm-3 control-label form-label">
                    <?= __("Profile Image") ?>
                </label>

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
                                    'wrapInput'    => false,
                                    'errorMessage' => false,
                                    'required'     => false
                                ]) ?>
                        </span>
                            <span class="help-block inline-block font_11px"><?= __('Smaller than 10MB') ?></span>
                        </div>
                    </div>

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
            <hr>
            <div class="form-group">
                <label for="" class="col col-sm-3 control-label form-label">
                    <?= __("Cover Image") ?>
                </label>

                <div class="col col-sm-6">
                    <div class="fileinput_cover fileinput-new profile-setting-cover" data-provides="fileinput">
                        <?php $noCoverClass = empty($me['User']['cover_photo_file_name']) ? "mod-no-image" : ""; ?>
                        <div class="fileinput-preview profile-setting-cover-upload <?= $noCoverClass ?> mb_8px"
                             data-trigger="fileinput">
                            <?php if (empty($me['User']['cover_photo_file_name'])): ?>
                                <a href="#" class="profile-setting-cover-upload-frame">
                                    <span class="">+</span>
                                </a>
                            <?php else: ?>
                                <?=
                                $this->Upload->uploadImage($this->request->data, 'User.cover_photo',
                                    ['style' => 'medium', 'class' => 'profile-setting-cover-image']) ?>
                            <?php endif; ?>
                        </div>
                        <div>
                        <span class="btn btn-default btn-file">
                            <span class="fileinput-new">
                                <?= __("Select an image") ?>
                            </span>
                            <span class="fileinput-exists"><?= __("Reselect an image") ?></span>
                            <?=
                            $this->Form->input('cover_photo',
                                [
                                    'type'         => 'file',
                                    'label'        => false,
                                    'div'          => false,
                                    'css'          => false,
                                    'wrapInput'    => false,
                                    'errorMessage' => false,
                                    'required'     => false
                                ]) ?>
                        </span>
                            <span class="help-block inline-block font_11px"><?= __('Smaller than 10MB') ?>
                                &nbsp;<?= __('More than %d×%d pixel', 672, 378) ?></span>
                        </div>
                    </div>

                    <div class="has-error">
                        <?=
                        $this->Form->error('cover_photo', null,
                            [
                                'class' => 'help-block text-danger',
                                'wrap'  => 'span'
                            ]) ?>
                    </div>
                </div>

            </div>
            <hr>
            <div class="form-group">
                <div class="col col-sm-3 control-label form-label">
                    <label for="UserComment" class=""><?= __("About me") ?></label>

                    <div class="label-addiction"><?= __("It will be shared with members only in this team.") ?></div>
                </div>
                <div class="col col-sm-6">
                    <?php if (isset($this->request->data['TeamMember'][0]['id'])): ?>
                        <?= $this->Form->hidden('TeamMember.0.id',
                            ['value' => $this->request->data['TeamMember'][0]['id']]) ?>
                    <?php endif; ?>
                    <?=
                    $this->Form->input('TeamMember.0.comment',
                        [
                            'label'                        => false,
                            'div'                          => false,
                            'wrapInput'                    => false,
                            'css'                          => false,
                            'data-bv-stringlength'         => 'true',
                            'data-bv-stringlength-max'     => 2000,
                            'data-bv-stringlength-message' => __("It's over limit characters (%s).", 2000),
                            'value'                        => (isset($this->request->data['TeamMember'][0]['comment']) && !empty($this->request->data['TeamMember'][0]['comment']))
                                ? $this->request->data['TeamMember'][0]['comment']
                                : __(
                                    "[What is that you can contribute to the team?]\n\n[What is the thing you want to achieve in the team?(Specifically)]\n\n[Others]\n\n")
                        ]
                    )
                    ?>
                    <a href="#" class="target-show-this-del link-dark-gray" target-id="CommentHelp"><?= __(
                            "Show Examples") ?></a>
                    <span class="help-block inline-block font_11px" id="CommentHelp" style="display: none">
                    <?= __("[Now, What is that you can contribute to the team?]<br>\n
Consulting UX of your production.<br>\n
[What is the thing you want to achieve in the team?(Specifically)]<br>\n
Innovation methods - Remote collaboration - Creativity - UX at C-level - Holocracy<br>\n
[Others]<br>\n
Need New Customers?<br>\n
1. Be focused on culture above all else, get happy employees.<br>\n
2. Create a culture of innovation, team spirit, collaboration, creativity everywhere<br>\n
3. Simplify everything
") ?>
                </span>
                </div>
            </div>
        </div>
        <div class="panel-footer setting_pannel-footer">
            <?= $this->Form->submit(__("Save changes"), ['class' => 'btn btn-primary pull-right']) ?>
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
                },
                "data[User][password]": {
                    validators: {
                        stringLength: {
                            min: 8,
                            message: "<?=__('At least %2$d characters is required.', "", 8)?>"
                        }
                    }
                },
                "data[User][password_confirm]": {
                    validators: {
                        identical: {
                            field: "data[User][password]",
                            message: "<?=__("Both of passwords are not same.")?>"
                        }
                    }
                }
            }
        });
    });
</script>
<?php $this->end() ?>
<?= $this->App->viewEndComment() ?>
