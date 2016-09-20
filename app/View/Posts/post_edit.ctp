<?php
/**
 *
 */
?>
<?= $this->App->viewStartComment()?>
<div class="col-sm-8 col-sm-offset-2">
    <div class="panel panel-default">
        <div class="panel-body post-edit"
             data-default-ogp-url="<?= $this->Post->extractOgpUrl($this->request->data('Post.site_info')) ?>">
            <?= $this->element('Feed/common_form') ?>
        </div>
    </div>
</div>
<?= $this->App->viewEndComment()?>