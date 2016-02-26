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
<!-- START app/View/Elements/modal_add_circle.ctp -->
<div class="modal fade" tabindex="-1" id="modal_add_circle">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close font_33px close-design" data-dismiss="modal" aria-hidden="true">
                    <span class="close-icon">&times;</span></button>
                <h4 class="modal-title"><?= __d('app', "サークルを作成") ?></h4>
            </div>
            <?=
            $this->Form->create('Circle', [
                'url'           => ['controller' => 'circles', 'action' => 'add'],
                'inputDefaults' => [
                    'div'       => 'form-group',
                    'label'     => [
                        'class' => 'aeiou control-label modal-label'
                    ],
                    'wrapInput' => false,
                    'class'     => 'form-control modal_input-design',
                ],
                'class'         => 'form-horizontal',
                'novalidate'    => true,
                'type'          => 'file',
                'id'            => 'AddCircleForm',
            ]); ?>
            <div class="modal-body modal-circle-body">
                <?=
                $this->Form->input('name',
                                   ['label'                        => __d('app', "サークル名"),
                                    'placeholder'                  => __d('app', "例) 営業部"),
                                    "data-bv-notempty-message"     => __d('validate', "入力必須項目です。"),
                                    'data-bv-stringlength'         => 'true',
                                    'data-bv-stringlength-max'     => 128,
                                    'data-bv-stringlength-message' => __d('validate', "最大文字数(%s)を超えています。", 128),
                                    'required'                     => true,
                                   ]) ?>
                <div class="form-group">
                    <label class="ccc control-label modal-label"><?= __d('app', 'メンバー') ?></label>

                    <div class="ddd">
                        <?=
                        $this->Form->hidden('members', ['class'    => 'ajax_add_select2_members',
                                                        'value'    => null,
                                                        'style'    => "width: 100%",
                                                        'data-url' => $this->Html->url(
                                                            ['controller' => 'users',
                                                             'action'     => 'ajax_select2_get_users',
                                                             '?'          => ['with_group' => '1']
                                                            ])
                        ]) ?>
                        <?php $this->Form->unlockField('Circle.members') ?>
                        <span class="help-block font_11px"><?=
                            __d('app', "管理者：%s",
                                h($this->Session->read('Auth.User.display_username'))) ?></span>
                    </div>
                </div>
                <?php $privacy_option = Circle::$TYPE_PUBLIC;
                $privacy_option[Circle::TYPE_PUBLIC_ON] .= '<span class="help-block font_11px">' . __d('app',
                                                                                                       "サークル名と参加メンバー、投稿がチーム内に公開されます。チームメンバーは誰でも自由に参加できます。") . '</span>';
                $privacy_option[Circle::TYPE_PUBLIC_OFF] .= '<span class="help-block font_11px">' . __d('app',
                                                                                                        "サークル名と参加メンバー、投稿はこのサークルの参加メンバーだけに表示されます。サークル管理者だけがメンバーを追加できます。") . '</span>';
                ?>
                <?php echo $this->Form->input('public_flg', array(
                    'type'     => 'radio',
                    'before'   => '<label class="eee control-label modal-label">' . __d('app',
                                                                                        'プライバシー') . '</label>',
                    'legend'   => false,
                    'class'    => false,
                    'options'  => $privacy_option,
                    'default'  => Circle::TYPE_PUBLIC_ON,
                    'required' => false
                )); ?>
                <div class="font_brownRed font_11px">
                    <?= __d('app', 'この設定は後で変更できません') ?>
                </div>
                <?=
                $this->Form->input('description',
                                   ['label'                        => __d('app', "サークルの説明"),
                                    'placeholder'                  => __d('app', "例) 最新情報を共有しましょう。"),
                                    'data-bv-notempty-message'     => __d('validate', "入力必須項目です。"),
                                    'data-bv-stringlength'         => 'true',
                                    'data-bv-stringlength-max'     => 2000,
                                    'data-bv-stringlength-message' => __d('validate', "最大文字数(%s)を超えています。", 2000),
                                   ]) ?>
                <div class="form-group">
                    <label for="" class="f control-label modal-label"><?= __d('app', "サークル画像") ?></label>

                    <div class="ggg">
                        <div class="fileinput_small fileinput-new" data-provides="fileinput">
                            <div class="fileinput-preview thumbnail nailthumb-container photo-design"
                                 data-trigger="fileinput" style="width: 96px; height: 96px; line-height:96px;">
                                <i class="fa fa-plus photo-plus-large"></i>
                            </div>
                            <span class="btn btn-default btn-file">
                                <span class="fileinput-new">
                                    <?= __d('app', "画像を選択") ?>
                                </span>
                                <span class="fileinput-exists"><?= __d('app', "画像を再選択") ?></span>
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
                                    <span class="help-block font_11px inline-block"><?= __d('app',
                                                                                            '10MB以下') ?></span>
                        </div>
                    </div>
                    <div>

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
                    <div class="h">
                        <button type="button" class="btn btn-link design-cancel bd-radius_4px"
                                data-dismiss="modal"><?= __d('app',
                                                             "キャンセル") ?></button>
                        <?=
                        $this->Form->submit(__d('app', "サークルを作成"),
                                            ['class' => 'btn btn-primary', 'div' => false, 'disabled' => 'disabled']) ?>

                    </div>
                </div>
            </div>
            <?= $this->Form->end(); ?>
        </div>
    </div>
</div>
<?php $this->append('script') ?>
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
                    feedbackIcons: 'false',
                    validators: {
                        file: {
                            extension: 'jpeg,jpg,png,gif',
                            type: 'image/jpeg,image/png,image/gif',
                            maxSize: 10485760,   // 10mb
                            message: "<?=__d('validate', "10MB以下かつJPG、PNG、GIFのいずれかの形式を選択して下さい。")?>"
                        }
                    }
                }
            }
        });
    });
</script>

<?php $this->end() ?>
<!-- END app/View/Elements/modal_add_circle.ctp -->
