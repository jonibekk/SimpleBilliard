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
<!-- START app/View/Elements/modal_edit_members_by_csv.ctp -->
<div class="modal fade" tabindex="-1" id="ModalEditMembersByCsv">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close font_33px close-design" data-dismiss="modal" aria-hidden="true">
                    <span class="close-icon">&times;</span></button>
                <h4 class="modal-title"><?= __d('gl', "メンバーの情報を変更") ?></h4>
            </div>
            <div class="modal-body">


            </div>
            <div class="modal-footer">
                <div class="row">
                    <div class="col-sm-9 col-sm-offset-3">
                        <button type="button" class="btn btn-link design-cancel bd-radius_4px"
                                data-dismiss="modal"><?= __d('gl',
                                                             "キャンセル") ?></button>
                        <?=
                        $this->Form->submit(__d('gl', "変更する"),
                                            ['class' => 'btn btn-primary', 'div' => false, 'disabled' => 'disabled']) ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- END app/View/Elements/modal_edit_members_by_csv.ctp -->
