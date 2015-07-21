<?php
/**
 * @var $key_results
 * @var $incomplete_kr_count
 * @var $edit_kr
 */
?>
<?php if ($key_results): ?>
    <?php foreach ($key_results as $kr): ?>
        <div style="padding:3px; margin:3px;">

            <?php if (isset($edit_kr) && $edit_kr): ?>
                <div class="pull-right dropdown">
                    <a href="#" class="font_lightGray-gray font_14px plr_4px pt_2px pb_2px"
                       data-toggle="dropdown"
                       id="download">
                        <i class="fa fa-cog mt_16px"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-right frame-arrow-icon" role="menu"
                        aria-labelledby="dropdownMenu1">
                        <li role="presentation">
                            <a href="<?= $this->Html->url(['controller' => 'goals', 'action' => 'ajax_get_edit_key_result_modal', 'key_result_id' => $kr['KeyResult']['id']]) ?>"
                               class="modal-ajax-get-add-key-result">
                                <i class="fa fa-pencil"></i><span class="ml_2px"><?= __d('gl', "出したい成果を編集する") ?></span></a>
                        </li>
                        <li role="presentation">
                            <?php if ($kr['KeyResult']['completed']): ?>
                                <?= $this->Form->postLink('<i class="fa fa-reply"></i><span class="ml_2px">' .
                                                          __d('gl', "出したい成果を未完了にする") . '</span>',
                                                          ['controller' => 'goals', 'action' => 'incomplete_kr', 'key_result_id' => $kr['KeyResult']['id']],
                                                          ['escape' => false]) ?>
                            <?php else: ?>
                                <?php //最後のKRの場合
                                if ($incomplete_kr_count === 1):?>
                                    <a href="<?= $this->Html->url(['controller' => 'goals', 'action' => 'ajax_get_last_kr_confirm', 'key_result_id' => $kr['KeyResult']['id']]) ?>"
                                       class="modal-ajax-get">
                                        <i class="fa fa-check"></i><span class="ml_2px"><?= __d('gl',
                                                                                                "出したい成果を完了にする") ?></span>
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
                                       class="kr_achieve_button">
                                        <i class="fa fa-check"></i><span class="ml_2px">
                                            <?= __d('gl', "出したい成果を完了にする"); ?>
                                        </span>
                                    </a>
                                <?php endif; ?>
                            <?php endif; ?>
                        </li>

                        <li role="presentation">
                            <?=
                            $this->Form->postLink('<i class="fa fa-trash"></i><span class="ml_5px">' .
                                                  __d('gl', "出したい成果を削除する") . '</span>',
                                                  ['controller' => 'goals', 'action' => 'delete_key_result', 'key_result_id' => $kr['KeyResult']['id']],
                                                  ['escape' => false], __d('gl', "本当にこの成果を削除しますか？")) ?>
                        </li>
                    </ul>
                </div>
            <?php endif ?>

            <div>
                <strong><?= h($kr['KeyResult']['name']) ?></strong>
                <?php if ($kr['KeyResult']['completed']): ?>
                    <?= __d('gl', 'クリア') ?>
                <?php endif ?>
                <br>
                <i class="fa fa-check-circle"></i>
                <?= h($kr['KeyResult']['action_result_count']) ?>
                <br>
                <?= h(round($kr['KeyResult']['start_value'],
                            1)) ?><?= h(KeyResult::$UNIT[$kr['KeyResult']['value_unit']]) ?> →
                <?= h(round($kr['KeyResult']['target_value'],
                            1)) ?><?= h(KeyResult::$UNIT[$kr['KeyResult']['value_unit']]) ?>
                <br>
                <?= $this->Time->format('Y/m/d',
                                        $kr['KeyResult']['start_date'] + $this->Session->read('Auth.User.timezone') * 3600) ?>
                →
                <?= $this->Time->format('Y/m/d',
                                        $kr['KeyResult']['end_date'] + $this->Session->read('Auth.User.timezone') * 3600) ?>
                <br>
            </div>

        </div>
    <?php endforeach ?>
<?php endif ?>