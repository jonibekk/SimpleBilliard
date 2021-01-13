
<?php?>
<?= $this->App->viewStartComment()?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close font_33px close-design" data-dismiss="modal" aria-hidden="true"><span
                    class="close-icon">&times;</span></button>
            <h4 class="modal-title font_18px font_bold">
                <?= __("There are unapproved Goals.") ?>
            </h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <p><?= __("There are %d unapproved Goals.", $countUnapprovedGoals) ?></p>
                <p><?= __("Unapproved Goals are not eligible for evaluation and do not appear on the evaluation page.") ?></p>
                <br>
                <p><?= __("Coach's approval is required. Please contact the coach or re-set the coach.") ?></p>
            </div>
            <div class="row">
                <a href="/terms/unapproved_goals?term_id=<?= $termId ?>" target="_blank">
                    <button class='btn btn-default'><?= __("Unapproved Goals list") ?></button>
                </a>
            </div>
            <br>
            <div class="row borderBottom">
                <div class="checkbox">
                    <input type="checkbox" id="ignore-unapproved-goals-checkbox" />
                    <label><?= __("Ignore it and start the evaluation") ?></label>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <div class="row">
                <div class="col-sm-9 col-sm-offset-3">
                    <button type="button" class="btn btn-link design-cancel bd-radius_4px" data-dismiss="modal">
                        <?= __("Cancel") ?>
                    </button>
                    <button id="ignore-unapproved-start-eval-btn" class="btn btn-primary" disabled="true" data-termId="<?= $termId ?>">
                        <?= __("Start evaluations") ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->App->viewEndComment()?>
