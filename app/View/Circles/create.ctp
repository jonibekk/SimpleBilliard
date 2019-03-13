<?= $this->App->viewStartComment()?>
<div class="row" id="CircleAdd">
    <div class="col-sm-8 col-sm-offset-2">
        <div class="panel panel-default">
            <div class="panel-heading"><?= __("Create a circle") ?></div>
            <?=
            $this->Form->create('Circle', [
                'url'           => ['controller' => 'circles', 'action' => 'add'],
                'inputDefaults' => [
                    'div'       => 'form-group',
                    'label'     => [
                        'class' => 'circle-create-label'
                    ],
                    'wrapInput' => false,
                    'class'     => 'form-control modal_input-design',
                ],
                'class'         => 'form-horizontal',
                'type'          => 'file',
                'id'            => 'AddCircleForm',
            ]); ?>
            <div class="modal-body " style="max-height: none;">
                <?=
                $this->Form->input('name',
                    [
                        'label'                        => __("Circle name"),
                        'placeholder'                  => __("eg) the sales division"),
                        "data-bv-notempty-message"     => __("Input is required."),
                        'data-bv-stringlength'         => 'true',
                        'data-bv-stringlength-max'     => 128,
                        'data-bv-stringlength-message' => __("It's over limit characters (%s).", 128),
                    ]) ?>
                <div class="form-group">
                    <label class="circle-create-label"><?= __('Members') ?></label>

                    <div class="ddd">
                        <?=
                        $this->Form->hidden('members', [
                            'class'    => 'ajax_add_select2_members',
                            'value'    => null,
                            'style'    => "width: 100%",
                            'data-url' => $this->Html->url(
                                [
                                    'controller' => 'users',
                                    'action'     => 'ajax_select2_get_users',
                                    '?'          => ['with_group' => '1']
                                ])
                        ]) ?>
                        <?php $this->Form->unlockField('Circle.members') ?>
                        <span class="help-block font_11px"><?=
                            __("Administrators",
                                h($this->Session->read('Auth.User.display_username'))) ?></span>
                    </div>
                </div>
                <?php $privacy_option = Circle::$TYPE_PUBLIC;
                $privacy_option[Circle::TYPE_PUBLIC_ON] .= '<span class="help-block font_11px">' . __(
                        "Anyone can see the circle, its members and their posts.") . '</span>';
                $privacy_option[Circle::TYPE_PUBLIC_OFF] .= '<span class="help-block font_11px">' . __(
                        "Only members can find the circle and see posts.") . '</span>';
                ?>
                <?php echo $this->Form->input('public_flg', array(
                    'type'     => 'radio',
                    'before'   => '<label class="circle-create-label">' . __('Privacy') . '</label>',
                    'legend'   => false,
                    'class'    => false,
                    'options'  => $privacy_option,
                    'default'  => Circle::TYPE_PUBLIC_ON,
                    'required' => false
                )); ?>
                <div class="font_brownRed font_11px">
                    <?= __('You can\'t change this setting lator.') ?>
                </div>
                <?=
                $this->Form->input('description',
                    [
                        'label'                        => __("Circle Description"),
                        'placeholder'                  => __("eg) Let's share the latest information."),
                        'data-bv-notempty-message'     => __("Input is required."),
                        'data-bv-stringlength'         => 'true',
                        'data-bv-stringlength-max'     => 2000,
                        'data-bv-stringlength-message' => __("It's over limit characters (%s).", 2000),
                    ]) ?>
                <div class="form-group">
                    <label for="" class="circle-create-label"><?= __("Circle Image") ?></label>

                    <div class="ggg">
                        <div class="fileinput_small fileinput-new" data-provides="fileinput">
                            <div class="fileinput-preview thumbnail nailthumb-container photo-design"
                                 data-trigger="fileinput" style="width: 96px; height: 96px; line-height:96px;">
                                <i class="fa fa-plus photo-plus-large"></i>
                            </div>
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
                            <span class="help-block font_11px inline-block"><?= __('Smaller than 10MB') ?></span>
                        </div>
                    </div>
                    <div>

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

            <div class="modal-footer addcircle_pannel-footer">
                <div class="row">
                    <div class="team-button pull-right">
                        <a href="/" class="btn btn-link design-cancel">
                            <?= __("Cancel") ?>
                        </a>
                        <?=
                        $this->Form->submit(__("Create a circle"),
                            ['class' => 'btn btn-primary', 'div' => false]) ?>

                    </div>
                </div>
            </div>
            <?= $this->Form->end(); ?>
        </div>
    </div>
</div>

<?= $this->App->viewEndComment()?>
