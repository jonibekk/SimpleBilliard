<?
/**
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Pages
 * @since         CakePHP(tm) v 0.10.0.1076
 * @var CodeCompletionView $this
 * @var                    $eval_term_id
 * @var                    $my_eval
 * @var                    $my_evaluatees
 * @var                    $total_incomplete_count_my_eval
 * @var                    $total_incomplete_count_as_evaluator
 */
?>
<!-- START app/View/Evaluations/index.ctp -->
<div class="panel panel-default col-sm-8 col-sm-offset-2 clearfix">
    <div class="panel-heading"><?= __d('gl', "評価") ?></div>
    <div class="panel-body eval-view-panel-body">
        <? if ($total_incomplete_count_my_eval + $total_incomplete_count_as_evaluator > 0): ?>
            <div class="col-sm-12 bg-danger font_bold p_8px mb_8px">
                <?= __d('gl', "あと%s件の評価が完了しておりません。以下より評価を行なってください。",
                        $total_incomplete_count_my_eval + $total_incomplete_count_as_evaluator) ?></div>
        <? endif; ?>
        <div class="form-group">
            <div for="#" class="col col-sm-12 eval-index-panel-title bg-lightGray p_8px mb_8px">
                <p class="font_bold"><?= __d('gl', "自分") ?></p>
                <? if ($total_incomplete_count_my_eval > 0): ?>
                    <p><?= __d('gl', "未完了:1") ?></p>
                <? endif; ?>
            </div>
            <?= $this->element('Evaluation/index_items', ['evaluatees' => $my_eval, 'eval_term_id' => $eval_term_id]) ?>
            <div for="#" class="col col-sm-12 eval-index-panel-title bg-lightGray p_8px mb_8px">
                <p class="font_bold"><?= __d('gl', "あなたが評価するメンバー") ?></p>
                <? if ($total_incomplete_count_as_evaluator > 0): ?>
                    <p><?= __d('gl', "未完了:%s", $total_incomplete_count_as_evaluator) ?></p>
                <? endif; ?>
            </div>
            <?= $this->element('Evaluation/index_items',
                               ['evaluatees' => $my_evaluatees, 'eval_term_id' => $eval_term_id]) ?>
        </div>
    </div>
</div>
<? $this->append('script') ?>
<? $this->end() ?>
<!-- END app/View/Evaluations/index.ctp -->
