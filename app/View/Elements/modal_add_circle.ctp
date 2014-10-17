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
<!-- START app/View/Elements/modal_add_circle.ctp -->
<div class="modal fade" tabindex="-1" id="modal_add_circle">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close font_33px close-design" data-dismiss="modal" aria-hidden="true">
                    <span class="close-icon">&times;</span></button>
                <h4 class="modal-title"><?= __d('gl', "サークルを作成") ?></h4>
            </div>
            <?=
            $this->Form->create('Circle', [
                'url'           => ['controller' => 'circles', 'action' => 'add'],
                'inputDefaults' => [
                    'div'       => 'form-group',
                    'label'     => [
                        'class' => 'col col-sm-3 control-label modal-label'
                    ],
                    'wrapInput' => 'col col-sm-6',
                    'class'     => 'form-control modal_input-design'
                ],
                'class'         => 'form-horizontal',
                'novalidate'    => true,
                'type'          => 'file',
                'id'            => 'AddCircleForm',
            ]); ?>
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
                                            ['id' => 'select2Member', 'value' => null, 'style' => "width: 100%",]) ?>
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
                    'default' => Circle::TYPE_PUBLIC_ON,
                )); ?>
                <hr>
                <?=
                $this->Form->input('description',
                                   ['label'       => __d('gl', "サークルの説明"),
                                    'placeholder' => __d('gl', "例) 最新情報を共有しましょう。"),
                                   ]) ?>
                <hr>
                <div class="form-group">
                    <label for="" class="col col-sm-3 control-label modal-label"><?= __d('gl', "サークル画像") ?></label>

                    <div class="col col-sm-6">
                        <div class="fileinput_small fileinput-new" data-provides="fileinput">
                            <div class="fileinput-preview thumbnail nailthumb-container photo-design"
                                 data-trigger="fileinput" style="width: 96px; height: 96px; line-height:96px;">
                                <i class="fa fa-plus photo-plus-large"></i>
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
                                                ''
                                               ]) ?>
                        </span>
                                <span class="help-block font_11px disp_ib"><?= __d('gl',
                                                                                              '10MB以下') ?></span>
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

            <div class="modal-footer addcircle_pannel-footer">
                <div class="row">
                    <div class="col-sm-9 col-sm-offset-3">
                        <button type="button" class="btn btn-link design-cancel bd-radius_4px"
                                data-dismiss="modal"><?= __d('gl',
                                                             "キャンセル") ?></button>
                        <?=
                        $this->Form->submit(__d('gl', "サークルを作成"),
                                            ['class' => 'btn btn-primary', 'div' => false, 'disabled' => 'disabled']) ?>

                    </div>
                </div>
            </div>
            <?= $this->Form->end(); ?>
        </div>
    </div>
</div>
<? $this->append('script') ?>
<script type="text/javascript">
    $(document).ready(function () {
        $('#AddCircleForm').bootstrapValidator({
            excluded: [':disabled'],
            live: 'enabled',
            feedbackIcons: {
                valid: 'fa fa-check',
                invalid: 'fa fa-times',
                validating: 'fa fa-refresh'
            },
            fields: {
                "data[Circle][photo]": {
                    enabled: false
                }
            }
        });
    });
</script>
<? $this->end() ?>
<!-- END app/View/Elements/modal_add_circle.ctp -->
