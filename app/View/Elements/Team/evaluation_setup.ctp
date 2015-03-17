<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 6/30/14
 * Time: 9:59 AM
 *
 * @var CodeCompletionView $this
 * @var array              $my_teams
 * @var                    $term_start_date
 * @var                    $term_end_date
 */
?>
<!-- START app/View/Elements/Team/evaluation_setup.ctp -->
<div class="panel panel-default">
    <div class="panel-heading"><?= __d('gl', "評価設定") ?></div>
    <div class="panel-body form-horizontal">
        <div class="form-group">
            <label for="TeamName" class="col col-sm-3 control-label form-label"></label>

            <div class="col col-sm-6">
                <p class="form-control-static"><?= __d('gl', "このセクションでは、現在の評価設定に基づき、評価を開始できます。") ?></p>

                <p class="form-control-static"><?= __d('gl', "この設定は取り消すことができませんので気を付けてください。") ?></p>
            </div>
        </div>
        <div class="form-group">
            <label for="TeamName" class="col col-sm-3 control-label form-label"><?= __d('gl', "今期の期間") ?></label>

            <div class="col col-sm-6">
                <p class="form-control-static"><b><?= $this->TimeEx->date($term_start_date) ?>
                        - <?= $this->TimeEx->date($term_end_date) ?></b></p>
            </div>
        </div>
    </div>
    <div class="panel-footer">
        <div class="row">
            <div class="col-sm-9 col-sm-offset-3">
                <a href="#" class="btn btn-primary" data-toggle="modal"
                   data-target="#ModalEditMembersByCsv"><?= __d('gl', '今期の評価を開始する') ?></a>
            </div>
        </div>
    </div>
</div>
<!-- END app/View/Elements/Team/evaluation_setup.ctp -->
<? $this->start('modal') ?>
<?= $this->element('modal_add_members_by_csv') ?>
<?= $this->element('modal_edit_members_by_csv') ?>
<? $this->end() ?>
