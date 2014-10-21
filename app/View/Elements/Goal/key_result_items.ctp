<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 9/26/14
 * Time: 11:14 AM
 *
 * @var CodeCompletionView $this
 * @var                    $key_results
 */
?>
<!-- START app/View/Elements/Goals/key_result_items.ctp -->
<? if (!empty($key_results)): ?>
    <? foreach ($key_results as $kr): ?>
        <div class="col col-xxs-12 bd-t ptb_5px">
            <i class=" fa fa-check-circle font_40px pull-left mr_5px tap-btn text-align_c check-on"></i>
            <div class="pull-left mw_75per"><span class="line-numbers ln_1 tap-btn-text font_verydark fin-kr"><?= h($kr['KeyResult']['name']) ?></span>
                <i class="fa fa-check-circle"><span class="ml_2px">5</span></i>
            </div>
            <div class="pull-right dropdown">
                <a href="#" class="font_lightGray-gray font_14px plr_4px pt_1px pb_2px"
                   data-toggle="dropdown"
                   id="download">
                    <i class="fa fa-cog"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-right frame-arrow-icon" role="menu"
                    aria-labelledby="dropdownMenu1">
                    <li role="presentation">
                        <a href="<?= $this->Html->url(['controller' => 'goals', 'action' => 'ajax_get_edit_key_result_modal', $kr['KeyResult']['id']]) ?>"
                           class="modal-ajax-get-add-key-result">
                            <i class="fa fa-pencil"><span class="ml_2px"><?= __d('gl', "成果を更新") ?></span></i></a>
                    </li>
                    <li role="presentation">
                        <? if ($kr['KeyResult']['completed']): ?>
                            <?= $this->Form->postLink('<i class="fa fa-pencil"><span class="ml_2px">' .
                                                      __d('gl', "未完了にする") . '</span></i>',
                                                      ['controller' => 'goals', 'action' => 'incomplete', $kr['KeyResult']['id']],
                                                      ['escape' => false]) ?>
                        <? else: ?>
                            <?= $this->Form->postLink('<i class="fa fa-pencil"><span class="ml_2px">' .
                                                      __d('gl', "完了にする") . '</span></i>',
                                                      ['controller' => 'goals', 'action' => 'complete', $kr['KeyResult']['id']],
                                                      ['escape' => false]) ?>
                        <?endif; ?>
                    </li>
                    <li role="presentation">
                        <?=
                        $this->Form->postLink('<i class="fa fa-trash"><span class="ml_5px">' .
                                              __d('gl', "削除") . '</span></i>',
                                              ['controller' => 'goals', 'action' => 'delete_key_result', $kr['KeyResult']['id']],
                                              ['escape' => false], __d('gl', "本当にこの成果を削除しますか？")) ?>
                    </li>
                </ul>
            </div>

        </div>
    <? endforeach ?>
<? else: ?>
    <div class="col col-xxs-12">
        <?= __d('gl', "成果はまだありません。") ?>
    </div>
<?endif; ?>
<!-- End app/View/Elements/Goals/key_result_items.ctp -->