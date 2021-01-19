<?php
?>
<?= $this->App->viewStartComment() ?>
<div class="modal fade" id="SeeGkaToggleOffModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close font_33px close-design" data-dismiss="modal" aria-hidden="true"><span class="close-icon">&times;</span></button>
                <h4 class="modal-title"><?= __("Attention") ?></h4>
            </div>
            <div class="modal-body">
                <div class="col col-xxs-12">
                    <p>
                        <?= __("This setting will set goals to only be visible to group members.") ?>
                    </p>
                    <p>
                        <?= __("Any goals created in the future will need to have a destination group set up for publication.") ?>
                    </p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= __("Cancel") ?></button>
                <?= $this->Form->submit(__('OK'), ['class' => 'btn btn-primary', 'div' => false]) ?>
            </div>
        </div>
    </div>
</div>
<?= $this->App->viewEndComment() ?>