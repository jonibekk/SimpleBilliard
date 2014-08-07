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
<div class="modal fade" id="modal_add_circle">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title"><?= __d('gl', "サークルを作成") ?></h4>
            </div>
            <?=
            $this->Form->create('Circle', [
                'url'           => ['controller' => 'circles', 'action' => 'add'],
                'inputDefaults' => [
                    'div'       => 'form-group',
                    'label'     => [
                        'class' => 'col col-sm-3 control-label'
                    ],
                    'wrapInput' => 'col col-sm-6',
                    'class' => 'form-control modal_input-design'
                ],
                'class'         => 'form-horizontal',
                'novalidate'    => true,
                'type'          => 'file',
                'id'            => 'AddCircleForm',
            ]); ?>
            <div class="modal-body">
                <?=
                $this->Form->input('name',
                                   ['label'                    => __d('gl', "サークル名"),
                                    'placeholder'              => __d('gl', "例) 営業部"),
                                    "data-bv-notempty-message" => __d('validate', "入力必須項目です。"),
                                   ]) ?>
                <hr>
                <div class="form-group">
                    <label class="col col-sm-3 control-label"><?= __d('gl', 'メンバー') ?></label>

                    <div class="col col-sm-6">
                        <?=
                        $this->Form->hidden('members',
                                            ['id' => 'select2Member', 'value' => null, 'style' => "width: 100%",]) ?>
                        <? $this->Form->unlockField('Circle.members') ?>
                        <span class="help-block"><?=
                            __d('gl', "管理者：%s",
                                $this->Session->read('Auth.User.display_username')) ?></span>
                    </div>
                </div>
                <hr>
                <?
                $privacy_option = Circle::$TYPE_PUBLIC;
                $privacy_option[Circle::TYPE_PUBLIC_ON] .= '<span class="help-block">' . __d('gl',
                                                                                             "サークル名と参加メンバー、投稿がチーム内に公開されます。チームメンバーは誰でも自由に参加できます。") . '</span>';
                $privacy_option[Circle::TYPE_PUBLIC_OFF] .= '<span class="help-block">' . __d('gl',
                                                                                              "サークル名と参加メンバー、投稿はこのサークルの感化メンバーだけに表示されます。サークル管理者だけがメンバーを追加できます。") . '</span>';
                if (isset($my_member_status['TeamMember']['admin_flg']) && $my_member_status['TeamMember']['admin_flg']) {
                    //管理者の場合はデフォルトがon
                    $default = Circle::TYPE_PUBLIC_ON;
                    $disabled = "";
                }
                else {
                    $default = Circle::TYPE_PUBLIC_OFF;
                    $disabled = "disabled";
                }
                ?>
                <?php echo $this->Form->input('public_flg', array(
                    'type'    => 'radio',
                    'before'  => '<label class="col col-md-3 control-label">' . __d('gl', 'プライバシー') . '</label>',
                    'legend'  => false,
                    'class'   => false,
                    'options' => $privacy_option,
                    'default' => $default,
                    $disabled => $disabled,
                )); ?>
                <hr>
                <?=
                $this->Form->input('description',
                                   ['label'       => __d('gl', "サークルの説明"),
                                    'placeholder' => __d('gl', "例) 最新情報を共有しましょう。"),
                                   ]) ?>
                <hr>
                <div class="form-group">
                    <label for="" class="col col-sm-3 control-label"><?= __d('gl', "サークル画像") ?></label>

                    <div class="col col-sm-6">
                        <div class="fileinput fileinput-new" data-provides="fileinput">
                            <div class="fileinput-preview thumbnail nailthumb-container addcircle_photo-design"
                                 data-trigger="fileinput"
                                 style="width: 150px; height: 150px;">
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
                            </div>
                        </div>
                        <span class="help-block"><?= __d('gl', '10MB以下') ?></span>

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
                        <?=
                        $this->Form->submit(__d('gl', "サークルを作成"),
                                            ['class' => 'btn btn-primary', 'div' => false,]) ?>
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
