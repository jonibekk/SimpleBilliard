<?php
/**
 * @var $circle_member
 */
?>
<?= $this->App->viewStartComment()?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close font_33px close-design" data-dismiss="modal" aria-hidden="true"><span
                    class="close-icon">&times;</span></button>
            <h4 class="modal-title"><?= __("Circle setting") ?></h4>
        </div>
        <?=
        $this->Form->create('CircleMember', [
            'url'           => ['controller' => 'circles', 'action' => 'ajax_change_setting'],
            'inputDefaults' => [
                'div'       => false,
                'label'     => [
                    'class' => ''
                ],
                'wrapInput' => false,
                'class'     => ''
            ],
            'class'         => '',
            'novalidate'    => true,
            'id'            => 'CircleSettingForm',
        ]); ?>
        <?= $this->Form->hidden('circle_id', ['value' => $circle_member['CircleMember']['circle_id']]); ?>
        <div class="modal-body">
            <div class="row borderBottom">
                <div class="col col-xxs-12 mpTB0">
                    <div class="comment-body modal-comment">
                        <div class="pull-right">
                            <?= $this->Form->input("show_for_all_feed_flg",
                                [
                                    'label'   => false,
                                    'div'     => false,
                                    'type'    => 'checkbox',
                                    'class'   => 'bt-switch',
                                    'checked' => $circle_member['CircleMember']['show_for_all_feed_flg'],
                                ]) ?>
                        </div>

                        <div class="ptb_10px font_bold">
                            <?= __("Display on Home feed") ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row borderBottom">
                <div class="col col-xxs-12 mpTB0">
                    <div class="comment-body modal-comment">
                        <div class="pull-right">
                            <?= $this->Form->input("get_notification_flg",
                                [
                                    'label'   => false,
                                    'div'     => false,
                                    'type'    => 'checkbox',
                                    'class'   => 'bt-switch',
                                    'checked' => $circle_member['CircleMember']['get_notification_flg'],

                                ]) ?>
                        </div>

                        <div class="ptb_10px font_bold">
                            <?= __("Notify new posts") ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?= $this->Form->end() ?>

    </div>
</div>
<?= $this->App->viewEndComment()?>
