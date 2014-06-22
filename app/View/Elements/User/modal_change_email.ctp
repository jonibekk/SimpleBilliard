<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 6/6/14
 * Time: 3:19 PM
 *
 * @var View $this
 */
?>
<div class="modal fade" id="modal_change_email">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title"><?= __d('gl', "メールアドレスの変更") ?></h4>
            </div>
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
                'url' => ['controller' => 'users', 'action' => 'change_email'],
                'class'         => 'form-horizontal',
                'novalidate'    => true,
                'id'            => 'ChangeEmail',
            ]); ?>
            <div class="modal-body">
                <?=
                $this->Form->input('email', [
                    'label'       => __d('gl', "メールアドレス"),
                    'placeholder' => __d('gl', "hiroshi@example.com"),
                ])?>
            </div>
            <div class="modal-footer">
                <?= $this->Form->submit(__d('gl', "変更を保存"), ['class' => 'btn btn-primary']) ?>
            </div>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
<? $this->append('script') ?>
<script type="text/javascript">
    $(document).ready(function () {
        $('#ChangeEmail').bootstrapValidator({
            live: 'enabled',
            feedbackIcons: {
                valid: 'fa fa-check',
                invalid: 'fa fa-times',
                validating: 'fa fa-refresh'
            },
            fields: {
                "data[Email][email]": {
                    validators: {
                        notEmpty: {
                            message: '<?=__d('validate', "入力必須項目です。")?>'
                        },
                        emailAddress: {
                            message: '<?=__d('validate', "メールアドレスが正しくありません。")?>'
                        }
                    }
                }
            }
        });
    });
</script>
<? $this->end() ?>
