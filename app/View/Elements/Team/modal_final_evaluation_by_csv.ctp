<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 6/6/14
 * Time: 3:19 PM
 *
 * @var CodeCompletionView $this
 * @var                    $my_member_status
 * @var                    $evaluate_term_id
 * @var                    $start
 * @var                    $end
 */
?>
<?= $this->App->viewStartComment()?>
<div class="modal fade" tabindex="-1" id="ModalFinalEvaluation_<?= $evaluate_term_id ?>_ByCsv">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close font_33px close-design" data-dismiss="modal" aria-hidden="true">
                    <span class="close-icon">&times;</span></button>
                <h4 class="modal-title"><?= __("Final evaluation (%s - %s)",
                        $this->TimeEx->date($start),
                        $this->TimeEx->date($end)) ?></h4>
            </div>
            <div class="modal-body">
                <?=
                $this->Form->create('Team', [
                    'url'           => [
                        'controller'       => 'teams',
                        'action'           => 'ajax_upload_final_evaluations_csv',
                        'evaluate_term_id' => $evaluate_term_id
                    ],
                    'inputDefaults' => [
                        'div'       => 'form-group',
                        'label'     => [
                            'class' => ''
                        ],
                        'wrapInput' => '',
                        'class'     => 'form-control'
                    ],
                    'novalidate'    => true,
                    'type'          => 'file',
                    'id'            => "FinalEvaluation_{$evaluate_term_id}_Form",
                    'loader-id'     => "FinalEvaluation_{$evaluate_term_id}_Loader",
                    'result-msg-id' => "FinalEvaluation_{$evaluate_term_id}_ResultMsg",
                    'submit-id'     => "FinalEvaluation_{$evaluate_term_id}_Submit",
                    'class'         => 'ajax-csv-upload',
                ]); ?>
                <div class="form-group">
                    <label class="">1. <?= __("Download evaluation data") ?></label>

                    <p><?= __("Download and edit csv without any changes in header.") ?></p>

                    <div class="">
                        <?=
                        $this->Html->link(__("Download evaluation data"),
                            ['action' => 'download_final_evaluations_csv', 'evaluate_term_id' => $evaluate_term_id],
                            ['class' => 'btn btn-default', 'div' => false])
                        ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="">2. <?= __('Upload the file.') ?></label>

                    <p><?= __("Upload edited evaluation data.") ?></p>

                    <div class="">
                        <div class="fileinput fileinput-new fileinput-enabled-submit" data-provides="fileinput"
                             submit-id="FinalEvaluation_<?= $evaluate_term_id ?>_Submit">
                            <span class="btn btn-default btn-file">
                                <span class="fileinput-new"><?= __("Choose a file.") ?></span>
                                <span class="fileinput-exists"><?= __("Change to another file.") ?></span>
                                <?=
                                $this->Form->input('csv_file',
                                    [
                                        'type'         => 'file',
                                        'label'        => false,
                                        'div'          => false,
                                        'css'          => false,
                                        'wrapInput'    => false,
                                        'errorMessage' => false,
                                        'accept'       => ".csv",
                                    ]) ?>
                            </span>
                            <span class="fileinput-filename"></span>
                            <a href="#" class="close fileinput-exists" data-dismiss="fileinput"
                               style="float: none">&times;</a>
                        </div>
                    </div>
                </div>
                <div id="FinalEvaluation_<?= $evaluate_term_id ?>_ResultMsg" class="none">
                    <div class="alert" role="alert">
                        <h4 class="alert-heading"></h4>
                        <span class="alert-msg"></span>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <div class="row">
                    <div class="col-sm-9 col-sm-offset-3">
                        <div id="FinalEvaluation_<?= $evaluate_term_id ?>_Loader" class="pull-right none">
                            &nbsp;<i class="fa fa-refresh fa-spin"></i>
                        </div>
                        <button type="button" class="btn btn-link design-cancel bd-radius_4px"
                                data-dismiss="modal"><?= __("Cancel") ?></button>
                        <?=
                        $this->Form->submit(__("Upload"),
                            ['class'    => 'btn btn-primary',
                             'div'      => false,
                             'disabled' => 'disabled',
                             'id'       => "FinalEvaluation_{$evaluate_term_id}_Submit"
                            ]) ?>

                    </div>
                </div>
            </div>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
<?= $this->App->viewEndComment()?>
