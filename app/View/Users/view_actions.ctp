<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 7/9/15
 * Time: 3:33 PM.
 *
 * @var CodeCompletionView
 * @var
 * @var
 * @var
 * @var
 * @var
 */
?>
<?= $this->App->viewStartComment() ?>
<div class="users-view-actions col-sm-8 col-sm-offset-2">
    <?= $this->element('User/simplex_top_section') ?>
    <div class="users-view-actions-panel">
        <div class="users-view-actions-panel-term input-group">
            <span class="users-view-actions-panel-term-icon input-group-addon">
                <i class="profile-user-action-related-goal-icon fa fa-flag"></i>
            </span>
            <?=
            $this->Form->input('goal_id', [
                'label'                    => false,
                'div'                      => false,
                'required'                 => true,
                'data-bv-notempty-message' => __('Input is required.'),
                'class'                    => 'users-view-actions-panel-term-select form-control disable-change-warning',
                'id'                       => 'SwitchGoalOnMyPage',
                'options'                  => $goal_select_options,
                'disabled'                 => ['disable_value1', 'disable_value2'],
                'default'                  => $goal_id,
                'redirect-url'             => $goal_base_url,
                'wrapInput'                => false,
            ])
            ?>
        </div>

        <ul class="profile-user-action-view-switch">
            <li class="profile-user-action-view-switch-img">
                <a href="<?= $this->Html->url(array_merge($this->request->params['named'],
                    ['page_type' => 'image'])) ?>">
                    <i class="fa fa-th-large link-dark-gray"></i>
                </a>
            </li>
            <li class="profile-user-action-view-switch-feed">
                <a href="<?= $this->Html->url(array_merge($this->request->params['named'],
                    ['page_type' => 'list'])) ?>">
                    <i class="fa fa-reorder link-dark-gray"></i>
                </a>
            </li>
        </ul>


        <div class="profile-user-action-contents" id="UserPageContents">
            <?php if ($this->request->params['named']['page_type'] == 'list'): ?>
                <?= $this->element('Feed/posts') ?>
            <?php elseif ($this->request->params['named']['page_type'] == 'image'): ?>
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
                $more_read_text = __('More...');
                $oldest_post_time = 0;
                if ((count($posts) != $item_num)) {
                    $next_page_num = 1;
                    $month_index = 1;
                    $more_read_text = __('View more');
                }

                // ユーザーの登録日以前の投稿は存在しないので読み込まないようにする
                if (isset($user['User']['created']) && $user['User']['created']) {
                    $oldest_post_time = $user['User']['created'];
                }
                ?>
                <div class="panel panel-default feed-read-more" id="FeedMoreRead">
                    <div class="panel-body panel-read-more-body">
                        <span class="none" id="ShowMoreNoData"><?= __('There is no further action.') ?></span>
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
                               'author_id'  => Hash::get($this->request->params, 'named.user_id'),
                               'goal_id'    => Hash::get($this->request->params, 'named.goal_id'),
                               'type'       => Post::TYPE_ACTION,
                               'page_type'  => Hash::get($this->request->params, 'named.page_type'),
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
<?= $this->App->viewEndComment() ?>
