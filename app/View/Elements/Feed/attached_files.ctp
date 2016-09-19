<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 8/3/15
 * Time: 5:48 PM
 *
 * @var CodeCompletionView $this
 * @var                    $files
 */
?>
<?= $this->App->viewStartComment()?>
<?php foreach ($files as $file): ?>
    <div class="panel-body pt_10px plr_11px pb_8px bd-b">
        <?php
        $p_id = viaIsSet($file['PostFile'][0]['post_id']);
        $p_id = $p_id ? $p_id : viaIsSet($file['CommentFile'][0]['Comment']['post_id']);
        ?>
        <?= $this->element('Feed/attached_file_item',
            ['data' => $file, 'page_type' => 'file_list', 'post_id' => $p_id]) ?>
    </div>
<?php endforeach ?>
<?= $this->App->viewEndComment()?>
