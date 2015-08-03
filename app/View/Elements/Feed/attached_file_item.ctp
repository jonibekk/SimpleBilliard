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
 */
if (!isset($page_type)) {
    $page_type = "feed";
}
$user = null;
if (isset($data['User'])) {
    $user = $data['User'];
}

if (isset($data['AttachedFile'])) {
    $data = $data['AttachedFile'];
}
?>
<!-- START app/View/Elements/Feed/attached_file_item.ctp -->
<div class="col col-xxs-12">
    <div class="pull-right">
        <div class="dropdown">
            <a href="#" class="font_lightGray-gray font_11px" data-toggle="dropdown" id="download">
                <i class="fa fa-ellipsis-h"></i>
            </a>
            <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="download">
                <li>
                    <a href="<?= $this->Upload->attachedFileUrl($data, "download") ?>" download>
                        <i class="fa fa-download"></i><?= __d('gl', "ダウンロード") ?></a>
                </li>
                <?php if ($this->Upload->isCanPreview($data)): ?>
                    <li>
                        <a href="<?= $this->Upload->attachedFileUrl($data, "viewer") ?>" target="_blank">
                            <i class="fa fa-external-link-square"></i><?= __d('gl', "プレビュー") ?></a>
                    </li>
                <?php endif; ?>
                <?php if ($page_type != 'feed'): ?>
                    <li>
                        <a href="<?= $this->Html->url(['controller' => 'posts', 'action' => 'feed', 'post_id' => $post_id]) ?>">
                            <i class="fa fa-eye"></i><?= __d('gl', "投稿を見る") ?></a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
    <div class="col col-xxs-1">
        <a href="<?= $this->Upload->attachedFileUrl($data, "preview") ?>" target="_blank">
            <div>
                <?php if ($data['file_type'] == AttachedFile::TYPE_FILE_IMG): ?>
                    <?=
                    $this->Html->image('ajax-loader.gif',
                                       [
                                           'class'         => 'lazy',
                                           'data-original' => $this->Upload->attachedFileUrl($data, "download"),
                                           'width'         => '25px',
                                           'height'        => '25px',
                                           'error-img'     => "/img/no-image-link.png",
                                       ]
                    )
                    ?>
                <?php else: ?>
                    <i style="font-size: 25px" class="fa <?= $this->Upload->getCssOfFileIcon($data) ?>"></i>
                <?php endif; ?>
            </div>
        </a>
    </div>
    <div class="col col-xxs-10" style="overflow: hidden">
        <a href="<?= $this->Upload->attachedFileUrl($data, "viewer") ?>" target="_blank">
                <span class="font_14px font_bold font_verydark">
                    <?= $data['attached_file_name'] ?>
                </span>
        </a>

        <div class="font_11px font_lightgray">
            <span
                title="<?= $this->TimeEx->datetimeLocalFormat(h($data['created'])) ?>"><?= $this->TimeEx->elapsedTime(h($data['created'])) ?></span>
            <span class="font_lightgray"> ･ </span>
            <span class=""><?= $data['file_ext'] ?></span>
            <?php if ($page_type == 'file_list'): ?>
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
            <?php endif; ?>
        </div>
        <?php if ($page_type == 'feed'): ?>
            <div class="row">
                <?php if ($this->Upload->isCanPreview($data)): ?>
                    <a class="link-dark-gray" href="<?= $this->Upload->attachedFileUrl($data, "viewer") ?>"
                       target="_blank">
                        <div class="col col-xxs-6 text-center" style="border-radius: 4px;border: 1px solid #dddddd;">
                            <i class="fa fa-external-link-square"></i><?= __d('gl', "プレビュー") ?>
                        </div>
                    </a>
                <?php else: ?>
                    <div class="col col-xxs-6 text-center">
                    </div>
                <?php endif; ?>
                <a class="link-dark-gray" href="<?= $this->Upload->attachedFileUrl($data, "download") ?>" download>
                    <div class="col col-xxs-6 text-center" style="border-radius: 4px;border: 1px solid #dddddd;">
                        <i class="fa fa-download"></i><?= __d('gl', "ダウンロード") ?>
                    </div>
                </a>
            </div>
        <?php endif; ?>

    </div>
</div>
<!-- END app/View/Elements/Feed/attached_file_item.ctp -->
