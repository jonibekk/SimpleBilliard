<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 6/6/14
 * Time: 3:19 PM
 *
 * @var View $this
 * @var      $my_member_status
 */
?>
<!-- START app/View/Elements/modal_edit_circle.ctp -->
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close font_33px close-design" data-dismiss="modal" aria-hidden="true"><span
                    class="close-icon">&times;</span></button>
            <h4 class="modal-title"><?= __d('gl', "サークルを編集") ?></h4>
        </div>
        <?=
        $this->Form->create('Circle', [
            'url'           => ['controller' => 'circles', 'action' => 'edit', $this->request->data['Circle']['id']],
            'inputDefaults' => [
                'div'       => 'form-group',
                'label'     => [
                    'class' => 'col col-sm-3 modal-label pr_12px'
                ],
                'wrapInput' => 'col col-sm-6',
                'class'     => 'form-control modal_input-design'
            ],
            'class'         => 'form-horizontal',
            'novalidate'    => true,
            'type'          => 'file',
            'id'            => 'EditCircleForm',
        ]); ?>
        <?= $this->Form->hidden('id') ?>
        <div class="modal-body modal-circle-body">
            <?=
            $this->Form->input('name',
                               ['label'                    => __d('gl', "サークル名"),
                                'placeholder'              => __d('gl', "例) 営業部"),
                                "data-bv-notempty-message" => __d('validate', "入力必須項目です。"),
                               ]) ?>
            <hr>
            <div class="form-group">
                <label class="col col-sm-3 control-label modal-label"><?= __d('gl', 'メンバー') ?></label>

                <div class="col col-sm-6">
                    <?=
                    $this->Form->hidden('members',
                                        ['class' => 'ajax_add_select2_members', 'value' => $this->request->data['Circle']['members'], 'style' => "width: 100%", 'circle_id' => $this->request->data['Circle']['id']]) ?>
                    <? $this->Form->unlockField('Circle.members') ?>
                    <span class="help-block font_11px"><?=
                        __d('gl', "管理者：%s",
                            $this->Session->read('Auth.User.display_username')) ?></span>
                </div>
            </div>
            <hr>
            <?
            $privacy_option = Circle::$TYPE_PUBLIC;
            $privacy_option[Circle::TYPE_PUBLIC_ON] .= '<span class="help-block font_11px">' . __d('gl',
                                                                                                   "サークル名と参加メンバー、投稿がチーム内に公開されます。チームメンバーは誰でも自由に参加できます。") . '</span>';
            $privacy_option[Circle::TYPE_PUBLIC_OFF] .= '<span class="help-block font_11px">' . __d('gl',
                                                                                                    "サークル名と参加メンバー、投稿はこのサークルの参加メンバーだけに表示されます。サークル管理者だけがメンバーを追加できます。") . '</span>';
            ?>
            <?php echo $this->Form->input('public_flg', array(
                'type'    => 'radio',
                'before'  => '<label class="col col-sm-3 control-label modal-label">' . __d('gl',
                                                                                            'プライバシー') . '</label>',
                'legend'  => false,
                'class'   => false,
                'options' => $privacy_option,
            )); ?>
            <hr>
            <?=
            $this->Form->input('description',
                               ['label'       => __d('gl', "サークルの説明"),
                                'placeholder' => __d('gl', "例) 最新情報を共有しましょう。"),
                                'rows'        => 1,
                               ]) ?>
            <hr>
            <div class="form-group">
                <label for="" class="col col-sm-3 control-label modal-label"><?= __d('gl', "サークル画像") ?></label>

                <div class="col col-sm-6">
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

        </div>

        <div class="modal-footer">
            <?=
            $this->Form->submit(__d('gl', "変更を保存"),
                                ['class' => 'btn btn-primary pull-right', 'div' => false,]) ?>
            <?= $this->Form->end(); ?>
            <button type="button" class="btn btn-link design-cancel pull-right mr_8px bd-radius_4px"
                    data-dismiss="modal"><?= __d('gl', "キャンセル") ?></button>
            <?=
            $this->Form->postLink(__d('gl', "サークルを削除"),
                                  ['controller' => 'circles', 'action' => 'delete', $this->request->data['Circle']['id']],
                                  ['class' => 'btn btn-default pull-left'], __d('gl', "本当にこのサークルを削除しますか？")) ?>

        </div>
    </div>
</div>
<!-- END app/View/Elements/modal_edit_circle.ctp -->
