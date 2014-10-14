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
        <div class="col col-xxs-12">
            <?= h($kr['KeyResult']['name']) ?>
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
                            <?= __d('gl', "成果を更新") ?></a>
                    </li>
                    <li role="presentation">
                        <?=
                        $this->Form->postLink(__d('gl', "削除"),
                                              ['controller' => 'goals', 'action' => 'delete_key_result', $kr['KeyResult']['id']],
                                              null, __d('gl', "本当にこの成果を削除しますか？")) ?>
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