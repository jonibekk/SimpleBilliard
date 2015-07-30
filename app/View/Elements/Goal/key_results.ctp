<?php
/**
 * @var CodeCompletionView $this
 * @var                    $key_results
 * @var                    $incomplete_kr_count
 * @var                    $kr_can_edit
 */
?>
<?php if ($key_results): ?>
    <!-- START app/View/Elements/Goal/key_results.ctp -->
    <?php foreach ($key_results as $kr): ?>
        <div class="goal-detail-kr-card">
            <div class="goal-detail-kr-achieve-wrap">
                <?php if ($kr['KeyResult']['completed']): ?>
                    <?= $this->Form->postLink('<i class="fa-check-circle fa goal-detail-kr-achieve-already"></i>',
                                              ['controller' => 'goals', 'action' => 'incomplete_kr', 'key_result_id' => $kr['KeyResult']['id']],
                                              ['escape' => false, 'class' => 'no-line']) ?>
                <?php else: ?>
                    <?php //最後のKRの場合
                    if ($incomplete_kr_count === 1):?>
                        <a href="<?= $this->Html->url(['controller' => 'goals', 'action' => 'ajax_get_last_kr_confirm', 'key_result_id' => $kr['KeyResult']['id']]) ?>"
                           class="modal-ajax-get no-line">
                            <i class="fa-check-circle fa goal-detail-kr-achieve-yet"></i>
                        </a>
                    <?php else: ?>
                        <?=
                        $this->Form->create('Goal', [
                            'url'           => ['controller' => 'goals', 'action' => 'complete_kr', 'key_result_id' => $kr['KeyResult']['id']],
                            'inputDefaults' => [
                                'div'       => 'form-group',
                                'label'     => false,
                                'wrapInput' => '',
                            ],
                            'class'         => 'form-feed-notify',
                            'name'          => 'kr_achieve_' . $kr['KeyResult']['id'],
                            'id'            => 'kr_achieve_' . $kr['KeyResult']['id']
                        ]); ?>
                        <?php $this->Form->unlockField('socket_id') ?>
                        <?= $this->Form->end() ?>
                        <a href="#" form-id="kr_achieve_<?= $kr['KeyResult']['id'] ?>"
                           class="kr_achieve_button no-line">
                            <i class="fa-check-circle fa goal-detail-kr-achieve-yet"></i>
                        </a>
                    <?php endif; ?>
                <?php endif; ?>

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
