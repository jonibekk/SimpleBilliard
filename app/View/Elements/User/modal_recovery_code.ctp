<?php
/**
 * @var $recovery_codes
 */
?>
<?= $this->App->viewStartComment()?>
<div class="modal-dialog" id="RecoveryCodeModal">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close font_33px close-design" data-dismiss="modal" aria-hidden="true"><span
                    class="close-icon">&times;</span></button>
            <h4 class="modal-title"><?= __("Recovery Codes") ?></h4>
        </div>
        <div class="modal-body">
            <div class="two_fa_step_box">
                <?= __('Keep them somewhere safe and acessible. Each code can be used only once.') ?>
            </div>
            <ol id="RecoveryCodeList">
                <?php foreach ($recovery_codes as $v): ?>
                    <?php $_code = wordwrap($v['RecoveryCode']['code'], 4, ' ', true); ?>
                    <?php if ($v['RecoveryCode']['used']): ?>
                        <li><span class="font_lightgray"><s><?= h($_code) ?></s>&nbsp;&nbsp;<span
                                    class="font_10px"><?= __("Used") ?></span></span></li>
                    <?php else: ?>
                        <li><?= h($_code) ?></li>
                    <?php endif ?>
                <?php endforeach ?>
            </ol>
            <hr>
            <?= $this->Html->link(__("Generate new recovery codes"), '#', [
                'class' => 'regenerate-recovery-code'
            ]) ?>
            <div class="font_11px">
                <?= __('After making new recovery codes, old ones will be unavailable.') ?>
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
<?= $this->App->viewEndComment()?>
