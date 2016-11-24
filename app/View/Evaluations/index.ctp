<?= $this->App->viewStartComment()?>
<div class="panel panel-default col-sm-8 col-sm-offset-2 clearfix eval-list">
    <div class="panel-heading"><?= __("Evaluation") ?></div>
    <div class="panel-body eval-view-panel-body">
        <?php // 評価期間選択プルダウン ?>
        <?=
        $this->Form->input('term_id', [
            'label'        => false,
            'div'          => false,
            'required'     => true,
            'class'        => 'form-control disable-change-warning mb_12px',
            'id'           => 'SelectTerm',
            'options'      => $termLabels,
            'default'      => $termId,
        ])
        ?>

        <?php // 評価が開始されているか ?>
        <?php if (!$isStartedEvaluation): ?>
            <div class="col-sm-12 bg-danger font_bold p_8px mb_8px">
                <?= __("Evaluation has not started.") ?>
            </div>
        <?php else:?>
            <?php if ($isFrozen): ?>
                <div class="col-sm-12 bg-danger font_bold p_8px mb_8px">
                    <?= __("Evaluation is frozen.") ?>
                </div>
            <?php else: ?>
                <?php if ($incompSelfEvalCnt + $incompEvaluateeEvalCnt > 0): ?>
                    <div class="col-sm-12 bg-danger font_bold p_8px mb_8px">
                        <?= __("%s evaluations have not completed. Evaluate them from the following.",
                            $incompSelfEvalCnt + $incompEvaluateeEvalCnt) ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
            <div class="form-group">
                <?php if (!empty($selfEval)): ?>
                    <div for="#" class="col col-xxs-12 eval-index-panel-title bg-lightGray p_8px mb_8px">
                        <p class="font_bold"><?= __("his/her") ?></p>
                        <?php if ($incompSelfEvalCnt > 0): ?>
                            <p><?= __("Incomplete:1") ?></p>
                        <?php endif; ?>
                    </div>
                    <?= $this->element('Evaluation/index_items',
                        [
                            'evaluatees'     => [$selfEval],
                            'eval_term_id'   => $termId,
                            'eval_is_frozen' => $isFrozen
                        ]) ?>
                <?php endif; ?>
                <?php if (!empty($evaluateesEval)): ?>
                    <div for="#" class="col col-xxs-12 eval-index-panel-title bg-lightGray p_8px mb_8px">
                        <p class="font_bold"><?= __("The member who you evaluate") ?></p>
                        <?php if ($incompEvaluateeEvalCnt > 0): ?>
                            <p><?= __("Incomplete:%s",
                                    $incompEvaluateeEvalCnt) ?></p>
                        <?php endif; ?>
                    </div>
                    <?= $this->element('Evaluation/index_items',
                        [
                            'evaluatees'     => $evaluateesEval,
                            'eval_term_id'   => $termId,
                            'eval_is_frozen' => $isFrozen
                        ]) ?>
                <?php endif; ?>
            </div>
        <?php endif;?>
    </div>
</div>
<?php $this->append('script') ?>
<script type="text/javascript">
    $(document).ready(function () {
        $('#SelectTerm').change(function () {
            var term_id = $(this).val();
            location.href = "/evaluations?term_id="+term_id;
        });
    });
</script>
<?php $this->end() ?>
<?= $this->App->viewEndComment()?>
