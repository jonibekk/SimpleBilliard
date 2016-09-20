<?php
/**
 * メールアドレスの再認証画面
 * Created by PhpStorm.
 * User: daikihirakata
 * Date: 2014/06/01
 * Time: 0:19
 *
 * @var CodeCompletionView $this
 */
?>
<?= $this->App->viewStartComment()?>
<div class="row">
    <div class="col-sm-8 col-sm-offset-2">
        <div class="panel panel-default">
            <div class="panel-heading"><?= __("Re-authentication of email address") ?></div>
            <?=
            $this->Form->create('User', [
                'inputDefaults' => [
                    'div'       => 'form-group',
                    'label'     => [
                        'class' => 'col col-sm-3 control-label'
                    ],
                    'wrapInput' => 'col col-sm-6',
                    'class'     => 'form-control'
                ],
                'class'         => 'form-horizontal validate',
                'novalidate'    => true
            ]); ?>
            <div class="panel-body">
                <p><?= __("Enter your email address.") ?></p>

                <p><?= __("We'll send the URL of the email address authentication.") ?></p>
                <?=
                $this->Form->input('email', [
                    'label'                        => __("Email Address"),
                    'data-bv-emailaddress-message' => __("Email address is incorrect."),
                    "data-bv-notempty-message"     => __("Input is required."),
                    'required'                     => true
                ]) ?>
            </div>
            <div class="panel-footer">
                <div class="form-group">
                    <div class="col-sm-9 col-sm-offset-3">
                        <?= $this->Form->submit(__("Send"), ['class' => 'btn btn-primary']) ?>
                    </div>
                </div>
            </div>
            <?= $this->Form->end(); ?>
        </div>
    </div>
</div>
<?= $this->App->viewEndComment()?>
