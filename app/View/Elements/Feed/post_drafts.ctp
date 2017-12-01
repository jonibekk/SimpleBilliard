<?= $this->App->viewStartComment()?>

<?php
if (!isset($post_drafts)) {
    $post_drafts = [];
}
?>
<link href="https://unpkg.com/video.js/dist/video-js.css" rel="stylesheet">
<script src="https://unpkg.com/video.js/dist/video.js"></script>
<script src="https://unpkg.com/videojs-flash/dist/videojs-flash.js"></script>
<script src="https://unpkg.com/videojs-contrib-hls/dist/videojs-contrib-hls.js"></script>
<?php foreach ($post_drafts as $post_draft_key => $post_draft): ?>
<div class="panel panel-default">
    <div class="panel-body">

        <div class="col feed-user">
            <div class="pull-right">
                <div class="dropdown">
                    <a href="#" class="font_lightGray-gray font_11px" data-toggle="dropdown" id="download">
                        <i class="fa fa-chevron-down feed-arrow"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="download">
                        <li><?=
                            $this->Form->postLink(__('Delete draft'),
                                [
                                    'controller' => 'post_draft',
                                    'action'     => 'delete',
                                    $post_draft['id']
                                ],
                                null,
                                __("Do you really want to delete this draft post?")) ?></li>
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
                <span class="label label-primary">PROCESSING</span>
                <?php
                    $minutesAgo = (time() - $post_draft['created']) / 60;
                    $minutesAgo = floor($minutesAgo);
                ?>
                <span title=""><?= $minutesAgo ?>minutes ago</span>
                <div>
                <span href="#" data-url="" class="modal-ajax-get-share-circles-users link-dark-gray">
                    <i class="fa fa-circle-o"></i>&nbsp;~~~~に共有予定(動画処理完了後に公開されます)
                </span>
                </div>
            </div>
        </div>
        <div class="col feed-contents post-contents showmore font_14px font_verydark box-align" id="PostTextBody_102">
            <div style="text-align: center">
                <i class="fa fa-spinner fa-4x fa-spin" aria-hidden="true"></i>
            </div>
            <div>
                <?php
                    $transcodeStatus = new Goalous\Model\Enum\Video\VideoTranscodeStatus(intval($post_draft['post_resources'][0]['status_transcode']));
                ?>
                [drafts.id: <?= $post_draft['id'] ?>][resource_id: <?=$post_draft['post_resources'][0]['id'] ?> / status: <?= $transcodeStatus->getValue() ?>(<?= $transcodeStatus->getKey() ?>)]
            </div>
            <?= nl2br(h($post_draft['data']['Post']['body'])) ?>
            <?php
                // TODO: foreach the post_resources
                $videoStreamId = sprintf('video_stream_%d', $post_draft['post_resources'][0]['id']);
            ?>

            <!--
            <video id="<?= $videoStreamId ?>" class="video-js vjs-default-skin" controls playsinline>
                <source
                        src="https://s3-ap-northeast-1.amazonaws.com/goalous-video-post-test/transcoded/1/2017-10-12.aes/playlist.m3u8"
                        type="application/x-mpegURL">
            </video>
            <script>videojs('<?= $videoStreamId ?>');</script>
            -->
        </div>

    </div>
</div>
<?php endforeach; ?>
<?= $this->App->viewEndComment()?>
