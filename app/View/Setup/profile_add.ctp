<div id="setup-guide-app">
    <div class="setup-container col col-sm-8 col-sm-offset-2 panel">
        <div class="setup-inner col col-xxs-offset-1 pb_8px pt_20px font_verydark">
            <!-- Setup guide header -->
            <div class="setup-pankuzu font_18px">
                <?= __("Set up Goalous") ?> < <?= __("Input your profile") ?>
            </div>

            <div id="profile-panel" class="panel">
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
                    'novalidate'    => true,
                    'type'          => 'file',
                    'id'            => 'ChangeProfileForm'
                ]); ?>
                <div class="panel-body profile-setting-panel-body">
                    <!--                    <div class="form-group">-->
                    <label for="" class="col control-label setup-profile-add-picture-title">
                        <?= __("Your profile picture") ?>
                    </label>

                    <div class="col setup-profile-add-picture-container">
                        <div class="fileinput fileinput-new" data-provides="fileinput">
                            <div
                                class="fileinput-preview thumbnail nailthumb-container setup-profile-add-picture-thumbnail"
                                data-trigger="fileinput"
                                style="width: 150px; height: 150px;">
                                <?=
                                $this->Upload->uploadImage($this->request->data, 'User.photo',
                                    ['style' => 'x_large']) ?>
                            </div>
                            <div>
                        <span class="btn btn-default btn-file setup-profile-add-picture-upload-btn">
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

                    <!--                    </div>-->
                    <div class="form-group">
                        <div class="col col-sm-3 control-label">
                            <label for="UserComment" class=""><?= __("Your self-info.") ?></label>
                        </div>
                        <div class="col">
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
                                    'data-bv-stringlength-message' => __("It's over limit characters (%s).",
                                        2000),
                                    'value'                        => (isset($this->request->data['TeamMember'][0]['comment']) && !empty($this->request->data['TeamMember'][0]['comment']))
                                        ? $this->request->data['TeamMember'][0]['comment']
                                        : __(
                                            "[What is that you can contribute to the team?]\n\n[What is the thing you want to achieve in the team?(Specifically)]\n\n[Others]\n\n")
                                ]
                            )
                            ?>
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
                <input class="setup-back-btn" value="<?= __("Back") ?>" type="submit">
                <?= $this->Form->submit(__("Submit"), ['class' => 'btn-primary setup-next-btn']) ?>
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
        <?= $this->App->viewEndComment()?>


    </div>
