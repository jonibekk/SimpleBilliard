<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 6/6/14
 * Time: 3:19 PM
 *
 * @var CodeCompletionView $this
 * @var                    $kr_id
 * @var                    $goal
 */
?>
<!-- START app/View/Elements/Goal/modal_last_kr_confirm.ctp -->
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close font_33px close-design" data-dismiss="modal" aria-hidden="true"><span
                    class="close-icon">&times;</span></button>
            <h4 class="modal-title"><?= __d\('app', "ゴールを達成しましたか？") ?></h4>
        </div>
        <div class="modal-body modal-circle-body">
            <ul class="add-key-result-goal-info">
                <li>
                  <i class="fa fa-flag"></i><?= __d\('app', "ゴール名") ?>:<?= h($goal['Goal']['name']) ?>
                </li>
                <li>
                  <?= __d\('app', "単位") ?>:<?= KeyResult::$UNIT[$goal['Goal']['value_unit']] ?>
                </li>
                <li>
                  <?= __d\('app', "現在値") ?>:<?= h($goal['Goal']['current_value']) ?>
                </li>
                <li>
                  <?= __d\('app', "開始時") ?>:<?= h($goal['Goal']['start_value']) ?>
                </li>
                <li>
                  <?= __d\('app', "達成時") ?>:<?= h($goal['Goal']['target_value']) ?>
                </li>
            </ul>
        </div>
        <div class="modal-footer">
            <div class="text-align_l font_12px font_rouge mb_12px">※どちらを選択しても、選択中の成果は「完了」となります。</div>
            <?=
            $this->Form->create('Post', [
                'url'           => ['controller' => 'goals', 'action' => 'complete_kr', 'key_result_id' => $kr_id, true],
                'inputDefaults' => [
                    'div'       => 'form-group',
                    'label'     => false,
                    'wrapInput' => '',
                ],
                'class'         => 'form-feed-notify'
            ]); ?>
            <?php $this->Form->unlockField('socket_id') ?>
            <a href="<?= $this->Html->url(['controller' => 'goals', 'action' => 'ajax_get_add_key_result_modal', 'goal_id' => $goal['Goal']['id'], 'key_result_id' => $kr_id]) ?>"
               class="btn btn-default modal-ajax-get-add-key-result" data-dismiss="modal"><?= __d\('app',
                                                                                                  "達成要素を追加") ?></a>
            <?=
            $this->Form->submit(__d\('app', "ゴール達成"),
                                ['class' => 'btn btn-primary', 'div' => false]) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
<!-- END app/View/Elements/Goal/modal_last_kr_confirm.ctp -->
