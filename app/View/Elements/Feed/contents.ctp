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

<?php
if(isset($user_status))
{
if(  isset($params['action']) && $params['controller']=='posts' && $params['action']=='feed' && ($user_status=='joined' || $user_status=='admin'))
{
?>
<?= $this->element("Feed/common_form") ?>
<?php } }  else { ?>
    <?= $this->element("Feed/common_form") ?>
<?php } ?>
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
                <span class="feed-current-filter"><?  $circle_name = h($current_circle['Circle']['name']);
                    if(strlen($circle_name) > 29){
                        $circle_name =  substr($circle_name, 0, 29).'...';
                    }
                    echo $circle_name;
                    ?></span>
                <a href="<?= $this->Html->url(['controller' => 'circles', 'action' => 'ajax_get_circle_members', $current_circle['Circle']['id']]) ?>"
                     class="modal-ajax-get"> <span class="feed-circle-user-number"><i
                            class="fa fa-user"></i>&nbsp;<?= count($circle_members) ?>
                    </span></a>
            <?php if($user_status!='admin'){ ?>
                <div class="pull-right header-function dropdown">
                    <a id="download" data-toggle="dropdown" class="font_lightGray-gray font_14px plr_4px pt_1px pb_2px bd-radius_4px header-function-link" href="#" style="opacity: 0.54;">
                        <i class="fa fa-cog header-function-icon" style="color: rgb(80, 80, 80); opacity: 0.88;"></i>
                        <i class="fa fa-caret-down goals-column-fa-caret-down header-function-icon" style="color: rgb(80, 80, 80); opacity: 0.88;"></i>
                    </a>
                    <ul aria-labelledby="dropdownMenu1" role="menu" class="dropdown-menu dropdown-menu-right frame-arrow-icon">
                        <li>
                            <?php if($user_status!='joined') { ?>
                        <a href="<?=$this->Html->url(['controller' => 'posts', 'action' => 'join_circle',$current_circle['Circle']['id']])?>">Join Circle</a>
                            <?php } else { ?>
                                <a href="<?=$this->Html->url(['controller' => 'posts', 'action' => 'unjoin_circle',$current_circle['Circle']['id']])?>">Leave Circle</a>
                            <?php }?>
                        </li>
                        <li>
                            <?php if($circle_status=='1') {
                                echo $this->Html->link('Hide', 'circleToggleStatus/'.$current_circle['Circle']['id'].'/0');
                                ?>
<!--                                <a href="--><?//=$this->Html->url(['controller' => 'posts', 'action' => 'circleToggleStatus','show'])?><!--">Show</a>-->

                            <?php } else {
                                echo $this->Html->link('Show', 'circleToggleStatus/'.$current_circle['Circle']['id'].'/1');
                                ?>
<!--                                <a href="--><?//=$this->Html->url(['controller' => 'posts', 'action' => 'circleToggleStatus','hide'])?><!--">Hide</a>-->
                            <?php }?>
                        </li>
                    </ul>
                </div>
                <?php } ?>
            <? endif; ?>
        </div>
    </div>
</div>
<a href="" class="alert alert-info feed-notify-box" role="alert" style="margin-bottom:5px;display:none;opacity:0;">
    <span class="num"></span><?= __d('gl', "件の新しい投稿があります。") ?></a>
<? if ($current_circle && $user_status!='admin'): ?>
    <?php if($user_status!='joined') { ?>
<div class="panel panel-default">
    <div class="panel-body ptb_10px plr_11px ">
        <div class="col col-xxs-12">

            Join this Circle to post or comment.
                <a href="<?=$this->Html->url(['controller' => 'posts', 'action' => 'join_circle',$current_circle['Circle']['id']])?>" class='btn btn-primary pull-right'>
                <i class="fa fa-user-plus"></i> <?=__d('gl', 'Join Circle')?>
                </a>
        </div>
    </div>
</div>
        <?php if(!empty($current_circle['Circle']['description'])) : ?>
        <div class="panel panel-default">
            <h4 style='margin-left:15px;font-weight:bold'>About </h4>
            <div class="panel-body ptb_10px plr_11px ">
                <?=$current_circle['Circle']['description']; ?>
            </div>
        </div>
            <?php endif; ?>
    <?php }?>
<? endif; ?>
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
