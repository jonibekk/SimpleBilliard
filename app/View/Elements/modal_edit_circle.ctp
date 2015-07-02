<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 6/6/14
 * Time: 3:19 PM
 *
 * @var CodeCompletionView $this
 * @var                    $my_member_status
 */
?>
<!-- START app/View/Elements/modal_edit_circle.ctp -->
<div class="modal-dialog edit-circle">
    <div class="modal-content">
        <div class="modal-header none-border">
            <button type="button" class="close font_33px close-design" data-dismiss="modal" aria-hidden="true"><span
                    class="close-icon">&times;</span></button>
            <h4 class="modal-title"><?= __d('gl', "サークルを編集") ?></h4>
        </div>
        <ul class="nav nav-tabs">
            <li class="active"><a href="#tab1" data-toggle="tab"><?= __d('gl', "基本情報") ?></a></li>
            <li><a href="#tab2" data-toggle="tab"><?= __d('gl', "メンバー一覧") ?></a></li>
            <?php if (!$this->request->data['Circle']['team_all_flg']): ?>
                <li><a href="#tab3" data-toggle="tab"><?= __d('gl', "メンバー追加") ?></a></li>
            <?php endif ?>
        </ul>

        <div class="modal-body modal-circle-body tab-content">
            <div class="tab-pane fade in active" id="tab1">
                <?=
                // サークル基本情報変更フォーム
                $this->Form->create('Circle', [
                    'url'           => ['controller' => 'circles', 'action' => 'edit', 'circle_id' => $this->request->data['Circle']['id']],
                    'inputDefaults' => [
                        'div'       => 'form-group',
                        'label'     => [
                            'class' => 'modal-label pr_12px'
                        ],
                        'wrapInput' => false,
                        'class'     => 'form-control modal_input-design'
                    ],
                    'class'         => 'form-horizontal',
                    'novalidate'    => true,
                    'type'          => 'file',
                    'id'            => 'EditCircleForm',
                ]); ?>
                <?= $this->Form->hidden('id') ?>
                <?=
                $this->Form->input('name',
                                   ['label'                    => __d('gl', "サークル名"),
                                    'placeholder'              => __d('gl', "例) 営業部"),
                                    "data-bv-notempty-message" => __d('validate', "入力必須項目です。"),
                                   ]) ?>
                <?php if (!$this->request->data['Circle']['team_all_flg']): ?>

                    <?php $privacy_option = Circle::$TYPE_PUBLIC;
                    $privacy_option[Circle::TYPE_PUBLIC_ON] .= '<span class="help-block font_11px">' . __d('gl',
                                                                                                           "サークル名と参加メンバー、投稿がチーム内に公開されます。チームメンバーは誰でも自由に参加できます。") . '</span>';
                    $privacy_option[Circle::TYPE_PUBLIC_OFF] .= '<span class="help-block font_11px">' . __d('gl',
                                                                                                            "サークル名と参加メンバー、投稿はこのサークルの参加メンバーだけに表示されます。サークル管理者だけがメンバーを追加できます。") . '</span>';
                    ?>
                    <?php echo $this->Form->input('public_flg', array(
                        'type'     => 'radio',
                        'before'   => '<label class="control-label modal-label">' . __d('gl',
                                                                                        'プライバシー') . '</label>',
                        'legend'   => false,
                        'class'    => false,
                        'options'  => $privacy_option,
                        'required' => false,
                    )); ?>
                <?php endif ?>
                <?=
                $this->Form->input('description',
                                   ['label'       => __d('gl', "サークルの説明"),
                                    'placeholder' => __d('gl', "例) 最新情報を共有しましょう。"),
                                    'rows'        => 1,
                                   ]) ?>
                <div class="form-group">
                    <label for="" class="control-label modal-label"><?= __d('gl', "サークル画像") ?></label>

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
                                <?=
                                __d('gl',
                                    "画像を選択") ?>
                            </span>
                            <span class="fileinput-exists"><?= __d('gl', "画像を再選択") ?></span>
                            <?=
                            $this->Form->input('photo',
                                               ['type'         => 'file',
                                                'label'        => false,
                                                'div'          => false,
                                                'css'          => false,
                                                'wrapInput'    => false,
                                                'errorMessage' => false,
                                                'required'     => false
                                               ]) ?>
                        </span>
                                <span class="help-block font_11px inline-block"><?= __d('gl', '10MB以下') ?></span>
                            </div>
                        </div>

                        <div class="has-error">
                            <?=
                            $this->Form->error('photo', null,
                                               ['class' => 'help-block text-danger',
                                                'wrap'  => 'span'
                                               ]) ?>
                        </div>
                    </div>
                </div>
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
                                                'url'          => ['controller' => 'circles',
                                                                   'action'     => 'ajax_edit_admin_status',
                                                                   'circle_id'  => $this->request->data['Circle']['id']],
                                                'class'        => 'ajax-edit-circle-admin-status',
                                                'id'           => 'EditAdminStatusForm1_' . $user['User']['id'],
                                                'type'         => 'post',
                                                'data-user-id' => $user['User']['id'],
                                            ]); ?>
                                            <?= $this->Form->hidden('user_id', ['value' => $user['User']['id']]) ?>
                                            <?= $this->Form->hidden('admin_flg', ['value' => 1]) ?>
                                            <?= $this->Form->end() ?>
                                            <?= $this->Html->link(__d('gl', "管理者にする"), '#',
                                                                  ['class'   => 'item-for-non-admin',
                                                                   'style'   => $user['CircleMember']['admin_flg'] ? 'display:none' : '',
                                                                   'onclick' => "$('#EditAdminStatusForm1_{$user['User']['id']}').submit(); return false;"]) ?>

                                            <?=
                                            // 管理者 -> 非管理者 設定フォーム
                                            $this->Form->create('CircleMember', [
                                                'url'          => ['controller' => 'circles',
                                                                   'action'     => 'ajax_edit_admin_status',
                                                                   'circle_id'  => $this->request->data['Circle']['id']],
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
                                                    __d('gl', '自分自身を管理者から外すとサークル情報の編集が出来なくなりますが、よろしいですか？') .
                                                    "')) { $('#EditAdminStatusForm2_{$user['User']['id']}').submit(); } return false;";
                                            }
                                            ?>
                                            <?= $this->Html->link(__d('gl', "管理者から外す"), '#',
                                                                  ['class'   => 'item-for-admin',
                                                                   'style'   => $user['CircleMember']['admin_flg'] ? '' : 'display:none',
                                                                   'onclick' => $onclick]); ?>

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
                                                        __d('gl', '自分自身をサークルから外すとサークル情報の編集が出来なくなりますが、よろしいですか？') .
                                                        "')) { $('#LeaveCircleForm_{$user['User']['id']}').submit(); } return false;";
                                                }
                                                ?>
                                                <?=
                                                // サークルから外す 設定フォーム
                                                $this->Form->create('CircleMember', [
                                                    'url'          => ['controller' => 'circles',
                                                                       'action'     => 'ajax_leave_circle',
                                                                       'circle_id'  => $this->request->data['Circle']['id']],
                                                    'class'        => 'ajax-leave-circle',
                                                    'id'           => 'LeaveCircleForm_' . $user['User']['id'],
                                                    'type'         => 'post',
                                                    'data-user-id' => $user['User']['id'],
                                                ]); ?>
                                                <?= $this->Form->hidden('user_id', ['value' => $user['User']['id']]) ?>
                                                <?= $this->Form->end() ?>
                                                <?= $this->Html->link(__d('gl', "サークルから外す"), '#', ['onclick' => $onclick]) ?>
                                            </li>
                                        <?php endif ?>
                                    </ul>
                                </div>
                                <?=
                                $this->Upload->uploadImage($user['User'], 'User.photo', ['style' => 'small'],
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
                    <?= __d('gl', "このサークルにはメンバーがいません。") ?>
                <?php endif ?>
            </div>

            <?php if (!$this->request->data['Circle']['team_all_flg']): ?>
                <div class="tab-pane fade" id="tab3">
                    <?=
                    // メンバー追加フォーム
                    $this->Form->create('Circle', [
                        'url'        => ['controller' => 'circles',
                                         'action'     => 'add_member',
                                         'circle_id'  => $this->request->data['Circle']['id']],
                        'class'      => 'form-horizontal',
                        'novalidate' => true,
                        'id'         => 'AddCircleMemberForm',
                        'type'       => 'put',
                    ]); ?>
                    <?= $this->Form->hidden('id') ?>
                    <div class="form-group">
                        <label class="control-label modal-label"><?= __d('gl', 'メンバー') ?></label>

                        <div class="bbb">
                            <?=
                            $this->Form->hidden('members',
                                                ['class'    => 'ajax_add_select2_members',
                                                 'value'    => null,
                                                 'style'    => "width: 100%",
                                                 'data-url' => $this->Html->url(
                                                     ['controller' => 'circles',
                                                      'action'     => 'ajax_select2_non_circle_member',
                                                      'circle_id'  => $this->request->data['Circle']['id']])
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
            $this->Form->button(__d('gl', "変更を保存"),
                                ['class'   => 'btn btn-primary pull-right',
                                 'onclick' => "document.getElementById('EditCircleForm').submit(); return false;",
                                 'div'     => false,]) ?>
            <button type="button" class="btn btn-link design-cancel pull-right mr_8px bd-radius_4px"
                    data-dismiss="modal"><?= __d('gl', "キャンセル") ?></button>
            <?php if (!$this->request->data['Circle']['team_all_flg']): ?>
                <?=
                $this->Form->postLink(__d('gl', "サークルを削除"),
                                      ['controller' => 'circles',
                                       'action'     => 'delete',
                                       'circle_id'  => $this->request->data['Circle']['id']],
                                      ['class' => 'btn btn-default pull-left'],
                                      __d('gl', "本当にこのサークルを削除しますか？")) ?>
            <?php endif ?>
        </div>

        <div class="modal-footer tab2-footer" style="display:none">
        </div>

        <?php if (!$this->request->data['Circle']['team_all_flg']): ?>
            <div class="modal-footer tab3-footer" style="display:none">
                <?=
                $this->Form->button(__d('gl', "メンバー追加"),
                                    ['class'   => 'btn btn-primary pull-right',
                                     'onclick' => "document.getElementById('AddCircleMemberForm').submit(); return false;",
                                     'div'     => false,
                                    ]) ?>
            </div>
        <?php endif ?>

    </div>
</div>
<!-- END app/View/Elements/modal_edit_circle.ctp -->
