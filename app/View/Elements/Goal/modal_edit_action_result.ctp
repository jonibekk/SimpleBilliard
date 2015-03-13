<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 6/6/14
 * Time: 3:19 PM
 *
 * @var CodeCompletionView $this
 * @var                    $kr_list
 */
?>
<?
$action = $this->request->data;
?>
<!-- START app/View/Elements/Goal/modal_edit_action_result.ctp -->
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close font_33px close-design" data-dismiss="modal" aria-hidden="true"><span
                    class="close-icon">&times;</span></button>
            <h4 class="modal-title"><?= __d('gl', "アクションを変更") ?></h4>
        </div>
        <?= $this->Form->create('ActionResult', [
            'inputDefaults' => [
                'div'       => 'form-group mb_5px develop--font_normal',
                'wrapInput' => false,
                'class'     => 'form-control',
            ],
            'url'           => ['controller' => 'goals', 'action' => 'edit_action', $action['ActionResult']['id']],
            'type'          => 'file',
        ]); ?>
        <?= $this->Form->hidden('ActionResult.id') ?>
        <div class="modal-body modal-circle-body">
            <div class="col col-xxs-12">
                <?=
                $this->Form->input('ActionResult.name', [
                                                          'label'       => false,
                                                          'rows'        => 1,
                                                          'placeholder' => __d('gl', "今日やったアクションを共有しよう！"),
                                                          'class'       => 'form-control tiny-form-text blank-disable col-xxs-10 goalsCard-actionInput mb_12px',
                                                          'id'          => "ActionEditFormName_" . $action['ActionResult']['id'],
                                                          'target-id'   => "ActionEditFormSubmit_" . $action['ActionResult']['id'],
                                                      ]
                )
                ?>
                <div class="form-group">
                    <label class="font_normal col-xxs-4 lh_40px" for="ActionPhotos">
                        <i class="fa fa-camera mr_2px"></i><?= __d('gl', "画像") ?>
                    </label>

                    <div class="col-xxs-8">
                        <ul class="col input-images post-images">
                            <? for ($i = 1; $i <= 5; $i++): ?>
                                <li>
                                    <?= $this->element('Feed/photo_upload_mini',
                                                       ['data' => $action, 'type' => 'action_result', 'index' => $i, 'submit_id' => "ActionEditFormSubmit_" . $action['ActionResult']['id'], 'has_many' => false]) ?>
                                </li>
                            <? endfor ?>
                        </ul>
                        <span class="help-block"
                              id="ActionResult_<?= $action['ActionResult']['id'] ?>_Photo_ValidateMessage"></span>
                    </div>
                </div>
                <label class="font_normal col-xxs-4 lh_40px" for="KeyResults_<?= $action['ActionResult']['id'] ?>">
                    <i class="fa fa-key mr_2px"></i><?= __d('gl', "成果") ?>
                </label>
                <?=
                $this->Form->input('ActionResult.key_result_id', [
                                                                   'label'   => false, //__d('gl', "紐付ける出したい成果を選択(オプション)"),
                                                                   'options' => [null => __d('gl', "選択なし")] + $kr_list,
                                                                   'class'   => 'form-control col-xxs-8 selectKrForAction',
                                                                   'id'      => 'ActionKeyResultId_' . $action['ActionResult']['id'],
                                                               ]
                )
                ?>

            </div>
        </div>
        <div class="modal-footer">
            <?=
            $this->Form->submit(__d('gl', "アクションを変更"),
                                ['class' => 'btn btn-primary', 'div' => false, 'id' => "ActionEditFormSubmit_" . $action['ActionResult']['id']]) ?>
            <?= $this->Form->end() ?>

            <button type="button" class="btn btn-default" data-dismiss="modal"><?= __d('gl', "閉じる") ?></button>
            <?=
            $this->Form->postLink(__d('gl', "アクションを削除"),
                                  ['controller' => 'goals', 'action' => 'delete_action', $action['ActionResult']['id']],
                                  ['class' => 'btn btn-default pull-left'], __d('gl', "本当にこのアクションを削除しますか？")) ?>


        </div>
    </div>
</div>
<!-- END app/View/Elements/Goal/modal_edit_action_result.ctp -->
