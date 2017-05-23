<?php /**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 6/11/14
 * Time: 11:40 AM
 *
 * @var CodeCompletionView $this
 * @var                    $border_months_options
 * @var                    $start_term_month_options
 * @var                    $timezones
 */
?>
<?= $this->App->viewStartComment()?>
<div class="row">
    <div class="col-sm-8 col-sm-offset-2">
        <div class="panel panel-default">
            <div class="panel-heading"><?= __("Create a team.") ?></div>
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
                'id'            => 'addOtherTeam',
                'class'         => 'form-horizontal',
                'novalidate'    => true,
                'type'          => 'file'
            ]); ?>
            <div class="panel-body add-team-panel-body">
                <?=
                $this->Form->input('name',
                    [
                        'label'                        => __("Team Name"),
                        'placeholder'                  => __("eg. Team Goalous"),
                        "data-bv-notempty-message"     => __("Input is required."),
                        'data-bv-stringlength'         => 'true',
                        'data-bv-stringlength-max'     => 128,
                        'data-bv-stringlength-message' => __("It's over limit characters (%s).", 128),
                    ]) ?>
                <hr>
                <div class="form-group">
                    <label for="" class="col col-sm-3 control-label form-label"><?= __("Team Image") ?></label>

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
                $this->Form->input('type', [
                    'label'      => __("Plan"),
                    'type'       => 'select',
                    'options'    => Team::$TYPE,
                    'afterInput' => '<span class="help-block font_11px">'
                        .__("You can use Goalous free of charge until the summer of 2017.") // 同様の文言がteam/edit_basic_setting.ctp
                        // . __("フリープランは、５人までのチームで使えます。また、複数の機能制限がございます。")
                        // . '<br>'
                        // . __("このプランはチーム作成後にいつでも変更できます。")
                        . '</span>'
                ]) ?>
                <hr>
                <?=
                $this->Form->input('timezone', [
                    'label'   => __("Timezone"),
                    'type'    => 'select',
                    'options' => $timezones,
                    'value'   => $this->Session->read('Auth.User.timezone')
                ])
                ?>
                <?=
                $this->Form->input('next_start_ym', [
                    'label'                    => __("Current Term"),
                    'type'                     => 'select',
                    // help-block の文言があるので、エラーメッセージは表示しない
                    "data-bv-notempty-message" => __(" "),
                    'class'                    => 'form-control addteam_input-design addOtherTeam-current-term-form',
                    'options'                  => [null => __("Please select")]
                ]) ?>
                <?=
                $this->Form->input('border_months', [
                    'label'                    => __("Next Term"),
                    'type'                     => 'select',
                    "data-bv-notempty-message" => __("Please select"),
                    'class'                    => 'form-control addteam_input-design addOtherTeam-next-term-form',
                    'options'                  => [null => __("Please select")]
                ]) ?>
            </div>

            <div class="panel-footer addteam_pannel-footer">
                <div class="row">
                    <div class="team-button pull-right">
                        <?=
                        $this->Form->submit(__("Create a team"),
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

        $('#AddTeamForm').bootstrapValidator({
            live: 'enabled',
            fields: {
                "data[Team][photo]": {
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
<?= $this->App->viewEndComment()?>
