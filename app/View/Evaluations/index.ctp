<?php /**
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Pages
 * @since         CakePHP(tm) v 0.10.0.1076
 * @var CodeCompletionView $this
 * @var                    $selectedTabTermId
 * @var                    $myEval
 * @var                    $myEvaluatees
 * @var                    $selectedTermName
 * @var                    $incompleteNumberList
 * @var                    $isFrozens
 */
?>
<?= $this->App->viewStartComment()?>
<div class="panel panel-default col-sm-8 col-sm-offset-2 clearfix eval-list">
    <div class="panel-heading"><?= __("Evaluation") ?></div>
    <div class="panel-body eval-view-panel-body">
        <div class="goal-search-menu">
            <div class="eval-term-tab-menu btn-group btn-group-justified" role="group">
                <?php foreach (['present' => __("Current Term"), 'previous' => __("Previous Term")] as $key => $val): ?>
                    <?php $selected = $key == $selectedTermName ? 'selected' : ''; ?>
                    <?php $incompleteNum = (int)$incompleteNumberList[$key]['my_eval'] + (int)$incompleteNumberList[$key]['my_evaluatees'];
                    ?>
                    <a href="<?= $this->Html->url([
                        'controller' => 'evaluations',
                        'action'     => 'index',
                        'term'       => $key
                    ]) ?>"
                       class="btn btn-default eval-term-tab-elm <?= $selected ?>" role="button">
                        <?php if ($incompleteNum > 0 && !$isFrozens[$key]):
                            ?>
                            <div class="btn btn-xs bell-notify-box eval-term-numbers" id="bellNum"
                                 style="opacity:1;">
                                <span><?= $incompleteNum ?></span>
                            </div>
                        <?php endif;
                        ?>
                        <?= h($val) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php if ($isFrozens[$selectedTermName]): ?>
            <div class="col-sm-12 bg-danger font_bold p_8px mb_8px">
                <?= __("Evaluation is frozen.") ?></div>
        <?php else: ?>
            <?php if ((int)$incompleteNumberList[$selectedTermName]['my_eval'] + (int)$incompleteNumberList[$selectedTermName]['my_evaluatees'] > 0): ?>
                <div class="col-sm-12 bg-danger font_bold p_8px mb_8px">
                    <?= __("%s evaluations have not completed. Evaluate them from the following.",
                        (int)$incompleteNumberList[$selectedTermName]['my_eval'] + (int)$incompleteNumberList[$selectedTermName]['my_evaluatees']) ?></div>
            <?php endif; ?>
        <?php endif; ?>
        <div class="form-group">
            <?php if (!empty($myEval[0])): ?>
                <div for="#" class="col col-xxs-12 eval-index-panel-title bg-lightGray p_8px mb_8px">
                    <p class="font_bold"><?= __("his/her") ?></p>
                    <?php if ((int)$incompleteNumberList[$selectedTermName]['my_eval'] > 0): ?>
                        <p><?= __("Incomplete:1") ?></p>
                    <?php endif; ?>
                </div>
                <?= $this->element('Evaluation/index_items',
                    [
                        'evaluatees'     => $myEval,
                        'eval_term_id'   => $selectedTabTermId,
                        'eval_is_frozen' => $isFrozens[$selectedTermName]
                    ]) ?>
            <?php endif; ?>
            <?php if (!empty($myEvaluatees)): ?>
                <div for="#" class="col col-xxs-12 eval-index-panel-title bg-lightGray p_8px mb_8px">
                    <p class="font_bold"><?= __("The member who you evaluate") ?></p>
                    <?php if ((int)$incompleteNumberList[$selectedTermName]['my_evaluatees'] > 0): ?>
                        <p><?= __("Incomplete:%s",
                                (int)$incompleteNumberList[$selectedTermName]['my_evaluatees']) ?></p>
                    <?php endif; ?>
                </div>
                <?= $this->element('Evaluation/index_items',
                    [
                        'evaluatees'     => $myEvaluatees,
                        'eval_term_id'   => $selectedTabTermId,
                        'eval_is_frozen' => $isFrozens[$selectedTermName]
                    ]) ?>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $this->append('script') ?>
<?php $this->end() ?>
<?= $this->App->viewEndComment()?>
