<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 6/6/14
 * Time: 3:19 PM
 *
 * @var CodeCompletionView $this
 * @var                    $goal
 * @var                    $priority_list
 */
?>
<?= $this->App->viewStartComment() ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close font_33px close-design" data-dismiss="modal" aria-hidden="true">
                <span class="close-icon">&times;</span></button>
            <h4 class="modal-title"><?= empty($goal['MyCollabo']) ? __("Collab") : __("Edit Collab") ?></h4>
        </div>
        <?php $goalMemberId = isset($goal['MyCollabo'][0]['id']) ? $goal['MyCollabo'][0]['id'] : null ?>
        <?=
        $this->Form->create('GoalMember', [
            'url'           => ['controller' => 'goals', 'action' => 'edit_collabo', 'goal_member_id' => $goalMemberId],
            'inputDefaults' => [
                'div'       => 'form-group',
                'label'     => [
                    'class' => 'col col-sm-3 control-label pr_5px'
                ],
                'wrapInput' => 'col col-sm-7',
                'class'     => 'form-control modal_input-design'
            ],
            'class'         => 'form-horizontal',
            'novalidate'    => true,
            'id'            => 'CollaboEditForm',
        ]); ?>
        <?= $this->Form->hidden('goal_id', ['value' => $goal['Goal']['id']]) ?>
        <?php if (isset($goal['MyCollabo'][0]['id'])) {
            echo $this->Form->hidden('id', ['value' => $goal['MyCollabo'][0]['id']]);
        }
        ?>
        <div class="modal-body">
            <?=
            $this->Form->input('role',
                [
                    'label'                        => __("Role"),
                    'placeholder'                  => __("eg) Increasing the number of users"),
                    "data-bv-notempty-message"     => __("Input is required."),
                    'data-bv-stringlength'         => 'true',
                    'data-bv-stringlength-max'     => 200,
                    'data-bv-stringlength-message' => __("It's over limit characters (%s).", 200),
                    'required'                     => true,
                    'rows'                         => 1,
                    'value'                        => isset($goal['MyCollabo'][0]['role']) ? $goal['MyCollabo'][0]['role'] : null,
                ]) ?>
            <hr>
            <?=
            $this->Form->input('description',
                [
                    'label'                        => __("Description"),
                    'placeholder'                  => __("eg) I will get the users by advertising."),
                    "data-bv-notempty-message"     => __("Input is required."),
                    'data-bv-stringlength'         => 'true',
                    'data-bv-stringlength-max'     => 2000,
                    'data-bv-stringlength-message' => __("It's over limit characters (%s).", 2000),
                    'required'                     => true,
                    'rows'                         => 1,
                    'value'                        => isset($goal['MyCollabo'][0]['description']) ? $goal['MyCollabo'][0]['description'] : null,
                ]) ?>
            <hr>
            <?php if ($approvalData['showApprove']):?>
                <?= $this->Form->input('is_wish_approval', [
                    'wrapInput' => "col col-sm-9 col-sm-offset-3",
                    'type'      => 'checkbox',
                    'label'     => [
                        'class' => null,
                        'text'  => __("Request goal approval")
                    ],
                    'checked'   => $approvalData['defaultChecked'],
                    'disabled'  => !$approvalData['canRequestApproval'],
                ]);
                ?>
                <?php if ($approvalData['pendingApproval']):?>
                    <div class="form-group">
                        <div class="col col-sm-9 col-sm-offset-3">
                            <?= __('Awaiting coach approval.') ?>
                        </div>
                    </div>
                <?php endif;?>
                <?php if ($approvalData['cannotRequestApprovalReason']):?>
                    <div class="form-group">
                        <div class="col col-sm-9 col-sm-offset-3">
                            <?= $approvalData['cannotRequestApprovalReason'] ?>
                        </div>
                    </div>
                <?php endif;?>
                <hr>
            <?php endif;?>
            <?=
            $this->Form->input('priority',
                [
                    'label'                    => __("Weight"),
                    "data-bv-notempty-message" => __("Input is required."),
                    'required'                 => true,
                    'type'                     => 'select',
                    'default'                  => 3,
                    'options'                  => $priority_list,
                    'value'                    => isset($goal['MyCollabo'][0]['priority']) ? $goal['MyCollabo'][0]['priority'] : null,
                ]) ?>

        </div>
        <div class="modal-footer">
            <div class="row">
                <div class="col-sm-9 col-sm-offset-3">
                    <button type="button" class="btn btn-link design-cancel bd-radius_4px" data-dismiss="modal">
                        <?= __("Cancel") ?>
                    </button>
                    <?=
                    $this->Form->submit(empty($goal['MyCollabo']) ? __("Collab") : __("Edit Collab"),
                        ['class' => 'btn btn-primary', 'div' => false, 'disabled' => 'disabled']) ?>
                    <?= $this->Form->end(); ?>
                    <?php if (!empty($goal['MyCollabo'])): ?>
                        <?=
                        $this->Form->postLink(__("Quit the collaboration"),
                            [
                                'controller'     => 'goals',
                                'action'         => 'delete_collabo',
                                'goal_member_id' => $goal['MyCollabo'][0]['id']
                            ],
                            ['class' => 'pull-left btn btn-link'],
                            __("Do you really want to quit the collaboration?")) ?>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->App->viewEndComment() ?>
