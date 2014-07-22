<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 7/22/14
 * Time: 11:05 AM

 *
*@var                    $index
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
<div class="form-group">
    <div class="fileinput_post_comment fileinput-new" data-provides="fileinput">
        <div class="fileinput-preview thumbnail nailthumb-container" data-trigger="fileinput"
             style="width: 50px; height: 50px;">
            <? if (isset($data[$model]["photo{$index}_file_name"]) && $data[$model]["photo{$index}_file_name"]): ?>
                <?=
                $this->Upload->uploadImage($data, "{$model}.photo" . $index,
                                           ['style' => 'small'], ['style' => 'width: 50px; height: 50px;']) ?>
            <? endif ?>
        </div>
        <div>
                        <span class="btn-file">
                            <?=
                            $this->Form->input('photo' . $index,
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
</div>