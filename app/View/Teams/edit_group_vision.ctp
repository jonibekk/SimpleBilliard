<?php /**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 6/11/14
 * Time: 11:40 AM
 *
 * @var CodeCompletionView $this
 * @var                    $border_months_options
 * @var                    $start_term_month_options
 * @var                    $group_list
 */
?>
<?= $this->App->viewStartComment() ?>
<div class="row">
    <div class="col-sm-8 col-sm-offset-2">
        <div class="panel panel-default">
            <div class="panel-heading"><?= __("Update a group vision") ?></div>
            <?=
            $this->Form->create('GroupVision', [
                'inputDefaults' => [
                    'div'       => 'form-group',
                    'label'     => [
                        'class' => 'col col-sm-3 control-label form-label'
                    ],
                    'wrapInput' => 'col col-sm-6 team-group-vision-edit-group-select-edit',
                    'class'     => 'form-control addteam_input-design'
                ],
                'class'         => 'form-horizontal',
                'novalidate'    => true,
                'type'          => 'file',
                'id'            => 'AddGroupVisionForm',
            ]); ?>
            <?= $this->Form->hidden('id') ?>
            <div class="panel-body add-team-panel-body">
                <?=
                $this->Form->input('name',
                    [
                        'label'                        => __("Group vision name"),
                        'placeholder'                  => __("eg. making an innovation"),
                        "data-bv-notempty-message"     => __("Input is required."),
                        'rows'                         => 1,
                        'data-bv-stringlength'         => 'true',
                        'data-bv-stringlength-max'     => 200,
                        'data-bv-stringlength-message' => __("It's over limit characters (%s).", 200),
                    ]) ?>
                <hr>
                <div class="form-group">
                    <label for="" class="col col-sm-3 control-label form-label"><?= __("Images") ?></label>

                    <div class="col col-sm-6">
                        <div class="fileinput_small fileinput-new" data-provides="fileinput">
                            <div class="fileinput-preview thumbnail nailthumb-container photo-design"
                                 data-trigger="fileinput"
                                 style="width: 96px; height: 96px; line-height:96px;">
                                <?=
                                $this->Upload->uploadImage($this->request->data, 'GroupVision.photo',
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
                                <span class="help-block font_11px inline-block"><?= __('Smaller than 10MB') ?></span>
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
                <?=
                $this->Form->input('description', [
                    'label'                        => __("Description"),
                    'type'                         => 'text',
                    'rows'                         => 1,
                    'data-bv-stringlength'         => 'true',
                    'data-bv-stringlength-max'     => 2000,
                    'data-bv-stringlength-message' => __("It's over limit characters (%s).", 2000),
                ]) ?>
            </div>

            <div class="panel-footer addteam_pannel-footer">
                <div class="row">
                    <div class="team-button pull-right">
                        <?=
                        $this->Form->submit(__("Update a group vision"),
                            ['class' => 'btn btn-primary display-inline', 'div' => false]) ?>
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

        $('[rel="tooltip"]').tooltip();

        $('#AddGroupVisionForm').bootstrapValidator({
            live: 'enabled',
            fields: {
                "data[GroupVision][photo]": {
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
