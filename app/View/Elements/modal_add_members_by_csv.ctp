<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 6/6/14
 * Time: 3:19 PM
 *
 * @var View $this
 * @var      $my_member_status
 */
?>
<!-- START app/View/Elements/modal_add_members_by_csv.ctp -->
<div class="modal fade" tabindex="-1" id="ModalAddMembersByCsv">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close font_33px close-design" data-dismiss="modal" aria-hidden="true">
                    <span class="close-icon">&times;</span></button>
                <h4 class="modal-title"><?= __d('gl', "新しいメンバーを追加") ?></h4>
            </div>
            <div class="modal-body">
                <?=
                $this->Form->create('Team', [
                    'url'           => ['controller' => 'teams', 'action' => 'xxxxxxx'],
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
                    'id'            => 'AddMembersForm',
                ]); ?>
                <div class="form-group">
                    <label class=""><?= __d('gl', "1.テンプレートをダウンロード") ?></label>

                    <p><?= __d('gl', "CSVフォーマットのテンプレートをダウンロードしてください。テンプレートのヘッダーは変更しないでください。") ?></p>

                    <div class="">
                        <?=
                        $this->Html->link(__d('gl', "テンプレートをダウンロード"), ['action' => 'download_add_members_csv_format'],
                                          ['class' => 'btn btn-default', 'div' => false])
                        ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class=""><?= __d('gl', '2.ファイルのアップロード') ?></label>

                    <p><?= __d('gl', "新しいメンバーを追加したファイルをアップロードしてください。") ?></p>

                    <div class="">
                        <div class="fileinput fileinput-new" data-provides="fileinput">
                            <span class="btn btn-default btn-file">
                                <span class="fileinput-new"><?= __d('gl', "ファイルを選択") ?></span>
                                <span class="fileinput-exists"><?= __d('gl', "別のファイルに変更する") ?></span>
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
            </div>
            <div class="modal-footer">
                <div class="row">
                    <div class="col-sm-9 col-sm-offset-3">
                        <button type="button" class="btn btn-link design-cancel bd-radius_4px"
                                data-dismiss="modal"><?= __d('gl',
                                                             "キャンセル") ?></button>
                        <?=
                        $this->Form->submit(__d('gl', "追加する"),
                                            ['class' => 'btn btn-primary', 'div' => false, 'disabled' => 'disabled']) ?>

                    </div>
                </div>
            </div>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
<!-- END app/View/Elements/modal_add_members_by_csv.ctp -->
