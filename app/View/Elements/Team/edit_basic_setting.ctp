<?php /**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 6/11/14
 * Time: 11:40 AM
 *
 * @var View $this
 * @var      $this CodeCompletionView
 * @var      $border_months_options
 * @var      $start_term_month_options
 */
?>
<!-- START app/View/Elements/Team/edit_basic_setting.ctp -->
<div class="panel panel-default">
    <div class="panel-heading"><?= __d('gl', "基本設定") ?></div>
    <?=
    $this->Form->create('Team', [
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
        'id'            => 'AddTeamForm',
        'url'           => ['action' => 'edit_team']
    ]); ?>
    <div class="panel-body add-team-panel-body">
        <?=
        $this->Form->input('name',
                           ['label'                    => __d('gl', "チーム名"),
                            'placeholder'              => __d('gl', "例) チームGoalous"),
                            "data-bv-notempty-message" => __d('validate', "入力必須項目です。"),
                           ]) ?>
        <hr>
        <div class="form-group">
            <label for="" class="col col-sm-3 control-label form-label"><?= __d('gl', "チーム画像") ?></label>

            <div class="col col-sm-6">
                <div class="fileinput_small fileinput-new" data-provides="fileinput">
                    <div class="fileinput-preview thumbnail nailthumb-container photo-design"
                         data-trigger="fileinput"
                         style="width: 96px; height: 96px; line-height:96px;">
                        <?=
                        $this->Upload->uploadImage($this->request->data, 'Team.photo',
                                                   ['style' => 'medium_large']) ?>
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
        $this->Form->input('type', [
            'label'      => __d('gl', "プラン"),
            'type'       => 'select',
            'options'    => Team::$TYPE,
            'afterInput' => '<span class="help-block font_11px">'
                . __d('gl', "フリープランは、５人までのチームで使えます。また、複数の機能制限がございます。")
                . '<br>'
                . __d('gl', "このプランはチーム作成後にいつでも変更できます。")
                . '</span>'
        ]) ?>
        <hr>
        <?=
        $this->Form->input('start_term_month', [
            'label'                    => __d('gl', "開始月"),
            'type'                     => 'select',
            "data-bv-notempty-message" => __d('validate', "選択してください。"),
            'options'                  => $start_term_month_options,
            'afterInput'               => '<span class="help-block font_11px">'
                . __d('gl', "基準となる期の開始月を選択して下さい。")
                . '</span>'
        ]) ?>
        <?=
        $this->Form->input('border_months', [
            'label'                    => __d('gl', "期間"),
            'type'                     => 'select',
            "data-bv-notempty-message" => __d('validate', "選択してください。"),
            'options'                  => $border_months_options
        ]) ?>
        <div class="form-group">
            <label class="col col-sm-3 control-label form-label"><?= __d('gl', "現在の期間") ?></label>

            <div class="col col-sm-6">
                <p class="form-control-static" id="CurrentTermStr">
                </p>
            </div>
        </div>
    </div>

    <div class="panel-footer addteam_pannel-footer">
        <div class="row">
            <div class="col-sm-9 col-sm-offset-3">
                <?=
                $this->Form->submit(__d('gl', "基本設定を更新"),
                                    ['class' => 'btn btn-primary display-inline', 'div' => false, 'disabled' => 'disabled']) ?>
            </div>
        </div>
    </div>
    <?= $this->Form->end(); ?>
</div>
<?php $this->append('script') ?>
<script type="text/javascript">
    $(document).ready(function () {

        $('[rel="tooltip"]').tooltip();

        $('#AddTeamForm').bootstrapValidator({
            live: 'enabled',
            feedbackIcons: {
                valid: 'fa fa-check',
                invalid: 'fa fa-times',
                validating: 'fa fa-refresh'
            },
            fields: {
                "data[Team][photo]": {
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
<!-- END app/View/Elements/Team/edit_basic_setting.ctp -->
