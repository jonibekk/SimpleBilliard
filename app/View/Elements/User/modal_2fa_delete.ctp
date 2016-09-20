<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 6/6/14
 * Time: 3:19 PM
 *
 * @var CodeCompletionView $this
 * @var                    $url_2fa
 */
?>
<?= $this->App->viewStartComment()?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close font_33px close-design" data-dismiss="modal" aria-hidden="true"><span
                    class="close-icon">&times;</span></button>
            <h4 class="modal-title"><?= __("2-Step Verification settings") ?></h4>
        </div>
        <div class="modal-body">
            <div class="form-group"><label for="" class="modal-label pr_12px"></label>

                <div class="aaa">
                    <p class="form-control-static"><?= __("2-Step Verification is enabled.") ?></p>

                    <p class="form-control-static"><?= __("Disable 2-Step Verification?") ?></p>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <?= $this->Form->postButton(__("Disable"), ['controller' => 'users', 'action' => 'delete_2fa'],
                ['class' => 'btn btn-primary pull-right', 'div' => false,]) ?>
        </div>
    </div>
</div>
<?= $this->App->viewEndComment()?>
