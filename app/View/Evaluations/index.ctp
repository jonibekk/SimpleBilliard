<?
/**
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Pages
 * @since         CakePHP(tm) v 0.10.0.1076
 * @var CodeCompletionView $this
 * @var                    $eval_term_id
 * @var                    $is_myself_evaluations_incomplete
 * @var                    $my_eval_status
 * @var                    $total_incomplete_count
 * @var                    $evaluatees
 * @var                    $total_incomplete_count_as_evaluator
 */
?>
<!-- START app/View/Evaluations/index.ctp -->
<div class="panel panel-default col-sm-8 col-sm-offset-2 clearfix">
    <div class="panel-heading"><?= __d('gl', "評価") ?></div>
    <div class="panel-body eval-view-panel-body">
        <? if ($total_incomplete_count > 0): ?>
            <div class="col-sm-12 bg-danger font_bold p_8px mb_8px">
                <?= __d('gl', "あと%s件の評価が完了しておりません。以下より評価を行なってください。", $total_incomplete_count) ?></div>
        <? endif; ?>
        <div class="form-group">
            <hr>
            <div for="#" class="col col-sm-12 eval-index-panel-title bg-lightGray p_8px mb_8px">
                <p class="font_bold"><?= __d('gl', "自分") ?></p>
                <? if ($is_myself_evaluations_incomplete): ?>
                    <p><?= __d('gl', "未完了:1") ?></p>
                <? endif; ?>
            </div>
            <a href="<?= $this->Html->url(['controller' => 'evaluations', 'action' => 'view', $eval_term_id, $this->Session->read('Auth.User.id')]) ?>"
               class="font_verydark">
                <div class="col-xxs-12 mb_8px">
                    <div class="disp_ib">
                        <?=
                        $this->Upload->uploadImage($this->Session->read('Auth'), 'User.photo', ['style' => 'medium'],
                                                   ['width' => '48px', 'height' => '48px', 'alt' => 'icon', 'class' => 'pull-left img-circle mtb_3px']) ?>
                    </div>
                    <div class="disp_ib ml_8px">
                        <p><?= $this->Session->read('Auth.User.display_username') ?></p>
                        <? foreach ($my_eval_status['flow'] as $k => $v): ?>
                            <? if ($k !== 0): ?>&nbsp;<i class="fa fa-long-arrow-right"></i>&nbsp;<? endif ?>
                            <span>
                                <? if ($v['my_tarn']): ?>
                                    <b><?= $v['name'] ?></b>
                                <? else: ?>
                                    <?= $v['name'] ?>
                                <? endif; ?>
                            </span>
                        <? endforeach ?>
                        <? if ($is_myself_evaluations_incomplete): ?>
                            <p class="font_brownRed"><?= __d('gl', "自己評価をしてください") ?></p>
                        <? endif; ?>
                    </div>
                </div>
            </a>
            <hr class="col-xxs-12">
            <div for="#" class="col col-sm-12 eval-index-panel-title bg-lightGray p_8px mb_8px">
                <p class="font_bold"><?= __d('gl', "あなたが評価するメンバー") ?></p>
                <? if ($total_incomplete_count_as_evaluator > 0): ?>
                    <p><?= __d('gl', "未完了:%s", $total_incomplete_count_as_evaluator) ?></p>
                <? endif; ?>
            </div>
            <? foreach ($evaluatees as $user): ?>
                <a href="<?= $this->Html->url(['controller' => 'evaluations', 'action' => 'view', $eval_term_id, $user['User']['id']]) ?>"
                   class="font_verydark">
                    <div class="col-xxs-12 mb_8px">
                        <div class="disp_ib">
                            <?=
                            $this->Upload->uploadImage($user, 'User.photo', ['style' => 'medium'],
                                                       ['width' => '48px', 'height' => '48px', 'alt' => 'icon', 'class' => 'pull-left img-circle mtb_3px']) ?>
                        </div>
                        <div class="disp_ib ml_8px">
                            <p class="font_bold"><?= h($user['User']['display_username']) ?></p>
                            <? foreach ($user['flow'] as $k => $v): ?>
                                <? if ($k !== 0): ?>&nbsp;<i class="fa fa-long-arrow-right"></i>&nbsp;<? endif ?>
                                <span>
                                <? if ($v['my_tarn']): ?>
                                    <? $my_tarn_name = $v['name'] ?>
                                    <b><?= $v['name'] ?></b>
                                <? else: ?>
                                    <?= $v['name'] ?>
                                <? endif; ?>
                            </span>
                            <? endforeach ?>
                            <? if (isset($my_tarn_name)): ?>
                                <p class="font_verydark"><?= __d('gl', "%sの評価待ち", $my_tarn_name) ?></p>
                            <? endif; ?>
                        </div>
                    </div>
                </a>
                <hr class="col-xxs-12">
            <? endforeach; ?>
        </div>
    </div>
</div>
<? $this->append('script') ?>
<? $this->end() ?>
<!-- END app/View/Evaluations/index.ctp -->
