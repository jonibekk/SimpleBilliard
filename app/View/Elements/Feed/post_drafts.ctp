<?= $this->App->viewStartComment()?>

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
                        <li>
                            <a>下書きを削除(未)</a>
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
                <span class="label label-primary">PROCESSING</span>
                <span title="2017年11月14日 18:35">8分前</span>
                <span class="font_lightgray"> ･ </span>
                <a href="#" data-url="/posts/ajax_get_share_circles_users_modal/post_id:102" class="modal-ajax-get-share-circles-users link-dark-gray">
                    <i class="fa fa-circle-o"></i>&nbsp;~~~~に共有予定(この投稿は自分のみ見えます)
                </a>
            </div>
        </div>

        <div class="col feed-contents post-contents showmore font_14px font_verydark box-align" id="PostTextBody_102">
            <?= $post_draft['data']['body'] ?>
            <?php
                // TODO: foreach the post_resources
                $videoStreamId = sprintf('video_stream_%d', $post_draft['post_resources'][0]['id']);
            ?>
            <video id="<?= $videoStreamId ?>" class="video-js vjs-default-skin" controls playsinline>
                <source
                        src="https://s3-ap-northeast-1.amazonaws.com/goalous-video-post-test/transcoded/1/2017-10-12.aes/playlist.m3u8"
                        type="application/x-mpegURL">
            </video>
            <script>videojs('<?= $videoStreamId ?>');</script>
        </div>

        <div style="display: none;"><?= json_encode($post_draft) ?></div>
    </div>
</div>
<?php endforeach; ?>
<?= $this->App->viewEndComment()?>
