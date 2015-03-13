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
 * @var                    $post_id
 * @var                    $type
 * @var CodeCompletionView $this
 * @var                    $id_prefix
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
        case "action_result":
            $model = "ActionResult";
            break;
        default:
            $model = "User";
            break;
    }
}
if (!isset($id_prefix)) {
    $id_prefix = null;
}
?>
<!-- START app/View/Elements/Feed/photo_upload_mini.ctp -->
<div class="form-group">
    <div class="fileinput_very_small fileinput-new" data-provides="fileinput">
        <div
            class="fileinput-preview thumbnail nailthumb-container m_0px click-target-enabled photo-plus-frame"
            data-trigger="fileinput"
            target-id="<?= $submit_id ?>"
            style="width: 34px; height: 34px;">
            <? $delete_style = null ?>
            <? if (isset($data[$model]["photo{$index}_file_name"]) && $data[$model]["photo{$index}_file_name"]): ?>
                <? $delete_style = "display:block" ?>
                <?=
                $this->Html->image('ajax-loader.gif',
                                   [
                                       'class'         => 'lazy',
                                       'style'         => 'width: 34px; height: 34px;',
                                       'data-original' => $this->Upload->uploadUrl($data, "{$model}.photo" . $index,
                                                                                   ['style' => 'small'])
                                   ]
                )
                ?>
            <? endif ?><? if ($index == 1): ?><i class="fa fa-plus photo-plus-mini"></i><? endif; ?>
        </div>
        <? if (isset($data[$model]["photo{$index}_file_name"]) && $data[$model]["photo{$index}_file_name"]): ?>
            <div class="custom-wrapper">
                <?=
                $this->Form->input('photo_delete.' . $index,
                                   [
                                       'type'         => 'checkbox',
                                       'label'        => "",
                                       'div'          => false,
                                       'class'        => 'custom-radio-check change-target-enabled',
                                       'target-id'    => $submit_id,
                                       'wrapInput'    => false,
                                       'errorMessage' => false,
                                       'id'           => $id_prefix . $model . "PhotoDelete" . $index
                                   ]
                ) ?>
            </div>
        <? endif; ?>
        <div>
            <span class="hideFileInput">
                <?
                $model_id = null;
                if (isset($data[$model]['id'])) {
                    $model_id = $data[$model]['id'];
                }
                if (isset($post_id)) {
                    $model_id = $model_id . "_Post_" . $post_id;
                }
                $field_prefix = $model;
                if ($has_many) {
                    $field_prefix .= ".0";
                }
                echo $this->Form->input($field_prefix . '.photo' . $index,
                                        ['type'                   => 'file',
                                         'label'                  => false,
                                         'div'                    => false,
                                         'class'                  => false,
                                         'wrapInput'              => false,
                                         'errorMessage'           => false,
                                         'required'               => false,
                                         'id'                     => $id_prefix . $model . "_" . $model_id . '_Photo_' . $index,
                                         'data-bv-container'      => '#' . $id_prefix . $model . "_" . $model_id . '_Photo_ValidateMessage',
                                         'data-bv-file'           => 'true',
                                         'data-bv-file-extension' => 'jpeg,jpg,png,gif',
                                         'data-bv-file-type'      => 'image/jpeg,image/png,image/gif',
                                         'data-bv-file-maxsize'   => 10485760,   // 10mb
                                         'data-bv-message'        => __d('validate',
                                                                         "10MB以下かつJPG、PNG、GIFのいずれかの形式を選択して下さい。")
                                        ]) ?>
            </span>
        </div>
    </div>
</div>
<!-- END app/View/Elements/Feed/photo_upload_mini.ctp -->
