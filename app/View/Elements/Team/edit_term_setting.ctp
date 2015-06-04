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
<!-- START app/View/Elements/Team/edit_term_setting.ctp -->
<div class="panel panel-default">
    <div class="panel-heading"><?= __d('gl', "期間設定") ?></div>
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
        'id'            => 'EditTermForm',
        'url'           => ['action' => 'edit_term']
    ]); ?>
    <div class="panel-body add-team-panel-body">
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
                $this->Form->submit(__d('gl', "期間設定を更新"),
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

        $('#EditTermForm').bootstrapValidator({
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
<!-- END app/View/Elements/Team/edit_term_setting.ctp -->
