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
<?php $action = $this->request->data;
?>
<?= $this->App->viewStartComment()?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close font_33px close-design" data-dismiss="modal" aria-hidden="true"><span
                    class="close-icon">&times;</span></button>
            <h4 class="modal-title"><?= __("Edit Action") ?></h4>
        </div>
        <?= $this->Form->create('ActionResult', [
            'inputDefaults' => [
                'div'       => 'form-group mb_5px develop--font_normal',
                'wrapInput' => false,
                'class'     => 'form-control',
            ],
            'url'           => [
                'controller'       => 'goals',
                'action'           => 'edit_action',
                'action_result_id' => $action['ActionResult']['id']
            ],
            'type'          => 'file',
        ]); ?>
        <?= $this->Form->hidden('ActionResult.id') ?>
        <div class="modal-body modal-circle-body">
            <div class="aaa">
                <?=
                $this->Form->input('ActionResult.name', [
                        'label'       => false,
                        'rows'        => 1,
                        'placeholder' => __("Let's share the actions that you've done today!"),
                        'class'       => 'form-control tiny-form-text blank-disable-and-undisable goalsCard-actionInput mb_12px',
                        'id'          => "ActionEditFormName_" . $action['ActionResult']['id'],
                        'target-id'   => "ActionEditFormSubmit_" . $action['ActionResult']['id'],
                    ]
                )
                ?>
                <div class="form-group">
                    <label class="font_normal lh_40px" for="ActionPhotos">
                        <i class="fa fa-camera mr_2px"></i><?= __("Images") ?>
                    </label>

                    <div class="bbb">
                        <ul class="col input-images post-images">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <li>
                                    <?= $this->element('Feed/photo_upload_mini',
                                        [
                                            'data'      => $action,
                                            'type'      => 'action_result',
                                            'index'     => $i,
                                            'submit_id' => "ActionEditFormSubmit_" . $action['ActionResult']['id'],
                                            'has_many'  => false
                                        ]) ?>
                                </li>
                            <?php endfor ?>
                        </ul>
                        <span class="help-block"
                              id="ActionResult_<?= $action['ActionResult']['id'] ?>_Photo_ValidateMessage"></span>
                    </div>
                </div>
                <label class="font_normal lh_40px" for="KeyResults_<?= $action['ActionResult']['id'] ?>">
                    <i class="fa fa-key mr_2px"></i><?= __("Results") ?>
                </label>
                <?=
                $this->Form->input('ActionResult.key_result_id', [
                        'label'   => false, //__("紐付ける達成要素を選択(オプション)"),
                        'options' => [null => __("Nothing")] + $kr_list,
                        'class'   => 'form-control selectKrForAction',
                        'id'      => 'ActionKeyResultId_' . $action['ActionResult']['id'],
                    ]
                )
                ?>

            </div>
        </div>
        <div class="modal-footer">
            <?=
            $this->Form->submit(__("Edit Action"),
                [
                    'class' => 'btn btn-primary',
                    'div'   => false,
                    'id'    => "ActionEditFormSubmit_" . $action['ActionResult']['id']
                ]) ?>
            <?= $this->Form->end() ?>

            <button type="button" class="btn btn-default" data-dismiss="modal"><?= __("Close") ?></button>
            <?=
            $this->Form->postLink(__("Delete the action"),
                [
                    'controller'       => 'goals',
                    'action'           => 'delete_action',
                    'action_result_id' => $action['ActionResult']['id']
                ],
                ['class' => 'btn btn-default pull-left'], __("Do you really want to delete this action?")) ?>


        </div>
    </div>
</div>
<?= $this->App->viewEndComment()?>
