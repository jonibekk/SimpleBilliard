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
<div class="panel-body pt_10px plr_11px pb_8px">
    <div class="col col-xxs-12 feed-user">
        <div class="pull-right">
            <div class="dropdown">
                <a href="#" class="font_lightGray-gray font_11px" data-toggle="dropdown" id="download">
                    <i class="fa fa-chevron-down feed-arrow"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="download">
                    <li>
                        <a href="<?= $this->Upload->attachedFileUrl($data, "download") ?>" download>
                            <i class="fa fa-download"></i><?= __d('gl', "ダウンロード") ?></a>
                    </li>
                    <li>
                        <a href="<?= $this->Upload->attachedFileUrl($data, "preview") ?>" _target="blank">
                            <i class="fa fa-external-link-square"></i><?= __d('gl', "プレビュー") ?></a>
                    </li>
                    <li>
                        <a href="<?= $this->Html->url(['controller' => 'posts', 'action' => 'feed', 'post_id' => $post_id]) ?>">
                            <i class="fa fa-eye"></i><?= __d('gl', "投稿を見る") ?></a>
                    </li>
                </ul>
            </div>
        </div>
        <a href="<?= $this->Upload->attachedFileUrl($data, "preview") ?>">
            <div>
                <?php if ($data['file_type'] == AttachedFile::TYPE_FILE_IMG): ?>
                    <?=
                    $this->Html->image('ajax-loader.gif',
                                       [
                                           'class'         => 'post-heading-goal-avator lazy media-object',
                                           'data-original' => $this->Upload->attachedFileUrl($data, "download"),
                                           'width'         => '32px',
                                           'error-img'     => "/img/no-image-link.png",
                                       ]
                    )
                    ?>
                <?php else: ?>
                    <?php
                    $color = null;
                    ?>
                    <i class="<?= $this->Upload->getCssOfFileIcon($data) ?>"></i>
                <?php endif; ?>
            </div>
            <span class="font_14px font_bold font_verydark">
                <?= $data['attached_file_name'] ?>
            </span>
        </a>

        <div class="font_11px font_lightgray">
            <span
                title="<?= $this->TimeEx->datetimeLocalFormat(h($data['created'])) ?>"><?= $this->TimeEx->elapsedTime(h($data['created'])) ?></span>
            <span class="font_lightgray"> ･ </span>
            <span class=""><?= $data['file_ext'] ?></span>

            <div>
                <?=
                $this->Html->image('ajax-loader.gif',
                                   [
                                       'class'         => 'post-heading-goal-avator lazy media-object',
                                       'data-original' => $this->Upload->uploadUrl($user, 'User.photo',
                                                                                   ['style' => 'small']),
                                       'width'         => '32px',
                                       'error-img'     => "/img/no-image-user.png",
                                   ]
                )
                ?>
                <?= h($user['display_username']) ?>
            </div>
        </div>
    </div>
</div>
<!-- END app/View/Elements/Feed/attached_file_item.ctp -->
