<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 *
 * @var CodeCompletionView $this
 * @var                    $index
 * @var                    $id
 */
?>
<?= $this->App->viewStartComment()?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close font_33px close-design" data-dismiss="modal" aria-hidden="true"><span
                    class="close-icon">&times;</span></button>
            <h4 class="modal-title"><?= __("Comfirm to delete goal category") ?></h4>
        </div>
        <div class="modal-body">
            <div class="col col-xxs-12">
                <p><?= __("Even if you delete a goal category, there is no affection to the past data.") ?></p>

                <p><?= __("After deleting, you can't select it.") ?></p>

                <p><?= __("Do you really want to delete the goal category?") ?></p>
            </div>
        </div>
        <div class="modal-footer">
            <?=
            $this->Form->postLink(__("Delete"),
                ['controller' => 'teams', 'action' => 'to_inactive_goal_category', 'team_id' => $id],
                ['class' => 'btn btn-primary']) ?>
        </div>
    </div>
</div>
<?= $this->App->viewEndComment()?>
