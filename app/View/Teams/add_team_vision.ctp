<?php /**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 6/11/14
 * Time: 11:40 AM
 *
 * @var CodeCompletionView $this
 * @var                    $border_months_options
 * @var                    $start_term_month_options
 */
?>
<!-- START app/View/Teams/add_team_vision.ctp -->
<div class="row">
    <div class="col-sm-8 col-sm-offset-2">
        <div class="panel panel-default">
            <div class="panel-heading"><?= __("チームビジョンを作成してください") ?></div>
            <?=
            $this->Form->create('TeamVision', [
                'inputDefaults' => [
                    'div'       => 'form-group',
                    'label'     => [
                        'class' => 'col col-sm-3 control-label form-label'
                    ],
                    'wrapInput' => 'col col-sm-6',
                    'class'     => 'form-control addteam_input-design'
                ],
                'class'         => 'form-horizontal',
                'novalidate'    => true,
                'type'          => 'file',
                'id'            => 'AddTeamVisionForm',
            ]); ?>
            <div class="panel-body add-team-panel-body">
                <?=
                $this->Form->input('name',
                                   ['label'                        => __("チームビジョン名"),
                                    'placeholder'                  => __("例) イノベーションを起こす"),
                                    "data-bv-notempty-message"     => __("入力必須項目です。"),
                                    'rows'                         => 1,
                                    'data-bv-stringlength'         => 'true',
                                    'data-bv-stringlength-max'     => 200,
                                    'data-bv-stringlength-message' => __("最大文字数(%s)を超えています。", 200),
                                   ]) ?>
                <hr>
                <div class="form-group">
                    <label for="" class="col col-sm-3 control-label form-label"><?= __("Images") ?></label>

                    <div class="col col-sm-6">
                        <div class="fileinput_small fileinput-new" data-provides="fileinput">
                            <div class="fileinput-preview thumbnail nailthumb-container photo-design"
                                 data-trigger="fileinput"
                                 style="width: 96px; height: 96px; line-height:96px;">
                                <i class="fa fa-plus photo-plus-large"></i>
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
                                <span class="help-block font_11px inline-block"><?= __('10MB以下') ?></span>
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
                    'label'                        => __("説明"),
                    'type'                         => 'text',
                    'rows'                         => 1,
                    'data-bv-stringlength'         => 'true',
                    'data-bv-stringlength-max'     => 2000,
                    'data-bv-stringlength-message' => __("最大文字数(%s)を超えています。", 2000),
                ]) ?>
            </div>

            <div class="panel-footer addteam_pannel-footer">
                <div class="row">
                    <div class="team-button pull-right">
                        <?=
                        $this->Form->submit(__("チームビジョンを作成"),
                                            ['class' => 'btn btn-primary display-inline', 'div' => false, 'disabled' => 'disabled']) ?>
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

        $('#AddTeamVisionForm').bootstrapValidator({
            live: 'enabled',
            feedbackIcons: {
                valid: 'fa fa-check',
                invalid: 'fa fa-times',
                validating: 'fa fa-refresh'
            },
            fields: {
                "data[TeamVision][photo]": {
                    feedbackIcons: 'false',
                    validators: {
                        file: {
                            extension: 'jpeg,jpg,png,gif',
                            type: 'image/jpeg,image/png,image/gif',
                            maxSize: 10485760,   // 10mb
                            message: "<?=__("10MB以下かつJPG、PNG、GIFのいずれかの形式を選択して下さい。")?>"
                        }
                    }
                }
            }
        });
    });
</script>
<?php $this->end() ?>
<!-- END app/View/Teams/add_team_vision.ctp -->
