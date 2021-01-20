<?= $this->App->viewStartComment()?>
<div class="panel panel-default col-sm-8 col-sm-offset-2 eval-list">
    <div class="panel-heading">
        <?= __('Evaluatees overview') ?>
    </div>
    <div class="panel-body eval-view-panel-body">
        <?php if ($countOfZeroEvaluateeUsers > 0): ?>
        <div class="col-sm-12 bg-danger font_bold p_8px mb_8px">
            <?= __('%d coached member has no evaluators.', $countOfZeroEvaluateeUsers) ?>
        </div>
        <?php endif ?>
        <div class="form-group">
            <div for="#" class="col col-xxs-12 eval-index-panel-title bg-lightGray p_8px mb_8px">
                <p class="font_bold"><?= __('You') ?></p>
            </div>
            <?= $this->element('EvaluatorSetting/index_items',
                [
                    'evaluatees'     => [$selfUser],
                    'isFixedEvaluationOrder'     => $isFixedEvaluationOrder,
                ]) ?>
            <div for="#" class="col col-xxs-12 eval-index-panel-title bg-lightGray p_8px mb_8px">
                <p class="font_bold"><?= __('Coached member(s)') ?> (<?= count($coachees) ?>)</p>
            </div>
            <?= $this->element('EvaluatorSetting/index_items',
                [
                    'evaluatees'     => $coachees,
                    'isFixedEvaluationOrder'     => $isFixedEvaluationOrder,
                ]) ?>
        </div>
    </div>
    <div class="panel-footer addteam_pannel-footer">
        <div class="row">
            <div class="team-button pull-right">
                <a class="btn btn-link design-cancel bd-radius_4px" data-dismiss="modal" href="<?= $this->Html->url(['controller'       => 'evaluations',
                                                                                                                     'action'           => 'index',
                ]) ?>">
                    <?= __('Back') ?>
                </a>
            </div>
        </div>
    </div>
</div>
<?= $this->App->viewEndComment()?>
