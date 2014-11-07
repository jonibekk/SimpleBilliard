<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 9/26/14
 * Time: 11:14 AM
 *
 * @var CodeCompletionView $this
 * @var                    $key_results
 * @var                    $kr_can_edit
 * @var                    $incomplete_kr_count
 * @var                    $goal_id
 */
?>
<!-- START app/View/Elements/Goal/key_result_items.ctp -->
<? if (!empty($key_results)): ?>
    <? foreach ($key_results as $kr): ?>
        <div class="bd-t h_50px">
            <div class="col col-xxs-12 w_90per mxh_50px line-numbers ln_1 ptb_5px">
                <a href="#" class="develop--forbiddenLink">
                    <i class=" fa fa-check-circle disp_ib mr_5px font_40px tap-btn text-align_c <?= empty($kr['KeyResult']['completed']) ? 'check-off' : 'check-fin' ?>"></i>
                </a>

                <div class="disp_ib w_80per">
                    <span class="line-numbers ln_1 tap-btn-text font_verydark fin-kr">
                        <?= h($kr['KeyResult']['name']) ?></span>
                    <i class="fa fa-check-circle"><span class="ml_2px">0</span></i>
                </div>
            </div>
            <? if ($kr_can_edit): ?>
                <div class="pull-right dropdown">
                    <a href="#" class="font_lightGray-gray font_14px plr_4px pt_2px pb_2px"
                       data-toggle="dropdown"
                       id="download">
                        <i class="fa fa-cog mt_16px"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-right frame-arrow-icon" role="menu"
                        aria-labelledby="dropdownMenu1">
                        <li role="presentation">
                            <a href="<?= $this->Html->url(['controller' => 'goals', 'action' => 'ajax_get_edit_key_result_modal', $kr['KeyResult']['id']]) ?>"
                               class="modal-ajax-get-add-key-result">
                                <i class="fa fa-pencil"><span class="ml_2px"><?= __d('gl', "出したい成果を編集する") ?></span></i></a>
                        </li>
                        <li role="presentation">
                            <? if ($kr['KeyResult']['completed']): ?>
                                <?= $this->Form->postLink('<i class="fa fa-reply"><span class="ml_2px">' .
                                                          __d('gl', "出したい成果を未完了にする") . '</span></i>',
                                                          ['controller' => 'goals', 'action' => 'incomplete', $kr['KeyResult']['id']],
                                                          ['escape' => false]) ?>
                            <? else: ?>
                                <?
                                //最後のKRの場合
                                if ($incomplete_kr_count === 1):?>
                                    <a href="<?= $this->Html->url(['controller' => 'goals', 'action' => 'ajax_get_last_kr_confirm', $kr['KeyResult']['id']]) ?>"
                                       class="modal-ajax-get">
                                        <i class="fa fa-check"><span class="ml_2px"><?= __d('gl',
                                                                                            "出したい成果を完了にする") ?></span></i>
                                    </a>
                                <? else: ?>
                                    <?= $this->Form->postLink('<i class="fa fa-check"><span class="ml_2px">' .
                                                              __d('gl', "出したい成果を完了にする") . '</span></i>',
                                                              ['controller' => 'goals', 'action' => 'complete', $kr['KeyResult']['id']],
                                                              ['escape' => false]) ?>
                                <? endif; ?>
                            <? endif; ?>
                        </li>

                        <? if (count($key_results) !== 1): ?>
                            <li role="presentation">
                                <?=
                                $this->Form->postLink('<i class="fa fa-trash"><span class="ml_5px">' .
                                                      __d('gl', "出したい成果を削除する") . '</span></i>',
                                                      ['controller' => 'goals', 'action' => 'delete_key_result', $kr['KeyResult']['id']],
                                                      ['escape' => false], __d('gl', "本当にこの成果を削除しますか？")) ?>
                            </li>
                        <? endif; ?>
                    </ul>
                </div>
            <? endif; ?>
        </div>
    <? endforeach ?>
    <? if ($kr_can_edit): ?>
        <div class="bd-t pt_8px">
            <a class="col col-xxs-12 bd-dash font_lightGray-gray p_10px modal-ajax-get-add-key-result"
               href="<?= $this->Html->url(['controller' => 'goals', 'action' => 'ajax_get_add_key_result_modal', $goal_id]) ?>">
                <i class="fa fa-plus-circle"><span class="ml_2px">
                                    <?= __d('gl', "出したい成果を追加") ?></span>
                </i>
            </a>
        </div>
    <? endif; ?>
<? else: ?>
    <div class="col col-xxs-12">
        <?= __d('gl', "成果はまだありません。") ?>
    </div>
<?endif; ?>
<!-- End app/View/Elements/Goal/key_result_items.ctp -->