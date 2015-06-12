<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 *
 * @var CodeCompletionView $this
 * @var                    $index
 * @var                    $id
 */
?>
<!-- START app/View/Elements/Team/confirm_to_inactive_score_modal.ctp -->
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close font_33px close-design" data-dismiss="modal" aria-hidden="true"><span
                    class="close-icon">&times;</span></button>
            <h4 class="modal-title"><?= __d('gl', "スコア削除の確認") ?></h4>
        </div>
        <div class="modal-body">
            <div class="col col-xxs-12">
                <p><?= __d('gl', "スコアを削除すると、過去のデータには影響ありません。") ?></p>

                <p><?= __d('gl', "今後、新規に選択はできなくなります。") ?></p>

                <p><?= __d('gl', "本当にこのスコア定義を削除しますか？") ?></p>
            </div>
        </div>
        <div class="modal-footer">
            <?=
            $this->Form->postLink(__d('gl', "削除する"),
                                  ['controller' => 'teams', 'action' => 'to_inactive_score','team_id'=>$id],
                                  ['class' => 'btn btn-primary']) ?>
        </div>
    </div>
</div>
<!-- END app/View/Elements/Team/confirm_to_inactive_score_modal.ctp -->
