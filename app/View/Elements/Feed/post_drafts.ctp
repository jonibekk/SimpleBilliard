<?= $this->App->viewStartComment()?>

<?php
if (!isset($post_drafts)) {
    $post_drafts = [];
}
?>
<?php
// TODO: move these link/scripts to externals, css files
?>
<link href="https://unpkg.com/video.js/dist/video-js.css" rel="stylesheet">
<style>
    .vjs-poster {
        background-color: #f5f5f5;
    }
    .video-js {
        background-color: #f5f5f5;
    }
</style>
<script src="https://unpkg.com/video.js/dist/video.js"></script>
<script src="https://unpkg.com/videojs-flash/dist/videojs-flash.js"></script>
<script src="https://unpkg.com/videojs-contrib-hls/dist/videojs-contrib-hls.js"></script>
<?php foreach ($post_drafts as $post_draft_key => $post_draft): ?>
<div class="panel panel-default" style="
    border: dashed #929292 3px;
    ">
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
            <div>
                <?php
                    $transcodeStatus = new Goalous\Model\Enum\Video\VideoTranscodeStatus(intval($post_draft['post_resources'][0]['status_transcode']));
                ?>
                <!--
                [drafts.id: <?= $post_draft['id'] ?>][resource_id: <?=$post_draft['post_resources'][0]['id'] ?> / status: <?= $transcodeStatus->getValue() ?>(<?= $transcodeStatus->getKey() ?>)]
                -->
            </div>
            <?= nl2br(h($post_draft['data']['Post']['body'])) ?>
            <?php
                // TODO: foreach the post_resources
                $videoStreamId = sprintf('video_stream_%d', $post_draft['post_resources'][0]['id']);
            ?>
        </div>

    </div>
    <?php if ($post_draft['hasTranscodeFailed']): ?>
    <div style="
        background-color: #d94e4e;
        color: white;
        margin: 5px;
        padding: 5px;
        text-align: center;
        vertical-align:middle;
    ">
        <i class="fa fa-exclamation-circle fa-4x" aria-hidden="true"></i>
        <span style="font-weight: bold; font-size: 24px; vertical-align:middle;">Fail to share</span>
    </div>
    <?php else: ?>
    <div style="
        background-color: #9c9c9c;
        color: white;
        margin: 5px;
        padding: 5px;
        text-align: center;
        vertical-align:middle;
    ">
        <img src="/img/loader-transcoding.gif">
        <span style="font-weight: bold; font-size: 24px; vertical-align:middle;">Now Processing...</span>
    </div>
    <?php endif; ?>
</div>
<?php endforeach; ?>
<?= $this->App->viewEndComment()?>
