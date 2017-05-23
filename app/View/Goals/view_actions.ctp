<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 7/9/15
 * Time: 3:33 PM
 *
 * @var CodeCompletionView $this
 * @var                    $posts
 * @var                    $kr_select_options
 * @var                    $goal_id
 * @var                    $goal_base_url
 * @var                    $key_result_id
 * @var                    $item_created
 */
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
                            $existKR = isset($this->request->params['named']['key_result_id']);
                            if ($existKR):
                                echo $kr_select_options[$this->request->params['named']['key_result_id']];
                            else:
                                echo "All";
                            endif; ?>&nbsp;</strong><span class="fa fa-angle-down ml_2px"></span>
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenu2">
                        <?php

                        foreach ($kr_select_options as $krID => $krName) { ?>
                            <?php if ($krName == "_separator_"): ?>
                                <li role="separator" class="divider"></li>
                            <?php elseif ($krName == "All"): ?>
                                <li>
                                    <a href="/goals/view_actions/goal_id:<?php echo $goal_id ?>/page_type:<?php echo $this->request->params['named']['page_type'] ?>/"><?php echo $krName ?></a>
                                </li>
                            <?php else: ?>
                                <li>
                                    <a href="/goals/view_actions/goal_id:<?php echo $goal_id ?>/page_type:<?php echo $this->request->params['named']['page_type'] ?>/key_result_id:<?php echo $krID ?>"><?php echo $krName ?></a>
                                </li>
                            <?php endif; ?>
                        <?php }
                        unset($krName);
                        unset($krID);
                        ?>
                    </ul>
                </div>
                <div class="view-actions-panel-btngroup-wrap">
                    <div class="view-action-panel-filter-btngroup">
                        <?php if ($this->request->params['named']['page_type'] == 'list'): ?>
                            <a class="view-action-panel-filter-button"
                               href="<?= $this->Html->url(array_merge($this->request->params['named'],
                                   ['page_type' => 'image'])) ?>">
                                <i class="fa fa-th-large link-dark-gray"></i>
                            </a>
                            <a class="view-action-panel-filter-button mod-active"
                               href="<?= $this->Html->url(array_merge($this->request->params['named'],
                                   ['page_type' => 'list'])) ?>">
                                <i class="fa fa-reorder link-dark-gray"></i>
                            </a>
                        <?php elseif ($this->request->params['named']['page_type'] == 'image'): ?>
                            <a class="view-action-panel-filter-button mod-active"
                               href="<?= $this->Html->url(array_merge($this->request->params['named'],
                                   ['page_type' => 'image'])) ?>">
                                <i class="fa fa-th-large link-dark-gray"></i>
                            </a>
                            <a class="view-action-panel-filter-button"
                               href="<?= $this->Html->url(array_merge($this->request->params['named'],
                                   ['page_type' => 'list'])) ?>">
                                <i class="fa fa-reorder link-dark-gray"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="profile-user-action-contents" id="UserPageContents">
                <?php if ($this->request->params['named']['page_type'] == 'list'): ?>
                    <?php if (count($posts) == 0 && $is_collaborated): ?>
                        <div class="cube-img-column-frame add-action mod-only">
                            <h3>You haven't created any actions&hellip; yet.</h3>
                            <div class="profile-user-action-contents-add-image">
                                <span><a href="/goals/add_action/goal_id:<?= $goal_id ?>">+</a></span>
                            </div>
                            <a href="/goals/add_action/goal_id:<?= $goal_id ?>"><?= __('Add Action') ?></a>
                        </div>
                    <?php elseif (count($posts) == 0 && !$is_collaborated): ?>
                        <div class="cube-img-column-frame add-action mod-only">
                            <h3>No actions have been created&hellip; yet.</h3>
                        </div>
                    <?php elseif (count($posts) > 0 && $is_collaborated): ?>
                        <div class="cube-img-column-frame add-action  mod-only">
                            <div class="profile-user-action-contents-add-image">
                                <span><a href="/goals/add_action/goal_id:<?= $goal_id ?>">+</a></span>
                            </div>
                            <a href="/goals/add_action/goal_id:<?= $goal_id ?>"><?= __('Add Action') ?></a>
                        </div>
                    <?php endif; ?>
                    <?= $this->element('Feed/posts') ?>
                <?php elseif ($this->request->params['named']['page_type'] == 'image'): ?>
                    <?php if (count($posts) == 0 && $is_collaborated): ?>
                        <div class="cube-img-column-frame add-action mod-only">
                            <h3><?=__("You haven't created any actions&hellip; yet.")?></h3>
                            <div class="profile-user-action-contents-add-image">
                                <span><a href="/goals/add_action/goal_id:<?= $goal_id ?>">+</a></span>
                            </div>
                            <a href="/goals/add_action/goal_id:<?= $goal_id ?>"><?= __('Add Action') ?></a>
                        </div>
                    <?php elseif (count($posts) == 0 && !$is_collaborated): ?>
                        <div class="cube-img-column-frame add-action mod-only">
                            <h3>No actions have been created&hellip; yet.</h3>
                        </div>
                    <?php elseif (count($posts) > 0 && $is_collaborated): ?>
                        <div class="cube-img-column-frame add-action">
                            <div class="profile-user-action-contents-add-image">
                                <span><a href="/goals/add_action/goal_id:<?= $goal_id ?>">+</a></span>
                            </div>
                            <a href="/goals/add_action/goal_id:<?= $goal_id ?>"><?= __('Add Action') ?></a>
                        </div>
                    <?php endif; ?>
                    <?= $this->element('cube_img_blocks') ?>
                <?php endif; ?>
            </div>
            <?php
            $item_num = POST_FEED_PAGE_ITEMS_NUMBER;
            if ($this->request->params['named']['page_type'] == 'image') {
                $item_num = MY_PAGE_CUBE_ACTION_IMG_NUMBER;
            }
            ?>
            <?php //投稿が指定件数　もしくは　アイテム作成日から１ヶ月以上経っている場合
            if (count($posts) == $item_num || $item_created < REQUEST_TIMESTAMP - MONTH): ?>

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
                                   'key_result_id'  => Hash::get($this->request->params, 'named.key_result_id'),
                                   'goal_id'        => Hash::get($this->request->params, 'named.goal_id'),
                                   'type'           => Post::TYPE_ACTION,
                                   'page_type'      => Hash::get($this->request->params, 'named.page_type'),
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
