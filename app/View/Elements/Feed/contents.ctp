<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 7/4/14
 * Time: 10:33 AM
 *
 * @var CodeCompletionView    $this
 * @var                       $posts
 * @var                       $current_circle
 * @var                       $circle_members
 * @var                       $feed_more_read_url
 */
?>
<!-- START app/View/Elements/Feed/contents.ctp -->
<?= $this->element("Feed/common_form") ?>
<? if ($current_circle): ?>
    <div class="panel panel-default feed-share-range">
        <div class="panel-body gl-feed">
            <div class="col col-xxs-12 font_12px">
                <?= $this->Html->link(__d('gl', 'すべて'), "/", ['class' => 'font_lightgray']) ?>
                <span> ･ </span>
                <span class="feed-share-range-circle"><?= $current_circle['Circle']['name'] ?></span>
                <span class="feed-circle-user-number">
        <?
        $title = '<ul class="gl-user-list-in-tooltip">';
        if (!empty($circle_members)) {
            foreach ($circle_members as $member) {
                $img = $this->Upload->uploadImage($member, 'User.photo', ['style' => 'small'],
                                                  ['width' => '16px', 'height' => '16px']);
                $username = $member['User']['display_username'];
                $title .= "<li>{$img}&nbsp;{$username}</li>";
            }
        }
        $title .= "</ul>";
        ?>
                    <a href="#" data-triger="click" data-toggle="tooltip" data-placement="bottom" data-html="true"
                       data-original-title='<?= $title ?>'>
                        <i class="fa fa-user"></i>&nbsp;<?= count($circle_members) ?></a>
                    </span>
            </div>
        </div>
    </div>
<? endif; ?>
<?= $this->element("Feed/posts") ?>
<? if (empty($posts)): ?>
    <div class="panel panel-default">
        <div class="panel-body gl-feed">
            <div class="col col-xxs-12">
                <?= __d('gl', "投稿がありません。") ?>
            </div>
        </div>
    </div>
<? endif; ?>
<? if (count($posts) == 20): ?>
    <div class="panel panel-default feed-read-more" id="FeedMoreRead">
        <div class="panel-body panel-read-more-body">
            <a href="#" class="btn btn-link click-feed-read-more"
               parent-id="FeedMoreRead"
               next-page-num="2"
               get-url="<?=
               $this->Html->url($feed_more_read_url) ?>"
                >
                <?= __d('gl', "もっと読む ▼") ?></a>
        </div>
    </div>
<? endif; ?>
<!-- END app/View/Elements/Feed/contents.ctp -->
