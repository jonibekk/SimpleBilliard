<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 6/19/14
 * Time: 2:41 PM
 *
 * @var CodeCompletionView $this
 * @var boolean            $last_first
 * @var string             $language_name
 * @var array              $me
 * @var boolean            $is_not_use_local_name
 */
?>
<!-- START app/View/Elements/User/notify_setting.ctp -->
<div id="profile">
    <div class="panel panel-default">
        <div class="panel-heading"><?= __d('gl', "通知") ?></div>
        <?=
        $this->Form->create('User', [
            'inputDefaults' => [
                'div'       => 'form-group',
                'label'     => [
                    'class' => 'col col-sm-3 control-label form-label'
                ],
                'wrapInput' => 'col col-xxs-5 col-sm-3 notify-setting-switch',
                'class'     => 'form-control setting_input-design'
            ],
            'class'         => 'form-horizontal',
            'novalidate'    => true,
            'type'          => 'file',
            'id'            => 'ChangeProfileForm'
        ]); ?>
        <?= $this->Form->hidden('NotifySetting.user_id', ['value' => $this->Session->read('Auth.User.id')]) ?>
        <?=
        $this->Form->hidden('NotifySetting.id',
                            ['value' => isset($this->request->data['NotifySetting']['id']) ? $this->request->data['NotifySetting']['id'] : null]) ?>
        <div class="panel-body notify-setting-panel-body">
            <? foreach (NotifySetting::$TYPE as $k => $v): ?>
                <div class="form-group">
                    <label class="col col-sm-3 col-xxs-12 control-label form-label"><?= $v['field_real_name'] ?></label>
                    <?=
                    $this->Form->input("NotifySetting.{$v['field_prefix']}_app_flg",
                                       [
                                           'label'       => false,
                                           'beforeInput' => '<i class="fa fa-bell-o icon-before-input" data-toggle="tooltip" title="' .
                                               __d('gl', "アプリ通知") . '"></i>&nbsp;',
                                           'div'         => false,
                                           'type'        => 'checkbox',
                                           'class'       => 'bt-switch',
                                           'default'     => true,
                                       ])
                    ?>
                    <?=
                    $this->Form->input("NotifySetting.{$v['field_prefix']}_email_flg",
                                       [
                                           'label'       => false,
                                           'beforeInput' => '<i class="fa fa-envelope-o icon-before-input" data-toggle="tooltip" title="' .
                                               __d('gl', "メール通知") . '"></i>&nbsp;',
                                           'div'         => false,
                                           'type'        => 'checkbox',
                                           'class'       => 'bt-switch',
                                           'default'     => true,
                                       ])
                    ?>
                </div>
            <? endforeach; ?>
        </div>
        <div class="panel-footer setting_pannel-footer">
            <?= $this->Form->submit(__d('gl', "変更を保存"), ['class' => 'btn btn-primary pull-right']) ?>
            <div class="clearfix"></div>
        </div>
        <?= $this->Form->end(); ?>
    </div>
</div>
<!-- END app/View/Elements/User/notify_setting.ctp -->
