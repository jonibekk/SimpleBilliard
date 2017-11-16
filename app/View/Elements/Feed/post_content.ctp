<?= $this->App->viewStartComment() ?>
<div
    class="col feed-contents post-contents <?= viaIsSet($long_text) ? "showmore-circle" : "showmore" ?> font_14px font_verydark box-align"
    id="PostTextBody_<?= $post['Post']['id'] ?>">
    <?php if (($post['Post']['type'] == Post::TYPE_NORMAL) || ($post['Post']['type'] == Post::TYPE_MESSAGE)): ?>
        <?= nl2br($this->TextEx->autoLink($post['Post']['body'])) ?>
    <?php elseif ($post['Post']['type'] == Post::TYPE_ACTION): ?>
        <i class="fa fa-check-circle disp_i"></i>&nbsp;<?= nl2br($this->TextEx->autoLink($post['ActionResult']['name'])) ?>
    <?php elseif ($post['Post']['type'] == Post::TYPE_KR_COMPLETE): ?>
        <i class="fa fa-key disp_i"></i>&nbsp;<?= __("Achieved %s!",
            h($post['KeyResult']['name'])) ?>
    <?php elseif ($post['Post']['type'] == Post::TYPE_GOAL_COMPLETE): ?>
        <i class="fa fa-flag disp_i"></i>&nbsp;<?= __("Achieved %s!", h($post['Goal']['name'])) ?>
    <?php else: ?>
        <?= Post::$TYPE_MESSAGE[$post['Post']['type']] ?>
    <?php endif; ?>
</div>
<?php foreach ($post['PostResources'] as $resource): ?>
    <div class="col feed-contents post-contents showmore font_14px font_verydark box-align" id="PostTextBody_104">
        <?php
        // TODO: foreach the post_resources
        $videoStreamId = sprintf('video_stream_%d', $resource['id']);
        $thumbnailPath = dirname($resource["playlist_path"])."/thumbs-00001.png";
        ?>
            <video id="<?= $videoStreamId ?>" class="video-js vjs-default-skin" controls playsinline poster="<?= $thumbnailPath ?>">
                <source
                        src="<?= $resource["playlist_path"] ?>"
                        type="application/x-mpegURL">
            </video>
            <script>videojs('<?= $videoStreamId ?>');</script>
    </div>
<?php endforeach; ?>
<?= $this->App->viewEndComment() ?>
