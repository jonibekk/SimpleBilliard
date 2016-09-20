<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 6/6/14
 * Time: 3:19 PM
 *
 * @var CodeCompletionView $this
 * @var string             $email
 * @var string             $email_id
 */
?>
<?= $this->App->viewStartComment()?>
<div class="modal fade" tabindex="-1" id="modal_delete_email">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close font_33px close-design" data-dismiss="modal" aria-hidden="true">
                    <span class="close-icon">&times;</span></button>
                <h4 class="modal-title"><?= __("Confirm") ?></h4>
            </div>
            <div class="modal-body">
                <p><?= __("Do you really want to cancel changing your email address?", $email) ?></p>
            </div>
            <div class="modal-footer modal_pannel-footer">
                <button type="button" class="btn btn-link design-cancel mr_8px bd-radius_4px" data-dismiss="modal">
                    <?= __('Close') ?>
                </button>
                <?=
                $this->Form
                    ->postLink(__("Cancel"),
                        [
                            'controller' => 'emails',
                            'action'     => 'delete',
                            $email_id
                        ],
                        ['class' => 'btn btn-primary']); ?>
            </div>
        </div>
    </div>
</div>
<?= $this->App->viewEndComment()?>
