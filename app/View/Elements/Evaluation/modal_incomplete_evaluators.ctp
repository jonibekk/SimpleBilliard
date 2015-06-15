<?php
/**
 * Created by PhpStorm.
 * User: saeki
 * Date: 15/04/17
 * Time: 14:12
 *
 * @var $incomplete_evaluators
 */
?>
<!-- START app/View/Elements/Elements/modal_incomplete_evaluators.ctp -->
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close font_33px close-design" data-dismiss="modal" aria-hidden="true"><span
                    class="close-icon">&times;</span></button>
            <h4 class="modal-title"><?= __d('gl', "未完了の評価者(%s)", count($incomplete_evaluators)) ?></h4>
        </div>
        <div class="modal-body modal-feed-body">
            <?php if (!empty($incomplete_evaluators)): ?>
                <div class="row borderBottom">
                    <?php foreach ($incomplete_evaluators as $user): ?>
                        <?=
                        $this->element('Evaluation/incomplete_evaluator',
                                       ['user' => $user['User'], 'evaluate_term_id' => $term_id]) ?>
                    <?php endforeach ?>
                </div>
            <?php else: ?>
                <?= __d('gl', "全員の評価が完了しています。") ?>
            <?php endif ?>
        </div>
        <div class="modal-footer modal-feed-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?= __d('gl', "閉じる") ?></button>
        </div>
    </div>
</div>
<!-- END app/View/Elements/Elements/modal_incomplete_evaluators.ctp -->
