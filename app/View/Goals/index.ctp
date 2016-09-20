<?php /**
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Pages
 * @since         CakePHP(tm) v 0.10.0.1076
 * @var CodeCompletionView $this
 * @var                    $is_admin
 */
?>
<?= $this->App->viewStartComment()?>
<?php if ($is_admin && $this->Session->read('ua.device_type') == 'Desktop'): ?>
    <div class="panel panel-default feed-share-range">
        <div class="panel-body ptb_10px plr_11px">
            <div class="col col-xxs-12 font_12px">
                <?= $this->Form
                    ->postLink("<i class='fa fa-download'></i> " . __('Export Data'),
                        [
                            'action' => 'download_all_goal_csv',
                        ],
                        [
                            'class'  => 'pull-right font_verydark',
                            'escape' => false,
                        ]
                    );
                ?>
            </div>
        </div>
    </div>
<?php endif; ?>
<div class="panel panel-default">
    <div class="panel-body">
        <div class="col col-xxs-12 goals-feed-head">
            <span class="font_14px goals-column-title"><?= __('Our Goals') ?></span>
        </div>
        <div class="goal-search-menu">
            <div class="goal-term-search-menu btn-group btn-group-justified" role="group">
                <?php foreach ($search_options['term'] as $key => $val): ?>
                    <?php if ($val == $search_option['term'][1]): ?>
                        <a href="<?= $this->Html->url(array_merge($search_url, ['term' => $key])) ?>"
                           class="goals-search-terms-tab selected" role="button"><?= $val ?></a>
                    <?php else: ?>
                        <a href="<?= $this->Html->url(array_merge($search_url, ['term' => $key])) ?>"
                           class="goals-search-terms-tab" role="button"><?= h($val) ?></a>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
            <div class="goals-filters-menu btn-group btn-group-justified" role="group">
                <div class="goals-search-filters-category-wrap btn-group" role="group">
                    <a href="#" class="goals-search-filters-category dropdown-toggle" data-toggle="dropdown"
                       role="button" aria-expanded="false">
                        <span
                            class="goals-search-filters-tab-text goal_type_name"><?= h($search_option['category'][1]) ?></span>
                        <i class="goals-search-filters-tab-caret fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu" role="menu">
                        <?php foreach ($search_options['category'] as $key => $val): ?>
                            <li><a href="<?= $this->Html->url(array_merge($search_url,
                                    ['category' => $key])) ?>"><?= h($val) ?></a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div class="goals-search-filters-category-wrap btn-group" role="group">
                    <a href="#" class="goals-search-filters-completed dropdown-toggle" data-toggle="dropdown"
                       role="button" aria-expanded="false">
                        <span
                            class="goals-search-filters-tab-text goal_type_name"><?= h($search_option['progress'][1]) ?></span>
                        <i class="goals-search-filters-tab-caret fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu" role="menu">
                        <?php foreach ($search_options['progress'] as $key => $val): ?>
                            <li><a href="<?= $this->Html->url(array_merge($search_url,
                                    ['progress' => $key])) ?>"><?= h($val) ?></a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div class="goals-search-filters-order-wrap btn-group" role="group">
                    <a href="#" class="goals-search-filters-order goal-filter-elm dropdown-toggle"
                       data-toggle="dropdown" role="button" aria-expanded="false">
                        <span
                            class="goals-search-filters-tab-text goal_type_name"><?= h($search_option['order'][1]) ?></span>
                        <i class="goals-search-filters-tab-caret fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu pull-right" role="menu">
                        <?php foreach ($search_options['order'] as $key => $val): ?>
                            <li><a href="<?= $this->Html->url(array_merge($search_url,
                                    ['order' => $key])) ?>"><?= $val ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
        <p class="goals-search-count"><?= __("Goals found") ?><span
                class="goals-search-count-number"><?= $goal_count ?></span></p>

        <div class="goals-page-cards">
            <?php if (empty($goals)): ?>
                <div class="col col-xxs-12 mt_16px">
                    <div class="alert alert-warning fade in" role="alert">
                        <?= __("No Goal") ?>
                    </div>
                </div>
            <?php else: ?>
                <?= $this->element('Goal/index_items', compact('goals', 'my_coaching_users')) ?>
                <? $more_read_url = array_merge($search_url, ['action' => 'ajax_get_more_index_items']) ?>
                <?php if (count($goals) == GOAL_INDEX_ITEMS_NUMBER): ?>
                    <div class="panel-body panel-read-more-body" id="GoalMoreView">
                        <a id="FeedMoreReadLink" href="#" class="btn btn-link click-feed-read-more"
                           parent-id="GoalMoreView"
                           next-page-num="2"
                           get-url="<?= $this->Html->url($more_read_url) ?>">
                            <?= __("View more") ?>▼</a>
                    </div>
                <?php endif; ?>
            <?php endif ?>
        </div>
    </div>
</div>
<?= $this->App->viewEndComment()?>
