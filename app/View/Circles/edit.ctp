<?= $this->App->viewStartComment()?>
<div class="row" id="CircleEdit">
    <div class="col-sm-8 col-sm-offset-2">
        <div class="panel panel-default">

            <div class="modal-header none-border">
                <h4 class="modal-title"><?= __("Edit Circle") ?></h4>
            </div>
            <ul class="nav nav-tabs">
                <li class="active"><a href="#tab1" data-toggle="tab"><?= __("View info") ?></a></li>
                <li><a href="#tab2" data-toggle="tab" id="memberListTab"><?= __("Members list") ?></a></li>
                <?php if (!$this->request->data['Circle']['team_all_flg']): ?>
                    <li><a href="#tab3" data-toggle="tab" id="addMembersTab"><?= __("Add member(s)") ?></a></li>
                <?php endif ?>
            </ul>

            <div class="modal-body modal-circle-body tab-content" style="max-height:none;">
                <div class="tab-pane fade in active" id="tab1">
                    <?=
                    // サークル基本情報変更フォーム
                    $this->Form->create('Circle', [
                        'url'           => [
                            'controller' => 'circles',
                            'action'     => 'update',
                            'circle_id'  => $this->request->data['Circle']['id']
                        ],
                        'inputDefaults' => [
                            'div'       => 'form-group',
                            'label'     => [
                                'class' => 'circle-create-label'
                            ],
                            'wrapInput' => false,
                            'class'     => 'form-control modal_input-design'
                        ],
                        'class'         => 'form-horizontal',
                        'type'          => 'file',
                        'id'            => 'EditCircleForm',
                    ]); ?>
                    <?= $this->Form->hidden('id') ?>
                    <?=
                    $this->Form->input('name',
                        [
                            'label'                        => __("Circle name"),
                            'placeholder'                  => __("eg) the sales division"),
                            "data-bv-notempty-message"     => __("Input is required."),
                            'data-bv-stringlength'         => 'true',
                            'data-bv-stringlength-max'     => 128,
                            'data-bv-stringlength-message' => __("It's over limit characters (%s).", 128)
                        ]) ?>
                    <?php if (!$this->request->data['Circle']['team_all_flg']): ?>

                        <?php $privacy_option = Circle::$TYPE_PUBLIC;
                        $privacy_option[Circle::TYPE_PUBLIC_ON] .= '<span class="help-block font_11px">' . __(
                                "Anyone can see the circle, its members and their posts.") . '</span>';
                        $privacy_option[Circle::TYPE_PUBLIC_OFF] .= '<span class="help-block font_11px">' . __(
                                "Only members can find the circle and see posts.") . '</span>';
                        ?>
                        <div class="form-group">
                            <label class="circle-create-label"><?= __('Privacy') ?></label>

                            <div>
                                <span class="font_14px">
                                <?= $privacy_option[$this->request->data['Circle']['public_flg']] ?>
                                </span>
                            </div>
                        </div>
                    <?php endif ?>

                    <div class="form-group">
                        <label class="circle-create-label"><?= __('Circle Description') ?></label>
                        <?=
                        $this->Form->input('description',
                            [
                                'label'                        => false,
                                'placeholder'                  => __("eg) Let's share the latest information."),
                                'rows'                         => 1,
                                'data-bv-notempty-message'     => __("Input is required."),
                                'data-bv-stringlength'         => 'true',
                                'data-bv-stringlength-max'     => 2000,
                                'data-bv-stringlength-message' => __("It's over limit characters (%s).", 2000),
                            ]) ?>
                    </div>

                    <div class="form-group">
                        <label for="" class="circle-create-label"><?= __("Circle Image") ?></label>

                        <div class="ccc">
                            <div class="fileinput_small fileinput-new" data-provides="fileinput">
                                <div class="fileinput-preview thumbnail nailthumb-container" data-trigger="fileinput"
                                     style="width: 96px; height: 96px; line-height: 96px;"
                                >
                                    <?=
                                    $this->Upload->uploadImage($this->request->data, 'Circle.photo',
                                        ['style' => 'medium_large']) ?>
                                </div>
                                <div>
                            <span class="btn btn-default btn-file">
                                <span class="fileinput-new">
                                    <?= __("Select an image") ?>
                                </span>
                                <span class="fileinput-exists">
                                    <?= __("Reselect an image") ?>
                                </span>
                                <?=
                                $this->Form->input('photo',
                                    [
                                        'type'         => 'file',
                                        'label'        => false,
                                        'div'          => false,
                                        'css'          => false,
                                        'wrapInput'    => false,
                                        'errorMessage' => false,
                                        'required'     => false
                                    ]) ?>
                            </span>
                                    <span class="help-block font_11px inline-block"><?= __('Smaller than 10MB') ?></span>
                                </div>
                            </div>

                            <div class="has-error">
                                <?=
                                $this->Form->error('photo', null,
                                    [
                                        'class' => 'help-block text-danger',
                                        'wrap'  => 'span'
                                    ]) ?>
                            </div>
                        </div>
                    </div>
                    <?php // dummy hide submit button for html5 validation ?>
                    <?= $this->Form->submit(__(""),
                        ['class' => 'none', 'div' => false, 'id' => 'EditCircleFormSubmit']) ?>
                    <?= $this->Form->end(); ?>
                </div>

                <div class="tab-pane fade" id="tab2">
                    <?php if ($circle_members): ?>
                        <div class="row borderBottom" style="padding-bottom:50px">
                            <?php foreach ($circle_members as $user): ?>
                                <div class="col col-xxs-12 mpTB0" id="edit-circle-member-row-<?= h($user['User']['id']) ?>">
                                    <div class="pull-right">
                                        <a class="btn btn-link btn-lightGray btn-white bd-radius_4px"
                                           data-toggle="dropdown">
                                            <i class="fa fa-cog"></i>
                                        </a>
                                        <ul class="dropdown-menu dropdown-menu-right frame-arrow-icon" role="menu">
                                            <li>
                                                <?=
                                                // 非管理者 -> 管理者 設定フォーム
                                                $this->Form->create('CircleMember', [
                                                    'url'          => [
                                                        'controller' => 'circles',
                                                        'action'     => 'ajax_edit_admin_status',
                                                        'circle_id'  => $this->request->data['Circle']['id']
                                                    ],
                                                    'class'        => 'ajax-edit-circle-admin-status',
                                                    'id'           => 'EditAdminStatusForm1_' . $user['User']['id'],
                                                    'type'         => 'post',
                                                    'data-user-id' => $user['User']['id'],
                                                ]); ?>
                                                <?= $this->Form->hidden('user_id', ['value' => $user['User']['id']]) ?>
                                                <?= $this->Form->hidden('admin_flg', ['value' => 1]) ?>
                                                <?= $this->Form->end() ?>
                                                <?= $this->Html->link(__("Set as Admin"), '#',
                                                    [
                                                        'class'   => 'item-for-non-admin',
                                                        'style'   => $user['CircleMember']['admin_flg'] ? 'display:none' : '',
                                                        'onclick' => "$('#EditAdminStatusForm1_{$user['User']['id']}').submit(); return false;"
                                                    ]) ?>

                                                <?=
                                                // 管理者 -> 非管理者 設定フォーム
                                                $this->Form->create('CircleMember', [
                                                    'url'          => [
                                                        'controller' => 'circles',
                                                        'action'     => 'ajax_edit_admin_status',
                                                        'circle_id'  => $this->request->data['Circle']['id']
                                                    ],
                                                    'class'        => 'ajax-edit-circle-admin-status',
                                                    'id'           => 'EditAdminStatusForm2_' . $user['User']['id'],
                                                    'type'         => 'post',
                                                    'data-user-id' => $user['User']['id'],
                                                ]); ?>
                                                <?= $this->Form->hidden('user_id', ['value' => $user['User']['id']]) ?>
                                                <?= $this->Form->hidden('admin_flg', ['value' => 0]) ?>
                                                <?= $this->Form->end() ?>

                                                <?php
                                                // 管理者から外すボタンを押した時の処理
                                                // 操作者自身を管理者から外す際はアラートを出す
                                                $onclick = "$('#EditAdminStatusForm2_{$user['User']['id']}').submit(); return false;";
                                                if ($this->Session->read('Auth.User.id') == $user['User']['id']) {
                                                    $onclick =
                                                        "if (confirm('" .
                                                        __('After quitting circle admin, you can\'t edit circle information. Do you really want to quit admin?') .
                                                        "')) { $('#EditAdminStatusForm2_{$user['User']['id']}').submit(); } return false;";
                                                }
                                                ?>
                                                <?= $this->Html->link(__("Remove from admin"), '#',
                                                    [
                                                        'class'   => 'item-for-admin',
                                                        'style'   => $user['CircleMember']['admin_flg'] ? '' : 'display:none',
                                                        'onclick' => $onclick
                                                    ]); ?>

                                            </li>
                                            <?php if (!$this->request->data['Circle']['team_all_flg']): ?>
                                                <li>
                                                    <?php
                                                    // サークルから外すボタンを押した時の処理
                                                    // 操作者自身をサークルから外す際はアラートを出す
                                                    $onclick = "$('#LeaveCircleForm_{$user['User']['id']}').submit(); return false;";
                                                    if ($this->Session->read('Auth.User.id') == $user['User']['id']) {
                                                        $onclick =
                                                            "if (confirm('" .
                                                            __('After leaving this circle, you can\'t edit any circle setting. Do you really want to leave the circle?') .
                                                            "')) { $('#LeaveCircleForm_{$user['User']['id']}').submit(); } return false;";
                                                    }
                                                    ?>
                                                    <?=
                                                    // サークルから外す 設定フォーム
                                                    $this->Form->create('CircleMember', [
                                                        'url'          => [
                                                            'controller' => 'circles',
                                                            'action'     => 'ajax_leave_circle',
                                                            'circle_id'  => $this->request->data['Circle']['id']
                                                        ],
                                                        'class'        => 'ajax-leave-circle',
                                                        'id'           => 'LeaveCircleForm_' . $user['User']['id'],
                                                        'type'         => 'post',
                                                        'data-user-id' => $user['User']['id'],
                                                    ]); ?>
                                                    <?= $this->Form->hidden('user_id', ['value' => $user['User']['id']]) ?>
                                                    <?= $this->Form->end() ?>
                                                    <?= $this->Html->link(__("Remove from the circle"), '#',
                                                        ['onclick' => $onclick]) ?>
                                                </li>
                                            <?php endif ?>
                                        </ul>
                                    </div>
                                    <?=
                                    $this->Upload->uploadImage($user['User'], 'User.photo', ['style' => 'medium_large'],
                                        ['class' => 'comment-img'])
                                    ?>
                                    <div class="comment-body modal-comment">
                                        <div class="font_12px font_bold modalFeedTextPadding">
                                            <?= h($user['User']['display_username']) ?>
                                            <i class="fa fa-adn ng-scope item-for-admin"
                                               <?php if (!$user['CircleMember']['admin_flg']): ?>style="display:none"<?php endif ?>></i>
                                        </div>
                                        <?php if ($user['CircleMember']['modified']): ?>
                                            <div class="font_12px font_lightgray modalFeedTextPaddingSmall">
                                                <?= $this->TimeEx->elapsedTime(h($user['CircleMember']['modified']),
                                                    'rough') ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach ?>
                        </div>
                    <?php else: ?>
                        <?= __("No one in this circle.") ?>
                    <?php endif ?>
                </div>

                <?php if (!$this->request->data['Circle']['team_all_flg']): ?>
                    <div class="tab-pane fade" id="tab3">
                        <?=
                        // メンバー追加フォーム
                        $this->Form->create('Circle', [
                            'url'        => [
                                'controller' => 'circles',
                                'action'     => 'add_member',
                                'circle_id'  => $this->request->data['Circle']['id']
                            ],
                            'class'      => 'form-horizontal',
                            'novalidate' => true,
                            'id'         => 'AddCircleMemberForm',
                            'type'       => 'put',
                        ]); ?>
                        <?= $this->Form->hidden('id') ?>
                        <div class="form-group">
                            <label class="circle-create-label"><?= __('Members') ?></label>

                            <div class="bbb">
                                <?=
                                $this->Form->hidden('members',
                                    [
                                        'class'    => 'ajax_add_select2_members disable-change-warning',
                                        'value'    => null,
                                        'style'    => "width: 100%",
                                        'data-url' => $this->Html->url(
                                            [
                                                'controller' => 'circles',
                                                'action'     => 'ajax_select2_non_circle_member',
                                                'circle_id'  => $this->request->data['Circle']['id']
                                            ])
                                    ]) ?>
                                <?php $this->Form->unlockField('Circle.members') ?>
                            </div>
                        </div>
                        <?= $this->Form->end(); ?>
                    </div>
                <?php endif ?>
            </div>

            <div class="modal-footer tab1-footer">
                <?=
                $this->Form->button(__("Save changes"),
                    [
                        'id'      => 'EditCircleFormSubmit',
                        'class'   => 'btn btn-primary pull-right',
                        'onclick' => "document.getElementById('EditCircleFormSubmit').click();",
                        'div'     => false,
                    ]) ?>
                <a href="/circles/<?= $circleId?>/about" class="btn btn-link design-cancel pull-right mr_8px bd-radius_4px">
                    <?= __("Cancel") ?>
                </a>
                <?php if (!$this->request->data['Circle']['team_all_flg']): ?>
                    <?=
                    $this->Form->postLink(__("Delete circle"),
                        [
                            'controller' => 'circles',
                            'action'     => 'delete',
                            'circle_id'  => $this->request->data['Circle']['id']
                        ],
                        ['class' => 'btn btn-default pull-left'],
                        __("Do you really want to delete the circle?")) ?>
                <?php endif ?>
            </div>

            <div class="modal-footer tab2-footer" style="display:none">
            </div>

            <?php if (!$this->request->data['Circle']['team_all_flg']): ?>
                <div class="modal-footer tab3-footer" style="display:none">
                    <?=
                    $this->Form->button(__("Add member(s)"),
                        [
                            'id' => 'AddCircleMemberFormSubmit',
                            'class'   => 'btn btn-primary pull-right',
                            'onclick' => "document.getElementById('AddCircleMemberForm').submit();",
                            'div'     => false,
                        ]) ?>
                </div>
            <?php endif ?>
        </div>
    </div>
</div>
<?php
// ↓ This is for temporary process because we gonna renewal circle create/edit feature in near future
?>
<script type="text/javascript">
    $(function () {
        var tab = "<?= $tab ?>";
        if (tab) {
            $('#' + tab + 'Tab').trigger('click');
        }
    });
</script>
<?= $this->App->viewEndComment()?>
