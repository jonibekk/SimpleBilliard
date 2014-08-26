<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 7/4/14
 * Time: 10:33 AM
 *
 * @var CodeCompletionView    $this
 * @var                    $posts
 * @var                    $current_circle
 * @var                       $feed_more_read_url
 */
?>
<!-- START app/View/Elements/Feed/contents.ctp -->
<?= $this->element("Feed/common_form") ?>
<? if ($current_circle): ?>
    <div class="panel panel-default">
        <div class="panel-body gl-feed">
            <div class="col col-xxs-12">
                <?= $this->Html->link(__d('gl', 'すべて'), "/") ?>
                <span class="label label-info"><?= $current_circle['Circle']['name'] ?></span>
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
    <div class="panel panel-default" id="FeedMoreRead">
        <div class="panel-body">
            <div class="col col-xxs-12">
                <a href="#" class="btn btn-link click-feed-read-more"
                   parent-id="FeedMoreRead"
                   next-page-num="2"
                   get-url="<?=
                   $this->Html->url($feed_more_read_url) ?>"
                    >
                    <?= __d('gl', "もっと読む") ?></a>
            </div>
        </div>
    </div>
<? endif; ?>
<!-- END app/View/Elements/Feed/contents.ctp -->
