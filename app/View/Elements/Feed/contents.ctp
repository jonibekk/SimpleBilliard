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
 * @var                       $feed_filter
 */
?>
<!-- START app/View/Elements/Feed/contents.ctp -->
<?= $this->element("Feed/common_form") ?>
<div class="feed-share-range">
    <div class="panel-body ptb_10px plr_11px">
        <div class="col col-xxs-12 font_12px">
            <? if ($feed_filter == "all"): ?>
                <span class="feed-current-filter"><?= __d('gl', 'すべて') ?></span>
            <? else: ?>
                <?= $this->Html->link(__d('gl', 'すべて'), "/", ['class' => 'font_lightgray']) ?>
            <? endif; ?>
            <span> ･ </span>
            <? if ($feed_filter == "goal"): ?>
                <span class="feed-current-filter"><?= __d('gl', 'ゴール') ?></span>
            <? else: ?>
                <?= $this->Html->link(__d('gl', 'ゴール'),
                                      ['controller' => 'posts', 'action' => 'feed', 'filter_goal' => true],
                                      ['class' => 'font_lightgray']) ?>
            <? endif; ?>
            <? if ($current_circle): ?>
                <span> ･ </span>
                <span class="feed-current-filter"><?= h($current_circle['Circle']['name']) ?></span>
                <span class="feed-circle-user-number">
        <?
        $title = '<ul class="user-list-in-tooltip">';
        if (!empty($circle_members)) {
            foreach ($circle_members as $member) {
                $img = $this->Upload->uploadImage($member, 'User.photo', ['style' => 'small'],
                                                  ['width' => '16px', 'height' => '16px']);
                $username = h($member['User']['display_username']);
                $title .= "<li>{$img}&nbsp;{$username}</li>";
            }
        }
        $title .= "</ul>";
        ?>
                    <a href="#" data-triger="click" data-toggle="tooltip" data-placement="bottom" data-html="true"
                       data-original-title='<?= $title ?>'>
                        <i class="fa fa-user"></i>&nbsp;<?= count($circle_members) ?></a>
                    </span>
            <? endif; ?>
        </div>
    </div>
</div>
<a href="" class="alert alert-info feed-notify-box" role="alert" style="margin-bottom:5px;display:none;opacity:0;"><span
        class="num"></span><?=__d('gl',"件の新しい投稿があります。")?></a>
<?= $this->element("Feed/posts") ?>
<? if (empty($posts)): ?>
    <div class="panel panel-default">
        <div class="panel-body ptb_10px plr_11px ">
            <div class="col col-xxs-12">
                <?= __d('gl', "投稿がありません。") ?>
            </div>
        </div>
    </div>
<? endif; ?>
<?
//ポストが存在する　かつ　パーマリンクでない
if (!empty($posts)&&(!isset($this->request->params['post_id']) || empty($this->request->params['post_id']))):
    ?>
    <?
    $next_page_num = 2;
    $month_index = 0;
    $more_read_text = __d('gl', "もっと読む ▼");
    if ((count($posts) != 20)) {
        $next_page_num = 1;
        $month_index = 1;
        $more_read_text = __d('gl', "さらに以前の投稿を読み込む ▼");
    }
    ?>
    <div class="panel panel-default feed-read-more" id="FeedMoreRead">
        <div class="panel-body panel-read-more-body">
            <span class="none" id="ShowMoreNoData"><?= __d('gl', "これ以上のデータがありません。") ?></span>
            <a href="#" class="btn btn-link click-feed-read-more"
               parent-id="FeedMoreRead"
               no-data-text-id="ShowMoreNoData"
               next-page-num="<?= $next_page_num ?>"
               month-index="<?= $month_index ?>"
               get-url="<?=
               $this->Html->url($feed_more_read_url) ?>"
                >
                <?= $more_read_text ?></a>
        </div>
    </div>
<? endif; ?>
<!-- END app/View/Elements/Feed/contents.ctp -->
