<?
/**
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Pages
 * @since         CakePHP(tm) v 0.10.0.1076
 * @var CodeCompletionView $this
 * @var                    $selected_tab_term_id
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
        <div class="goal-search-menu">
            <div class="goal-term-search-menu btn-group btn-group-justified" role="group">
                <? foreach (['present' => __d('gl', "今期"), 'previous' => __d('gl', "前期")] as $key => $val): ?>
                    <? $selected = $key == $term_name ? 'selected' : ''; ?>
                    <?
                    $incompleteNum = (int)$incomplete_number_list[$key]['my_eval'] + (int)$incomplete_number_list[$key]['my_evaluatees'];
                    ?>
                    <a href="<?= $this->Html->url(['controller' => 'evaluations', 'action' => 'index', 'term' => $key]) ?>"
                       class="btn btn-default goal-search-elm <?= $selected ?>" role="button">
                        <? if ($incompleteNum > 0 && !$eval_is_frozen):
                            ?>
                            <div class="btn btn-danger btn-xs bell-notify-box" id="bellNum" style="position: absolute;
                                margin: 0 0 0 33px;
                                color: #fff;
                                font-size: 10px;
                                background-color:red!important;
                                display:block"><?= $incompleteNum ?></div>
                        <?
                        endif;
                        ?>
                        <?= $val ?>
                    </a>
                <? endforeach; ?>
            </div>
        </div>
        <? if($eval_is_frozen): ?>
            <div class="col-sm-12 bg-danger font_bold p_8px mb_8px">
                <?= __d('gl', "評価は凍結されています。") ?></div>
        <? else:?>
            <? if ((int)$incomplete_number_list[$term_name]['my_eval'] + (int)$incomplete_number_list[$term_name]['my_evaluatees'] > 0): ?>
                <div class="col-sm-12 bg-danger font_bold p_8px mb_8px">
                    <?= __d('gl', "あと%s件の評価が完了しておりません。以下より評価を行なってください。",
                            (int)$incomplete_number_list[$term_name]['my_eval'] + (int)$incomplete_number_list[$term_name]['my_evaluatees']) ?></div>
            <? endif; ?>
        <? endif; ?>
        <div class="form-group">
            <? if (!empty($my_eval[0])): ?>
                <div for="#" class="col col-xxs-12 eval-index-panel-title bg-lightGray p_8px mb_8px">
                    <p class="font_bold"><?= __d('gl', "自分") ?></p>
                    <? if ((int)$incomplete_number_list[$term_name]['my_eval'] > 0): ?>
                        <p><?= __d('gl', "未完了:1") ?></p>
                    <? endif; ?>
                </div>
                <?= $this->element('Evaluation/index_items',
                                   ['evaluatees' => $my_eval, 'eval_term_id' => $selected_tab_term_id, 'eval_is_frozen' => $eval_is_frozen]) ?>
            <? endif; ?>
            <? if (!empty($my_evaluatees)): ?>
                <div for="#" class="col col-xxs-12 eval-index-panel-title bg-lightGray p_8px mb_8px">
                    <p class="font_bold"><?= __d('gl', "あなたが評価するメンバー") ?></p>
                    <? if ((int)$incomplete_number_list[$term_name]['my_evaluatees'] > 0): ?>
                        <p><?= __d('gl', "未完了:%s", (int)$incomplete_number_list[$term_name]['my_evaluatees']) ?></p>
                    <? endif; ?>
                </div>
                <?= $this->element('Evaluation/index_items',
                                   ['evaluatees' => $my_evaluatees, 'eval_term_id' => $selected_tab_term_id, 'eval_is_frozen' => $eval_is_frozen]) ?>
            <? endif; ?>
        </div>
    </div>
</div>
<? $this->append('script') ?>
<? $this->end() ?>
<!-- END app/View/Evaluations/index.ctp -->
