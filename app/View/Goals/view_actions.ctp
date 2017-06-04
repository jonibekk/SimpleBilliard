<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 7/9/15
 * Time: 3:33 PM
 *
 * @var CodeCompletionView $this
 * @var                    $posts
 * @var                    $krSelectOptions
 * @var                    $goalId
 * @var                    $goalBaseUrl
 * @var                    $keyResultId
 * @var                    $item_created
 */
$namedParams = $this->request->params['named'];
?>
<?= $this->App->viewStartComment() ?>
<div class="goal-view-actions col-sm-8 col-sm-offset-2">
    <div class="panel panel-default">
        <?= $this->element('Goal/simplex_top_section') ?>
        <div class="goal-view-actions-panel">
            <div class="view-actions-panel-wrap">
                <div class="view-actions-panel-filter">
                    <a class="dropdown-toggle" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true"
                       aria-expanded="true">
                        KR:&nbsp;<strong><?php
                            if (Hash::get($namedParams, 'key_result_id')) {
                                echo $krSelectOptions[$namedParams['key_result_id']];
                            } else {
                                echo __("All");

                            }
                            ?>&nbsp;</strong><span class="fa fa-angle-down ml_2px"></span>
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenu2">
                        <?php foreach ($krSelectOptions as $krID => $krName): ?>
                            <li>
                                <a href="/goals/view_actions/goal_id:<?= $goalId ?>/page_type:<?= $namedParams['page_type'] ?>/key_result_id:<?= $krID ?>"><?= $krName ?></a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php
                $item_num = POST_FEED_PAGE_ITEMS_NUMBER;
                if ($this->request->params['named']['page_type'] == 'image') {
                    $item_num = MY_PAGE_CUBE_ACTION_IMG_NUMBER;
                }
                ?>
                <?php if ($actionCount != 0): ?>
                <div class="view-actions-panel-btngroup-wrap">
                    <div class="view-action-panel-filter-btngroup">
                        <?php if ($namedParams['page_type'] == 'list'): ?>
                            <a class="view-action-panel-filter-button"
                               href="<?= $this->Html->url(am($namedParams, ['page_type' => 'image'])) ?>">
                                <i class="fa fa-th-large link-dark-gray"></i>
                            </a>
                            <a class="view-action-panel-filter-button mod-active"
                               href="<?= $this->Html->url(am($namedParams, ['page_type' => 'list'])) ?>">
                                <i class="fa fa-reorder link-dark-gray"></i>
                            </a>
                        <?php elseif ($namedParams['page_type'] == 'image'): ?>
                            <a class="view-action-panel-filter-button mod-active"
                               href="<?= $this->Html->url(am($namedParams, ['page_type' => 'image'])) ?>">
                                <i class="fa fa-th-large link-dark-gray"></i>
                            </a>
                            <a class="view-action-panel-filter-button"
                               href="<?= $this->Html->url(am($namedParams, ['page_type' => 'list'])) ?>">
                                <i class="fa fa-reorder link-dark-gray"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php } ?>
            </div>
            
            <div class="profile-user-action-contents" id="UserPageContents">
                <?php if ($namedParams['page_type'] == 'list'): ?>
                    <?php if ($actionCount == 0): ?>
                        <div class="cube-img-column-frame add-action mod-only">
                            <h3><?= $canAction ? __("You haven't created any actions&hellip; yet.") : __("There is no Action.") ?></h3>
                            <?= $canAction ? $this->element('Goal/add_action_button',
                                ['goal_id' => $goalId, 'key_result_id' => $keyResultId]) : null; ?>
                        </div>
                    <?php elseif ($canAction): ?>
                        <div class="cube-img-column-frame add-action  mod-only">
                            <?= $this->element('Goal/add_action_button',
                                ['goal_id' => $goalId, 'key_result_id' => $keyResultId]); ?>
                        </div>
                    <?php endif; ?>
                    <?= $this->element('Feed/posts') ?>
                <?php elseif ($namedParams['page_type'] == 'image'): ?>
                    <?php if ($actionCount == 0): ?>
                        <div class="cube-img-column-frame add-action mod-only">
                            <h3><?= $canAction ? __("You haven't created any actions&hellip; yet.") : __("There is no Action.") ?></h3>
                            <?= $canAction ? $this->element('Goal/add_action_button',
                                ['goal_id' => $goalId, 'key_result_id' => $keyResultId]) : null; ?>
                        </div>
                    <?php elseif ($canAction): ?>
                        <div class="cube-img-column-frame add-action">
                            <?= $this->element('Goal/add_action_button',
                                ['goal_id' => $goalId, 'key_result_id' => $keyResultId]); ?>
                        </div>
                    <?php endif; ?>
                    <?= $this->element('cube_img_blocks') ?>
                <?php endif; ?>
            </div>
            <?php
            $item_num = POST_FEED_PAGE_ITEMS_NUMBER;
            if ($namedParams['page_type'] == 'image') {
                $item_num = MY_PAGE_CUBE_ACTION_IMG_NUMBER;
            }
            ?>
            <?php //投稿が指定件数　もしくは　アイテム作成日から１ヶ月以上経っている場合
            if ($actionCount > 0 &&
                (count($posts) == $item_num || $item_created < REQUEST_TIMESTAMP - MONTH)
            ): ?>

                <div class="panel-body">
                    <?php
                    $next_page_num = 2;
                    $month_index = 0;
                    $more_read_text = __("More...");
                    $oldest_post_time = 0;
                    if ((count($posts) != $item_num)) {
                        $next_page_num = 1;
                        $month_index = 1;
                        $more_read_text = __("View more");
                    }

                    // ゴールの登録日以前の投稿は存在しないので読み込まないようにする
                    if (isset($goal['Goal']['created']) && $goal['Goal']['created']) {
                        $oldest_post_time = $goal['Goal']['created'];
                    }
                    ?>
                    <div class="panel panel-default feed-read-more" id="FeedMoreRead">
                        <div class="panel-body panel-read-more-body">
                            <span class="none" id="ShowMoreNoData"><?= __("There is no further action.") ?></span>
                            <a href="#" class="click-feed-read-more"
                               onclick="Page.action_resize()"
                               parent-id="FeedMoreRead"
                               no-data-text-id="ShowMoreNoData"
                               next-page-num="<?= $next_page_num ?>"
                               month-index="<?= $month_index ?>"
                               get-url="<?=
                               $this->Html->url([
                                   'controller'     => 'posts',
                                   'action'         => 'ajax_get_user_page_post_feed',
                                   'key_result_id'  => Hash::get($namedParams, 'key_result_id'),
                                   'goal_id'        => Hash::get($namedParams, 'goal_id'),
                                   'type'           => Post::TYPE_ACTION,
                                   'page_type'      => Hash::get($namedParams, 'page_type'),
                                   'without_header' => true,
                               ]) ?>"
                               id="FeedMoreReadLink"
                               append-target-id="UserPageContents"
                               oldest-post-time="<?= $oldest_post_time ?>"
                            >
                                <?= h($more_read_text) ?> </a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?= $this->App->viewEndComment() ?>
