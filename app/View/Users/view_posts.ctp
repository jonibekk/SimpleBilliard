<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 7/9/15
 * Time: 3:33 PM
 *
 * @var $user
 * @var $posts
 * @var $item_created
 */
?>
<?= $this->App->viewStartComment() ?>
<div class="col-sm-8 col-sm-offset-2">
    <div class="panel panel-default">
        <?= $this->element('User/simplex_top_section') ?>
        <div class="panel-body view-posts-panel">
            <?= $this->element("Feed/posts") ?>
            <?php //投稿が指定件数　もしくは　アイテム作成日から１ヶ月以上経っている場合
            if (count($posts) == POST_FEED_PAGE_ITEMS_NUMBER || $item_created < REQUEST_TIMESTAMP - (60 * 60 * 24 * 30)):?>

                <?php
                $next_page_num = 2;
                $month_index = 0;
                $more_read_text = __("More...");
                $oldest_post_time = 0;
                if ((count($posts) != POST_FEED_PAGE_ITEMS_NUMBER)) {
                    $next_page_num = 1;
                    $month_index = 1;
                    $more_read_text = __("View previous posts ▼");
                }

                // ユーザーの登録日以前の投稿は存在しないので読み込まないようにする
                if (isset($user['User']['created']) && $user['User']['created']) {
                    $oldest_post_time = $user['User']['created'];
                }

                ?>
                <div class="panel panel-default feed-read-more" id="FeedMoreRead">
                    <div class="panel-body panel-read-more-body">
                        <span class="none" id="ShowMoreNoData"><?= __("There is no more post to show.") ?></span>
                        <a href="#" class="click-feed-read-more"
                           parent-id="FeedMoreRead"
                           no-data-text-id="ShowMoreNoData"
                           next-page-num="<?= $next_page_num ?>"
                           month-index="<?= $month_index ?>"
                           get-url="<?=
                           $this->Html->url([
                               'controller' => 'posts',
                               'action'     => 'ajax_get_user_page_post_feed',
                               'user_id'    => Hash::get($this->request->params, 'named.user_id'),
                               'type'       => Post::TYPE_NORMAL,
                           ]) ?>"
                           id="FeedMoreReadLink"
                           oldest-post-time="<?= $oldest_post_time ?>"
                        >
                            <?= h($more_read_text) ?> </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?= $this->App->viewEndComment() ?>
