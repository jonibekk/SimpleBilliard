<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 7/4/14
 * Time: 10:33 AM
 *
 * @var CodeCompletionView $this
 */
?>
<?= $this->element("Feed/common_form") ?>
<?= $this->element("Feed/posts") ?>
<div class="panel panel-default" id="FeedMoreRead">
    <div class="panel-body">
        <div class="col col-xxs-12">
            <a href="#" class="btn btn-link click-feed-read-more"
               parent-id="FeedMoreRead"
               next-page-num="2"
               get-url="<?= $this->Html->url(["controller" => "posts", 'action' => 'ajax_get_feed']) ?>"
                >
                <?= __d('gl', "もっと読む") ?></a>
        </div>
    </div>
</div>