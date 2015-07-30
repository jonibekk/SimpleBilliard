<?php
/**
 * @var $key_results
 * @var $incomplete_kr_count
 * @var $kr_can_edit
 */
?>
<?php if ($key_results): ?>
    <!-- START app/View/Elements/Goal/key_results.ctp -->
    <?php foreach ($key_results as $kr): ?>
        <div class="goal-detail-kr-card">
            <div class="goal-detail-kr-achieve-wrap">
            <!-- todo :  押したらKRの完了機能。classの変更
                未完了 -> 完了
                goal-detail-kr-achieve-yet -> goal-detail-kr-achieve-already
            -->
                <i class="fa-check-circle fa goal-detail-kr-achieve-yet"></i>
            </div>
            <div class="goal-detail-kr-cards-contents">
                <h4 class="goal-detail-kr-card-title"><?= h($kr['KeyResult']['name']) ?></h4>
                <?php if ($kr['KeyResult']['completed']): ?>
                    <?= __d('gl', 'クリア') ?>
                <?php endif ?>
                <div class="goal-detail-kr-score">
                    <i class="fa fa-bullseye"></i>
                    <?= h(round($kr['KeyResult']['start_value'],
                                1)) ?><?= h(KeyResult::$UNIT[$kr['KeyResult']['value_unit']]) ?> →
                    <?= h(round($kr['KeyResult']['target_value'],
                                1)) ?><?= h(KeyResult::$UNIT[$kr['KeyResult']['value_unit']]) ?>
                </div>
                <i class="fa fa-calendar"></i>
                <?= $this->Time->format('Y/m/d',
                                        $kr['KeyResult']['start_date'] + $this->Session->read('Auth.User.timezone') * 3600) ?>
                →
                <?= $this->Time->format('Y/m/d',
                                        $kr['KeyResult']['end_date'] + $this->Session->read('Auth.User.timezone') * 3600) ?>
                <div class="goal-detail-action-wrap">
                    <!-- todo アクション画像を読み込むようにお願いします  -->
                    <ul class="goal-detail-action">
                        <li class="goal-detail-action-list">
                            <a class="goal-detail-add-action modal-ajax-get-add-action"
                               href="<?= $this->Html->url(['controller' => 'goals', 'action' => 'ajax_get_add_action_modal', 'goal_id' => $goal['Goal']['id']]) ?>"><i
                                    class="fa fa-plus"></i>

                                <p class="goal-detail-add-action-text "><?= __d('gl', "アクション") ?></p>

                                <p class="goal-detail-add-action-text "><?= __d('gl', "追加") ?></p>
                            </a>
                        </li>
                        <li class="goal-detail-action-list">
                            <i class="fa-plus fa"></i>
                            <?= h($kr['KeyResult']['action_result_count']) ?>
                        </li>
                    </ul>
                </div>
            </div>

            <?php if (isset($kr_can_edit) && $kr_can_edit): ?>
                <?= $this->element('Goal/key_result_edit_button', ['kr' => $kr]) ?>
            <?php endif ?>

        </div>
    <?php endforeach ?>
    <!-- END app/View/Elements/Goal/key_results.ctp -->
<?php endif ?>
