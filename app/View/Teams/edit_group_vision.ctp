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
<!-- START app/View/Teams/edit_group_vision.ctp -->
<div class="row">
    <div class="col-sm-8 col-sm-offset-2">
        <div class="panel panel-default">
            <div class="panel-heading"><?= __d('gl', "グループビジョンを更新") ?></div>
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
                $this->Form->input('group_id', [
                    'label'   => __d('gl', "グループ"),
                    'type'    => 'select',
                    'options' => $group_list,
                ]) ?>
                <hr>
                <?=
                $this->Form->input('name',
                                   ['label'                    => __d('gl', "グループビジョン名"),
                                    'placeholder'              => __d('gl', "例) イノベーションを起こす"),
                                    "data-bv-notempty-message" => __d('validate', "入力必須項目です。"),
                                    'rows'                     => 1,
                                   ]) ?>
                <hr>
                <div class="form-group">
                    <label for="" class="col col-sm-3 control-label form-label"><?= __d('gl', "画像") ?></label>

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
                                                'errorMessage' => false,
                                                'required'     => false
                                               ]) ?>
                        </span>
                                <span class="help-block font_11px inline-block"><?= __d('gl', '10MB以下') ?></span>
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
                <?=
                $this->Form->input('description', [
                    'label' => __d('gl', "説明"),
                    'type'  => 'text',
                    'rows'  => 1,
                ]) ?>
            </div>

            <div class="panel-footer addteam_pannel-footer">
                <div class="row">
                    <div class="team-button pull-right">
                        <?=
                        $this->Form->submit(__d('gl', "グループビジョンを更新する"),
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
            feedbackIcons: {
                valid: 'fa fa-check',
                invalid: 'fa fa-times',
                validating: 'fa fa-refresh'
            },
            fields: {
                "data[GroupVision][photo]": {
                    feedbackIcons: 'false',
                    validators: {
                        file: {
                            extension: 'jpeg,jpg,png,gif',
                            type: 'image/jpeg,image/png,image/gif',
                            maxSize: 10485760,   // 10mb
                            message: "<?=__d('validate', "10MB以下かつJPG、PNG、GIFのいずれかの形式を選択して下さい。")?>"
                        }
                    }
                }
            }
        });
    });
</script>
<?php $this->end() ?>
<!-- END app/View/Teams/edit_group_vision.ctp -->
