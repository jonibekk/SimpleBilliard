<?
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 6/11/14
 * Time: 11:40 AM
 *
 * @var View $this
 * @var      $this CodeCompletionView
 */
?>
<!-- START app/View/Teams/add.ctp -->
<div class="row">
    <div class="col-sm-8 col-sm-offset-2">
        <div class="panel panel-default">
            <div class="panel-heading"><?= __d('gl', "チームを作成してください") ?></div>
            <?=
            $this->Form->create('Team', [
                'inputDefaults' => [
                    'div'       => 'form-group',
                    'label'     => [
                        'class' => 'col col-sm-3 control-label'
                    ],
                    'wrapInput' => 'col col-sm-6',
                    'class'     => 'form-control addteam_input-design'
                ],
                'class'         => 'form-horizontal',
                'novalidate'    => true,
                'type'          => 'file',
                'id'            => 'AddTeamForm',
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
                    <label for="" class="col col-sm-3 control-label"><?= __d('gl', "チーム画像") ?></label>

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
                                                ''
                                               ]) ?>
                        </span>
                            </div>
                        </div>
                        <span class="help-block font_11px fileinput-limit_mb"><?= __d('gl', '10MB以下') ?></span>

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
                    'afterInput' => '<span class="help-block">' . __d('gl',
                                                                      "フリープランは、５人までのチームで使えます。また、複数の機能制限がございます。") . '<br>' . __d('gl',
                                                                                                                                "このプランはチーム作成後にいつでも変更できます。") . '</span>'
                ])?>
            </div>

            <div class="panel-footer addteam_pannel-footer">
                <div class="row">
                    <div class="team-button pull-right">
                        <?=
                        $this->Html->link(__d('gl', "スキップ"), "/",
                                          ['class' => 'btn btn-default design-cancel', 'div' => false]) ?>
                        <?=
                        $this->Form->submit(__d('gl', "チームを作成"),
                                            ['class' => 'btn btn-primary display-inline', 'div' => false, 'disabled' => 'disabled']) ?>
                    </div>
                </div>
            </div>
            <?= $this->Form->end(); ?>
        </div>
    </div>
</div>
<? $this->append('script') ?>
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
                    enabled: false
                }
            }
        });
    });
</script>
<? $this->end() ?>
<!-- END app/View/Teams/add.ctp -->
