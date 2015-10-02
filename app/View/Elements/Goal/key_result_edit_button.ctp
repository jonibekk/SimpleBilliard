<?php
/**
 * @var $kr
 * @var $incomplete_kr_count
 */
?>
<?php if (isset($kr) && $kr): ?>
    <!-- START app/View/Elements/Goal/key_result_edit_button.ctp -->
    <div class="btn-edit-kr-wrap pull-right dropdown">
        <a href="#" class="font_lightGray-gray font_14px plr_4px pt_2px pb_2px"
           data-toggle="dropdown"
           id="download">
            <i class="fa fa-ellipsis-h btn-edit-kr"></i>
        </a>
        <ul class="dropdown-menu dropdown-menu-right frame-arrow-icon" role="menu"
            aria-labelledby="dropdownMenu1">
            <?php if (!$kr['KeyResult']['completed']): ?>
            <li role="presentation">
                <a href="<?= $this->Html->url(['controller' => 'goals', 'action' => 'ajax_get_edit_key_result_modal', 'key_result_id' => $kr['KeyResult']['id']]) ?>"
                   class="modal-ajax-get-add-key-result">
                    <i class="fa fa-pencil"></i><span class="ml_2px"><?= __d('gl', "出したい成果を編集する") ?></span></a>
            </li>
            <?php endif ?>
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

            <?php if (!$kr['KeyResult']['completed']): ?>
            <li role="presentation">
                <?=
                $this->Form->postLink('<i class="fa fa-trash"></i><span class="ml_5px">' .
                                      __d('gl', "出したい成果を削除する") . '</span>',
                                      ['controller' => 'goals', 'action' => 'delete_key_result', 'key_result_id' => $kr['KeyResult']['id']],
                                      ['escape' => false], __d('gl', "本当にこの成果を削除しますか？")) ?>
            </li>
            <?php endif ?>
        </ul>
    </div>
    <!-- END app/View/Elements/Goal/key_result_edit_button.ctp -->
<?php endif ?>
