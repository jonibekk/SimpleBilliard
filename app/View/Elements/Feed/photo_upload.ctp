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
 * @var                    $has_many
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
if (!isset($has_many)) {
    $has_many = false;
}
if (!isset($id_prefix)) {
    $id_prefix = null;
}
?>
<!-- START app/View/Elements/Feed/photo_upload.ctp -->
<div class="form-group">
    <div class="fileinput_post_comment fileinput-new" data-provides="fileinput">
        <div
            class="fileinput-preview thumbnail nailthumb-container m_0px click-target-enabled photo-plus-frame"
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
            <span class="btn-file">
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
                                        ['type'         => 'file',
                                         'label'        => false,
                                         'div'          => false,
                                         'class'        => false,
                                         'wrapInput'    => false,
                                         'errorMessage' => false,
                                         'required'     => false,
                                         'accept'       => 'image/*',
                                         'id'           => $id_prefix . $model . "_" . $model_id . '_Photo_' . $index
                                        ]) ?>
            </span>
        </div>
    </div>
</div>
<!-- END app/View/Elements/Feed/photo_upload.ctp -->
