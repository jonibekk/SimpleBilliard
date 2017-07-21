<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 6/30/14
 * Time: 9:59 AM
 *
 * @var CodeCompletionView $this
 * @var array              $my_teams
 */
?>
<?= $this->App->viewStartComment()?>
<section class="panel panel-default">
    <header>
        <h2><?= __("Batch Update") ?></h2>
    </header>
    <div class="panel-body">
        <div class="form-group">
            <label for="TeamName" class="col col-sm-3 control-label form-label"></label>

            <div class="col col-sm-6">
                <?php if ($this->Session->read('ua.device_type') == 'Desktop'): ?>
                    <p class="form-control-static"><?= __("Managed update of team members by CSV.") ?></p>

                    <p class="form-control-static">
                        <?= __("Download CSV. After editing, upload it.") ?>
                    </p>

                    <p class="form-control-static">
                        <?= __("Existing accounts will be updated.") ?>
                    </p>

                    <p class="form-control-static"><?= __("") ?></p>

                    <p class="form-control-static"><?= __("") ?></p>

                    <p class="form-control-static"><?= __("") ?></p>

                    <p class="form-control-static"><?= __("") ?></p>
                <?php else: ?>
                    <p class="form-control-static"><?= __("This function can be used only by PC.") ?></p>
                <?php endif; ?>
            </div>

        </div>
    </div>
    <?php if ($this->Session->read('ua.device_type') == 'Desktop'): ?>
        <footer>
            <a href="#" class="btn btn-default" data-toggle="modal"
                data-target="#ModalEditMembersByCsv"><?= __('Update members information') ?></a>
        </footer>
    <?php endif; ?>
</section>
<?= $this->App->viewEndComment()?>
<?php $this->start('modal') ?>
<?= $this->element('modal_edit_members_by_csv') ?>
<?php $this->end() ?>
