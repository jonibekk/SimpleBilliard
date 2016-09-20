<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 8/3/15
 * Time: 5:48 PM
 *
 * @var CodeCompletionView $this
 * @var                    $data
 * @var                    $page_type
 * @var                    $post_id
 * @var                    $comment_id
 * @var                    $ua
 * @var                    $is_mb_app
 */
if (!isset($comment_id)) {
    $comment_id = null;
}
if (!isset($page_type)) {
    $page_type = "feed";
}
$user = null;
if (isset($data['User'])) {
    $user = $data['User'];
} elseif (isset($data['AttachedFile']['User'])) {
    $user = $data['AttachedFile']['User'];
};

if (isset($data['AttachedFile'])) {
    $data = $data['AttachedFile'];
}

if (!isset($message_page_image)) {
    $message_page_image = false;
}

?>
<?= $this->App->viewStartComment()?>
<div class="col col-xxs-12">
    <?php if ($page_type != 'feed'): ?>
        <div class="pull-right">
            <div class="dropdown">
                <a href="#" class="font_lightGray-gray font_11px" data-toggle="dropdown" id="download">
                    <i class="fa fa-ellipsis-h"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="download">
                    <?php if (strtolower($this->Session->read('ua.device_type')) === "desktop" && !$is_mb_app): ?>
                        <li>
                            <a href="<?= $this->Html->url(
                                [
                                    'controller' => 'posts',
                                    'action'     => 'attached_file_download',
                                    'file_id'    => $data['id']
                                ]) ?>">
                                <i class="fa fa-download"></i><?= __("Download") ?></a>
                        </li>
                    <?php endif; ?>
                    <?php if ($this->Upload->isCanPreview($data)): ?>
                        <li>
                            <a href="<?= $this->Upload->attachedFileUrl($data, "viewer") ?>"
                                <?= $data['file_type'] == AttachedFile::TYPE_FILE_IMG ? "rel='lightbox' data-lightbox='LightBoxAttachedFilePreview_{$data['id']}'" : "target='_blank'" ?>>
                                <i class="fa fa-external-link-square"></i><?= __("Preview") ?></a>
                        </li>
                    <?php endif; ?>
                    <li>
                        <a href="<?= $this->Html->url([
                            'controller' => 'posts',
                            'action'     => 'feed',
                            'post_id'    => $post_id
                        ]) ?>">
                            <i class="fa fa-eye"></i><?= __("See the post") ?></a>
                    </li>
                </ul>
            </div>
        </div>
    <?php endif; ?>
    <?
    $icon_url = $this->Upload->attachedFileUrl($data, "viewer");
    if ($data['file_type'] == AttachedFile::TYPE_FILE_IMG) {
        $icon_url = $this->Upload->uploadUrl($data, 'AttachedFile.attached');
    }
    if ($page_type == "file_list") {
        $icon_url = $this->Html->url(['controller' => 'posts', 'action' => 'feed', 'post_id' => $post_id]);
    }
    ?>

    <?php if ($message_page_image) { ?>
        <a href="<?= $icon_url ?>" <?= $page_type == "feed" ? "rel='lightbox' data-lightbox='LightBoxAttachedFileImg_Post{$post_id}_Comment_{$comment_id}'" : "target='_blank'" ?>>
            <img src="<?= $this->Upload->uploadUrl($data, 'AttachedFile.attached', ['style' => 'small']) ?>">
        </a>
    <?php } else { ?>
    <div class="col col-xxs-1 messanger-attached-files-icon">
        <?php if ($data['file_type'] == AttachedFile::TYPE_FILE_IMG): ?>
            <a href="<?= $icon_url ?>" <?= $page_type == "feed" ? "rel='lightbox' data-lightbox='LightBoxAttachedFileImg_Post{$post_id}_Comment_{$comment_id}'" : "target='_blank'" ?>>
                <div>
                    <?php if ($data['file_type'] == AttachedFile::TYPE_FILE_IMG): ?>
                        <?=
                        $this->Html->image('ajax-loader.gif',
                            [
                                'class'         => 'lazy',
                                'data-original' => $this->Upload->uploadUrl($data,
                                    "AttachedFile.attached",
                                    ['style' => 'x_small']),
                                'width'         => '25px',
                                'height'        => '25px',
                                'error-img'     => "/img/no-image-link.png",
                                'style'         => []
                            ]
                        )
                        ?>
                    <?php else: ?>
                        <i class="fa <?= $this->Upload->getCssOfFileIcon($data) ?>"></i>
                    <?php endif; ?>
                </div>
            </a>
        <?php else: ?>
            <a href="<?= $icon_url ?>" target="_blank">
                <div>
                    <i class="fa <?= $this->Upload->getCssOfFileIcon($data) ?>"></i>
                </div>
            </a>

        <?php endif; ?>
    </div>

    <div class="col col-xxs-10 file-info-wrap">
        <a href="<?= $icon_url ?>"
            <?= $data['file_type'] == AttachedFile::TYPE_FILE_IMG && $page_type == "feed" ?
                "rel='lightbox' data-lightbox='LightBoxAttachedFileName_Post{$post_id}_Comment{$comment_id}'" : "target='_blank'"
            ?>
           target="_blank">
                <span class="font_14px font_bold font_verydark">
                    <?= $this->Upload->getAttachedFileName($data) ?>
                </span>
        </a>

        <div class="font_11px font_lightgray">
            <?php if ($page_type === 'file_list'): ?>
                <span
                    title="<?= $this->TimeEx->datetimeLocalFormat(h($data['created'])) ?>"><?= $this->TimeEx->elapsedTime(h($data['created'])) ?></span>
                <span class="font_lightgray"> ･ </span>
            <?php endif; ?>
            <span class=""><?= h($data['file_ext']) ?></span>
            <?php if ($page_type == 'file_list'): ?>
                <a href="<?= $this->Html->url([
                    'controller' => 'users',
                    'action'     => 'view_goals',
                    'user_id'    => $user['id']
                ]) ?>"
                   class="link-dark-gray">
                    <div>
                        <?=
                        $this->Html->image('ajax-loader.gif',
                            [
                                'class'         => 'lazy',
                                'data-original' => $this->Upload->uploadUrl($user, 'User.photo',
                                    ['style' => 'small']),
                                'width'         => '16px',
                                'error-img'     => "/img/no-image-user.png",
                            ]
                        )
                        ?>
                        <?= h($user['display_username']) ?>
                    </div>
                </a>
            <?php endif; ?>
        </div>
        <?php if ($page_type == 'feed'): ?>
            <div class="row file-btn-group">
                <?php if ($this->Upload->isCanPreview($data)): ?>
                    <a class="link-dark-gray" href="<?= $this->Upload->attachedFileUrl($data, "viewer") ?>"
                        <?= $data['file_type'] == AttachedFile::TYPE_FILE_IMG ? "rel='lightbox' data-lightbox='LightBoxAttachedFileMenu_Post{$post_id}_Comment{$comment_id}'" : "target='_blank'" ?>>
                        <div class="col col-xxs-6 text-center file-btn-wap">
                            <div class="file-btn">
                                <i class="fa fa-external-link-square"></i><?= __("Preview") ?>
                            </div>
                        </div>
                    </a>
                <?php else: ?>
                    <div class="col col-xxs-6 text-center">
                    </div>
                <?php endif; ?>
                <?php if (strtolower($this->Session->read('ua.device_type')) === "desktop" && !$is_mb_app): ?>
                    <a class="link-dark-gray" href="<?= $this->Html->url(
                        [
                            'controller' => 'posts',
                            'action'     => 'attached_file_download',
                            'file_id'    => $data['id']
                        ]) ?>">
                        <div class="col col-xxs-6 text-center file-btn-wap">
                            <div class="file-btn">
                                <i class="fa fa-download"></i><?= __("Download") ?>
                            </div>
                        </div>
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        <?php } ?>

    </div>
</div>
<?= $this->App->viewEndComment()?>
