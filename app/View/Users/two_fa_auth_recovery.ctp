<?php
/**
 * ２段階認証 リカバリコード入力画面
 */
?>
<?= $this->App->viewStartComment()?>
<div class="row">
    <div class="col-sm-8 col-sm-offset-2">
        <div class="panel panel-default">
            <div class="panel-heading"><?= __("Enter a Recovery Code") ?></div>
            <div class="panel-body login-panel-body">
                <?=
                $this->Form->create('User', [
                    'inputDefaults' => [
                        'div'       => 'form-group',
                        'label'     => [
                            'class' => 'col col-sm-3 control-label form-label'
                        ],
                        'wrapInput' => 'col col-sm-6',
                        'class'     => 'form-control login_input-design disable-change-warning'
                    ],
                    'class'         => 'form-horizontal login-form',
                    'novalidate'    => true
                ]); ?>
                <p class="mb_12px"><?= __("You can also login with a recovery code.") ?></p>

                <?=
                $this->Form->input('recovery_code', [
                    'label' => __("Code")
                ]) ?>

                <div class="form-group">
                    <div class="col-sm-9 col-sm-offset-3">
                        <?=
                        $this->Form->submit(__("Send a code"), ['class' => 'btn btn-primary']) ?>
                    </div>
                </div>
                <?= $this->Form->end(); ?>
            </div>
        </div>
    </div>
</div>
<?= $this->App->viewEndComment()?>
