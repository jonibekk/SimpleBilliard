<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 6/6/14
 * Time: 3:19 PM
 *
 * @var CodeCompletionView $this
 * @var                    $key_result_id
 * @var                    $skr
 */
?>
<!-- START app/View/Elements/Goal/modal_edit_key_result.ctp -->
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close font_33px close-design" data-dismiss="modal" aria-hidden="true"><span
                    class="close-icon">&times;</span></button>
            <h4 class="modal-title"><?= __d('gl', "ゴールが達成しましたか？") ?></h4>
        </div>
        <div class="modal-body modal-circle-body">
            <div class="col col-xxs-12">
                <?= h($skr['KeyResult']['name']) ?>
            </div>
        </div>
        <div class="modal-footer">
            <a href="<?= $this->Html->url(['controller' => 'goals', 'action' => 'ajax_get_add_key_result_modal', $skr['KeyResult']['id']]) ?>"
               class="btn btn-default modal-ajax-get-add-key-result" data-dismiss="modal"><?= __d('gl',
                                                                                                  "出したい成果を追加") ?></a>
            <?= $this->Form->postLink(__d('gl', "ゴール達成"),
                                      ['controller' => 'goals', 'action' => 'complete', $key_result_id, true],
                                      ['escape' => false, 'class' => 'btn btn-default']) ?>
        </div>
    </div>
</div>
<!-- END app/View/Elements/Goal/modal_edit_key_result.ctp -->
