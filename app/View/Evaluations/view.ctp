<?
/**
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Pages
 * @since         CakePHP(tm) v 0.10.0.1076
 * @var CodeCompletionView $this
 */
?>
<!-- START app/View/Evaluations/view.ctp -->
<div class="row">
    <div class="col-sm-8 col-sm-offset-2">
        <div class="panel panel-default">
            <div class="panel-heading"><?= __d('gl', "評価画面的タイトル") ?></div>
            <div class="panel-body add-team-panel-body">
                評価画面だよ。なる。よろしく(^^)
            </div>
        </div>
    </div>
</div>
<div class="panel panel-default">
    <div class="panel-heading"><?= __d('gl', "アカウント") ?></div>
    <?=
    $this->Form->create('User', [
        'inputDefaults' => [
            'div'       => 'form-group',
            'label'     => [
                'class' => 'col col-sm-3 control-label form-label'
            ],
            'wrapInput' => 'col col-sm-6',
            'class'     => 'form-control setting_input-design'
        ],
        'class'         => 'form-horizontal',
        'novalidate'    => true,
        'id'            => 'UserAccountForm',
    ]); ?>
    <div class="panel-body user-setting-panel-body">
        <div class="form-group">
            <label for="PrimaryEmailEmail" class="col col-sm-3 control-label form-label"><?= __d('gl', "お疲れ様です") ?></label>

            <div class="col col-sm-6">
                <p class="form-control-static"></p>

                <? if (!empty($not_verified_email)): ?>
                    <p class="form-control-static">
                        <a href="#" rel="tooltip" title="<?= __d('gl', "認証待ちのメールアドレスが存在するため、変更はできません。") ?>">
                            <?= __d('gl', "メールアドレスを変更する") ?>
                        </a>
                    </p>
                    <div class="alert alert-warning fade in">
                        <p></p>
                        <p></p>
                        <a href="#" data-toggle="modal" data-target="#modal_delete_email"></a>
                    </div>

                <? else: ?>
                    <p class="form-control-static">
                        <a href="#" data-toggle="modal" data-target="#modal_change_email"></a>
                    </p>
                <?
                endif ?>
            </div>
        </div>
        <hr>
        <hr>
        <hr>
        <div class="form-group">
            <label for="UserPassword" class="col col-sm-3 control-label form-label"><?= __d('gl', "太郎") ?></label>
            <div class="col col-sm-6">
                <p class="form-control-static">
                    <?= __d('gl', "花子") ?>
                </p>
            </div>
        </div>
    </div>
    <div class="panel-footer setting_pannel-footer">
        <?= $this->Form->submit(__d('gl', "変更を保存"), ['class' => 'btn btn-primary pull-right']) ?>
        <div class="clearfix"></div>
    </div>
    <?= $this->Form->end(); ?>
</div>
<? $this->append('script') ?>
<script type="text/javascript">
    $(document).ready(function () {
        $('#UserAccountForm').bootstrapValidator({
            live: 'enabled',
            feedbackIcons: {
                valid: 'fa fa-check',
                invalid: 'fa fa-times',
                validating: 'fa fa-refresh'
            },
            fields: {}
        });
    });
</script>
<? $this->end() ?>
<?= $this->element('User/modal_change_password') ?>
<?= $this->element('User/modal_change_email') ?>
<?
if (!empty($not_verified_email)) {
    echo $this->element('User/modal_delete_email',
                        ['email'    => $not_verified_email['Email']['email'],
                         'email_id' => $not_verified_email['Email']['id']]);
}
?>
<!-- END app/View/Evaluations/view.ctp -->
