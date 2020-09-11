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
<?= $this->App->viewStartComment()?>
<div id="notify_setting">
    <div class="panel panel-default">
        <div class="panel-heading"><?= __("Notification") ?></div>
        <?=
        $this->Form->create('User', [
            'inputDefaults' => [
                'div'   => 'form-group',
                'label' => [
                    'class' => 'col col-sm-3 control-label form-label'
                ],
                'class' => 'form-control setting_input-design'
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
            <div class="form-group">
                <label class="col col-sm-3 col-xxs-12 control-label form-label">
                    <i class="fa fa-globe"></i> <?= __('Goalous Web') ?>
                </label>

                <div class="col col-xxs-9 col-sm-9">
                    <p class="form-control-static"><?= h(NotifySetting::$TYPE_GROUP['all']) ?></p>
                </div>
            </div>
            <div id="NotifySettingAppHelp" class="col col-sm-offset-3 help-block font_12px">
                <?= __("You'll get all notification.") ?>
            </div>


            <hr>
            <div class="form-group">
                <label class="col col-sm-3 col-xxs-12 control-label form-label">
                    <i class="fa fa-envelope-o"></i> <?= __('Email') ?>
                </label>
                <?=
                $this->Form->input("NotifySetting.email_status", [
                    'id'        => 'NotifySettingEmail',
                    'label'     => false,
                    'div'       => false,
                    'type'      => 'select',
                    'class'     => 'form-control',
                    'options'   => NotifySetting::$TYPE_GROUP,
                    'wrapInput' => 'user-setting-notify-email-select col col-xxs-5 col-sm-3',
                ])
                ?>
            </div>
            <div id="NotifySettingEmailHelp" class="col col-sm-offset-3 help-block font_12px none"></div>

            <hr>
            <div class="form-group">
                <label class="col col-sm-3 col-xxs-12 control-label form-label">
                    <i class="fa fa-mobile"></i> <?= __('Mobile App') ?>
                </label>
                <?=
                $this->Form->input("NotifySetting.mobile_status", [
                    'id'        => 'NotifySettingMobile',
                    'label'     => false,
                    'div'       => false,
                    'type'      => 'select',
                    'class'     => 'form-control',
                    'options'   => NotifySetting::$TYPE_GROUP,
                    'wrapInput' => 'user-setting-notify-mobile-select col col-xxs-5 col-sm-3',
                ])
                ?>
            </div>
            <div id="NotifySettingMobileHelp" class="col col-sm-offset-3 help-block font_12px none"></div>

            <hr>
            <div class="form-group" id="desktopNormalOptions">

                <div class="col col-sm-offset-3 help-block font_12px none" style="color:red" id="desktopInvalidReminder" hidden>
                    <?=__("Browser notification permission denied!<br>Please enable notification permission via brower setting and refresh.")?>
                </div>
                <label class="col col-sm-3 col-xxs-12 control-label form-label">
                    <i class="fa fa-mobile"></i> <?= __('Desktop') ?>
                </label>
                <?=
                $this->Form->input("NotifySetting.desktop_status", [
                    'id'        => 'NotifySettingDesktop',
                    'label'     => false,
                    'div'       => false,
                    'type'      => 'select',
                    'class'     => 'form-control',
                    'options'   => NotifySetting::$TYPE_GROUP,
                    'wrapInput' => 'user-setting-notify-mobile-select col col-xxs-5 col-sm-3',
                ])
                ?>
            </div>
            <div id="NotifySettingDesktopHelp" class="col col-sm-offset-3 help-block font_12px none"></div>
        </div>
        <div class="panel-footer setting_pannel-footer">
            <?= $this->Form->submit(__("Save changes"), ['class' => 'btn btn-primary pull-right']) ?>
            <div class="clearfix"></div>
        </div>
        <?= $this->Form->end(); ?>
    </div>
</div>
<?php $this->append('script'); ?>
<script>
    $(function () {
        var notify_help_message = {
            'all': "<?= __("You'll get all notification.") ?>",
            'primary': "<?= __("You'll get important notification.") ?>",
            'none': "<?= __("Nothing will be deliverd.") ?>"
        };

        var onSelectChange = function () {
            var $select = $(this);
            var selected = $select.val();
            var $helpMessage = $('#' + $select.attr('id') + 'Help');

            $helpMessage.hide();
            if (notify_help_message[selected]) {
                $helpMessage.text(notify_help_message[selected]).show();
            }
        };
        $('#NotifySettingEmail').on('change', onSelectChange).trigger('change');
        $('#NotifySettingMobile').on('change', onSelectChange).trigger('change');
        $('#NotifySettingDesktop').on('change', onSelectChange).trigger('change');
         
        var invalidReminder = $('#desktopInvalidReminder');
        if (Notification.permission == 'denied') {
            invalidReminder.show();
        } else {
            invalidReminder.hide();
        }
    })
</script>
<?php $this->end(); ?>
<?= $this->App->viewEndComment()?>
