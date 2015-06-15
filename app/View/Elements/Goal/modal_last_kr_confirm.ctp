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
            <h4 class="modal-title"><?= __d('gl', "ゴールが達成しましたか？") ?></h4>
        </div>
        <div class="modal-body modal-circle-body">
            <div class="col col-xxs-12">
                <?= __d('gl', "ゴール名") ?>:<?= h($goal['Goal']['name']) ?><br>
                <?= __d('gl', "単位") ?>:<?= KeyResult::$UNIT[$goal['Goal']['value_unit']] ?><br>
                <?= __d('gl', "現在値") ?>:<?= $goal['Goal']['current_value'] ?><br>
                <?= __d('gl', "開始時") ?>:<?= $goal['Goal']['start_value'] ?><br>
                <?= __d('gl', "達成時") ?>:<?= $goal['Goal']['target_value'] ?><br>
            </div>
        </div>
        <div class="modal-footer">
            <div class="text-align_l font_12px font_rouge mb_12px">※どちらを選択しても、このゴールに紐づいた出したい成果は「すべて完了」となります。</div>
            <?=
            $this->Form->create('Post', [
                'url'           => ['controller' => 'goals', 'action' => 'complete_kr', $kr_id, true],
                'inputDefaults' => [
                    'div'       => 'form-group',
                    'label'     => false,
                    'wrapInput' => '',
                ],
                'class'         => 'form-feed-notify'
            ]); ?>
            <?php $this->Form->unlockField('socket_id') ?>
            <a href="<?= $this->Html->url(['controller' => 'goals', 'action' => 'ajax_get_add_key_result_modal', 'goal_id' => $goal['Goal']['id'], 'key_result_id' => $kr_id]) ?>"
               class="btn btn-default modal-ajax-get-add-key-result" data-dismiss="modal"><?= __d('gl',
                                                                                                  "出したい成果を追加") ?></a>
            <?=
            $this->Form->submit(__d('gl', "ゴール達成"),
                                ['class' => 'btn btn-default', 'div' => false]) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
<!-- END app/View/Elements/Goal/modal_last_kr_confirm.ctp -->
