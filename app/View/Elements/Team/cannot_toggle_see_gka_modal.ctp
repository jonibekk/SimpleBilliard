<?php
?>
<?= $this->App->viewStartComment() ?>
<div class="modal fade" id="CannotToggleSeeGkaModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close font_33px close-design" data-dismiss="modal" aria-hidden="true"><span class="close-icon">&times;</span></button>
                <h4 class="modal-title"><?= __("Attention") ?></h4>
            </div>
            <div class="modal-body">
                <div class="col col-xxs-12">
                    <p>
                        <?= __("This check cannot be turned off because there is no valid group in this team.") ?>
                    </p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">
                    <?= __("OK") ?>
                </button>
            </div>
        </div>
    </div>
</div>
<?= $this->App->viewEndComment() ?>
