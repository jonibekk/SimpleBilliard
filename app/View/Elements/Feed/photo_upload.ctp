<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 7/22/14
 * Time: 11:05 AM
 *
 * @var                    $index
 * @var                    $submit_id
 * @var                    $data
 * @var                    $type
 * @var CodeCompletionView $this
 */
$model = null;
if (isset($type)) {
    switch ($type) {
        case "post":
            $model = "Post";
            break;
        case "comment":
            $model = "Comment";
            break;
        default:
            $model = "User";
            break;
    }
}
?>
<!-- START app/View/Elements/Feed/photo_upload.ctp -->
<div class="form-group">
    <div class="fileinput_post_comment fileinput-new" data-provides="fileinput">
        <div
            class="fileinput-preview thumbnail nailthumb-container gl-thumb-container click-target-enabled photo-plus-frame"
            data-trigger="fileinput"
             target-id="<?= $submit_id ?>"
             style="width: 50px; height: 50px;">
            <? $delete_style = null ?>
            <? if (isset($data[$model]["photo{$index}_file_name"]) && $data[$model]["photo{$index}_file_name"]): ?>
                <? $delete_style = "display:block" ?>
                <?=
                $this->Html->image('ajax-loader.gif',
                                   [
                                       'class'         => 'lazy',
                                       'style'         => 'width: 50px; height: 50px;',
                                       'data-original' => $this->Upload->uploadUrl($data, "{$model}.photo" . $index,
                                                                                   ['style' => 'small'])
                                   ]
                )
                ?>
            <? endif ?><? if ($index == 1): ?><i class="fa fa-plus photo-plus"></i><? endif; ?>
        </div>
        <? if (isset($data[$model]["photo{$index}_file_name"]) && $data[$model]["photo{$index}_file_name"]): ?>
            <div class="gl-custom-wrapper">
                <?=
                $this->Form->input('photo_delete.' . $index,
                                   [
                                       'type'         => 'checkbox',
                                       'label'        => "",
                                       'div'          => false,
                                       'class'        => 'gl-custom-radio-check change-target-enabled',
                                       'target-id'    => $submit_id,
                                       'wrapInput'    => false,
                                       'errorMessage' => false,
                                       'id'           => $model . "PhotoDelete" . $index
                                   ]
                )?>
            </div>
        <? endif; ?>
        <div>
                        <span class="btn-file">
                            <?=
                            $this->Form->input('photo' . $index,
                                               ['type'         => 'file',
                                                'label'        => false,
                                                'div'          => false,
                                                //TODO ios画像アップロードのデバッグ
                                                'class' => 'change_photo_input',
                                                'wrapInput'    => false,
                                                'errorMessage' => false,
                                               ]) ?>
                        </span>
        </div>
    </div>
</div>
<!-- END app/View/Elements/Feed/photo_upload.ctp -->
