<?= $this->App->viewStartComment()?>

<?php
if (!isset($post_drafts)) {
    $post_drafts = [];
}
?>
<?php foreach ($post_drafts as $post_draft_key => $post_draft): ?>
<div class="panel panel-default panel-draft-post post_draft_<?= $post_draft['id'] ?>">
    <div class="panel-body">

        <div class="col feed-user">
            <div class="pull-right">
                <div class="dropdown">
                    <a href="#" class="font_lightGray-gray font_11px" data-toggle="dropdown" id="download">
                        <i class="fa fa-chevron-down feed-arrow"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="download">
                        <li class="delete-post-draft" data-post-draft-id="<?= $post_draft['id'] ?>">
                            <a><?= __('Delete draft') ?></a>
                        </li>
                    </ul>
                </div>
            </div>
            <a>

                <?= $this->Upload->uploadImage($my_prof, 'User.photo', ['style' => 'medium'],
                    ['class' => 'lazy feed-img']) ?>
                <span class="font_14px font_bold font_verydark">
                    <?= h($this->Session->read('Auth.User.display_username')) ?>
                </span>
            </a>
            <div class="font_11px font_lightgray oneline-ellipsis">
                <span class="label label-primary">
                    <?php if ($post_draft['hasTranscodeFailed']): ?>
                        Error
                    <?php else: ?>
                        Processing
                    <?php endif ?>
                </span>
                &nbsp;
                <span href="#" data-url="" class="modal-ajax-get-share-circles-users link-dark-gray">
                    <i class="fa fa-circle-o"></i>&nbsp<?= $post_draft['share_text'] ?>
                </span>
            </div>
        </div>
        <div class="col feed-contents post-contents showmore font_14px font_verydark box-align" id="PostTextBody_102">
            <?= nl2br(h($post_draft['data']['Post']['body'])) ?>
        </div>
    </div>
    <?php if ($post_draft['hasTranscodeFailed']): ?>
    <div class="draft-post-message-error">
        <div class="margin_auto">
            <div class="disp_ib draft-post-message-content-left">
                <i class="fa fa-exclamation-circle fa-4x" aria-hidden="true"></i>
            </div>
            <div class="disp_ib text-left">
                <span class="draft-post-message-text">Processing Failed</span>
            </div>
        </div>
    </div>
    <?php else: ?>
    <div class="draft-post-message">
        <img src="/img/loader-transcoding.gif">
        <span class="draft-post-message-text">Now Processing</span>
    </div>
    <div class="draft-post-message-succeed hide" style="vertical-align: middle;">
        <div class="margin_auto">
            <div class="disp_ib draft-post-message-content-left">
                <i class="fa fa-check fa-4x" aria-hidden="true"></i>
            </div>
            <div class="disp_ib text-left">
                <span class="draft-post-message-text">Succeeded!</span><br>
                <a href="" class="link_succeed">Go here to see</a>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>
<?php endforeach; ?>
<?= $this->App->viewEndComment()?>
