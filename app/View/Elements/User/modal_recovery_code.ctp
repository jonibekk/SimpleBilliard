<?php
/**
 * @var $recovery_codes
 */
?>
<!-- START app/View/Elements/User/modal_recovery_code.ctp -->
<div class="modal-dialog" id="RecoveryCodeModal">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close font_33px close-design" data-dismiss="modal" aria-hidden="true"><span
                    class="close-icon">&times;</span></button>
            <h4 class="modal-title"><?= __("リカバリーコード") ?></h4>
        </div>
        <div class="modal-body">
            <div class="two_fa_step_box">
                <?= __('これらのコードを印刷するか書き留めて、安全な場所に保管しましょう。各リカバリーコードは１回のみ使用できます。') ?>
            </div>
            <ol id="RecoveryCodeList">
                <?php foreach ($recovery_codes as $v): ?>
                    <?php $_code =  wordwrap($v['RecoveryCode']['code'], 4, ' ', true); ?>
                    <?php if ($v['RecoveryCode']['used']): ?>
                        <li><span class="font_lightgray"><s><?= h($_code) ?></s>&nbsp;&nbsp;<span class="font_10px"><?= __("使用済") ?></span></span></li>
                    <?php else: ?>
                        <li><?= h($_code) ?></li>
                    <?php endif ?>
                <?php endforeach ?>
            </ol>
            <hr>
            <?= $this->Html->link(__("新しいリカバリーコードを作成"), '#', [
                'class' => 'regenerate-recovery-code'
            ]) ?>
            <div class="font_11px">
                <?= __('新しいリカバリーコードを生成すると、上記のリカバリーコードは全て無効になります。') ?>
            </div>
            <?= $this->Form->create('RecoveryCode', [
                'url'   => ['controller' => 'users', 'action' => 'ajax_regenerate_recovery_code'],
                'class' => 'none',
                'id'    => 'RegenerateRecoveryCodeForm',
                'type'  => 'post',
            ]) ?>
            <?= $this->Form->end() ?>
        </div>
        <div class="modal-footer">
            <?=
            $this->Form->button(__("Completed"), [
                'class'        => 'btn btn-primary pull-right',
                'data-dismiss' => 'modal',
                'div'          => false,
            ]) ?>
        </div>
    </div>
</div>
<!-- END app/View/Elements/User/modal_recovery_code.ctp -->
