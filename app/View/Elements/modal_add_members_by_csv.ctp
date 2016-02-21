<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 6/6/14
 * Time: 3:19 PM
 *
 * @var CodeCompletionView $this
 * @var                    $my_member_status
 */
?>
<!-- START app/View/Elements/modal_add_members_by_csv.ctp -->
<div class="modal fade" tabindex="-1" id="ModalAddMembersByCsv">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close font_33px close-design" data-dismiss="modal" aria-hidden="true">
                    <span class="close-icon">&times;</span></button>
                <h4 class="modal-title"><?= __d('app', "新しいメンバーを追加") ?></h4>
            </div>
            <div class="modal-body">
                <?=
                $this->Form->create('Team', [
                    'url'           => ['controller' => 'teams', 'action' => 'ajax_upload_new_members_csv'],
                    'inputDefaults' => [
                        'div'       => 'form-group',
                        'label'     => [
                            'class' => ''
                        ],
                        'wrapInput' => '',
                        'class'     => 'form-control',
                    ],
                    'novalidate'    => true,
                    'type'          => 'file',
                    'id'            => 'AddMembersForm',
                    'loader-id'     => 'AddMembersLoader',
                    'result-msg-id' => 'AddMembersResultMsg',
                    'submit-id'     => 'AddMembersSubmit',
                    'class'         => 'ajax-csv-upload',
                ]); ?>
                <div class="form-group">
                    <label class=""><?= __d('app', "1.テンプレートをダウンロード") ?></label>

                    <p><?= __d('app', "CSVフォーマットのテンプレートをダウンロードしてください。テンプレートのヘッダーは変更しないでください。") ?></p>

                    <div class="">
                        <?=
                        $this->Html->link(__d('app', "テンプレートをダウンロード"), ['action' => 'download_add_members_csv_format'],
                                          ['class' => 'btn btn-default', 'div' => false])
                        ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class=""><?= __d('app', '2.ファイルのアップロード') ?></label>

                    <p><?= __d('app', "新しいメンバーを追加したファイルをアップロードしてください。") ?></p>

                    <div class="">
                        <div class="fileinput fileinput-new fileinput-enabled-submit" data-provides="fileinput"
                             submit-id="AddMembersSubmit">
                            <span class="btn btn-default btn-file">
                                <span class="fileinput-new"><?= __d('app', "ファイルを選択") ?></span>
                                <span class="fileinput-exists"><?= __d('app', "別のファイルに変更する") ?></span>
                                <?=
                                $this->Form->input('csv_file',
                                                   ['type'         => 'file',
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
                <div id="AddMembersResultMsg" class="none">
                    <div class="alert" role="alert">
                        <h4 class="alert-heading"></h4>
                        <span class="alert-msg"></span>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <div class="row">
                    <div class="col-sm-9 col-sm-offset-3">
                        <div id="AddMembersLoader" class="pull-right none">
                            &nbsp;<i class="fa fa-refresh fa-spin"></i>
                        </div>
                        <button type="button" class="btn btn-link design-cancel bd-radius_4px"
                                data-dismiss="modal"><?= __d('app',
                                                             "キャンセル") ?></button>
                        <?=
                        $this->Form->submit(__d('app', "追加する"),
                                            ['class' => 'btn btn-primary', 'div' => false, 'disabled' => 'disabled', 'id' => 'AddMembersSubmit']) ?>

                    </div>
                </div>
            </div>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
<!-- END app/View/Elements/modal_add_members_by_csv.ctp -->
