<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 7/4/14
 * Time: 10:33 AM
 *
 * @var CodeCompletionView    $this
 * @var                       $posts
 * @var                       $currentCircle
 * @var                       $circle_member_count
 * @var                       $feed_more_read_url
 * @var                       $feed_filter
 * @var                       $circle_status
 * @var                       $user_status
 * @var                       $params
 * @var                       $item_created
 */
?>
<?= $this->App->viewStartComment() ?>
<?= $this->element('Feed/feed_share_range_filter',
    compact('currentCircle', 'user_status', 'circle_member_count', 'circle_status', 'feed_filter')) ?>
<?php
// 投稿単体ページでは入力フォームを表示しない
if (!isset($this->request->params['post_id'])) {
    if (isset($user_status)) {
        if (Hash::get($params, 'controller') == 'posts' && (Hash::get($params, 'action') == 'feed' || Hash::get($params,
                    'action') == 'ajax_circle_feed') && ($user_status == 'joined' || $user_status == 'admin')
        ) {
            echo $this->element("Feed/common_form");
        }
    } else {
        echo $this->element("Feed/common_form");
    }
}
?>
<a href="" class="alert alert-info feed-notify-box" role="alert" style="margin-bottom:5px;display:none;opacity:0;">
    <span class="num"></span><?= __(" new posts") ?></a>

<?= $this->element('Feed/circle_join_button', compact('currentCircle', 'user_status')) ?>
<?php
// 通知 -> 投稿単体ページ と遷移してきた場合は、通知一覧に戻るボタンを表示する
if (isset($this->request->params['post_id']) && isset($this->request->params['named']['notify_id'])): ?>
    <a href="#" get-url="<?= $this->Html->url(['controller' => 'notifications']) ?>"
       class="btn-back btn-back-notifications">
        <i class="fa fa-chevron-left font_18px font_lightgray lh_20px"></i>
    </a>
    <?php
    // 削除された投稿へのリクエストの場合
    if (!$posts): ?>
        <?= $this->element("Feed/post_not_found") ?>
    <?php endif ?>
<?php endif ?>
<div id="app-view-elements-feed-posts">
    <?= $this->element("Feed/posts") ?>
</div>
<?php
$next_page_num = 2;
$month_index = 0;
$more_read_text = __("More...");
if ((count($posts) != POST_FEED_PAGE_ITEMS_NUMBER)) {
    $next_page_num = 1;
    $month_index = 1;
    $more_read_text = __("View previous posts ▼");
}

$hideReadMoreLink = true;
//(投稿が指定件数　もしくは　アイテム作成日から１ヶ月以上経っている)かつパーマリンクでない場合は「もっと読む」ボタンを表示
if ((count($posts) == POST_FEED_PAGE_ITEMS_NUMBER || (isset($item_created) && $item_created < REQUEST_TIMESTAMP - MONTH)) &&
    !Hash::get($this->request->params, 'post_id')
) {
    $hideReadMoreLink = false;
}

// １件目の投稿の更新時間
// 次回 Ajax リクエスト時はこの投稿の更新時間より前の投稿のみを読み込む
// （新着投稿による重複表示をふせぐため）
// ホームフィードでは created、その他では modified を使用する
$currentCircle = $currentCircle ?? [];
$currentTeam = $currentTeam ?? [];
$oldestPostTime = $this->Post->getOldestPostTime($currentCircle, $currentTeam);
$postTimeBefore = $this->Post->getPostTimeBefore($posts, $currentCircle, $currentTeam);
?>
<div class="panel panel-default feed-read-more <?= $hideReadMoreLink ? 'hidden' : null ?>" id="FeedMoreRead">
    <div class="panel-body panel-read-more-body">
        <span class="none" id="ShowMoreNoData"><?= __("There is no more post to show.") ?></span>
        <a href="#" class="click-feed-read-more"
           parent-id="FeedMoreRead"
           no-data-text-id="ShowMoreNoData"
           next-page-num="<?= $next_page_num ?>"
           month-index="<?= $month_index ?>"
           get-url="<?=
           $this->Html->url($feed_more_read_url) ?>"
           id="FeedMoreReadLink"
           oldest-post-time="<?= $oldestPostTime ?>"
           post-time-before="<?= $postTimeBefore ?>"
        >
            <?= $more_read_text ?> </a>
    </div>
</div>
<?php
// 通知 -> 投稿単体ページ と遷移してきた場合は、通知一覧に戻るボタンを表示する
if (isset($this->request->params['post_id']) && isset($this->request->params['named']['notify_id'])): ?>
    <a href="#" get-url="<?= $this->Html->url(['controller' => 'notifications']) ?>"
       class="btn-back btn-back-notifications">
        <i class="fa fa-chevron-left font_18px font_lightgray lh_20px"></i>
    </a>
<?php endif ?>
<?= $this->App->viewEndComment() ?>
