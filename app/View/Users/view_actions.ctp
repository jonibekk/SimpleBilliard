<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 7/9/15
 * Time: 3:33 PM

 *
*@var CodeCompletionView $this
 * @var                    $goal_list
 * @var                    $goal_id
 * @var                    $posts
 */
?>
<!-- START app/View/Users/view_actions.ctp -->
<div class="col-sm-8 col-sm-offset-2">
    <div class="panel panel-default">
        <?= $this->element('simplex_top_section') ?>
        <div class="panel-body">
            <div class="form-group">
                <div class="input-group">
                    <span class="input-group-addon" id=""><i class="fa fa-flag"></i></span>
                    <?=
                    $this->Form->input('goal_id', [
                        'label'                    => false,
                        'div'                      => false,
                        'required'                 => true,
                        'data-bv-notempty-message' => __d('validate', "入力必須項目です。"),
                        'class'                    => 'form-control',
                        'id'                       => 'GoalSelectForm',
                        'options'                  => $goal_list,
                        'default'                  => $goal_id,
                    ])
                    ?>
                </div>
            </div>
        </div>
        <div class="panel-body">
            <div class="pull-right">
                <ul class="list-inline">
                    <li>
                        <a href="<?= $this->Html->url(array_merge($this->request->params['named'],
                                                                  ['page_type' => 'image'])) ?>">
                            <i class="fa fa-th-large link-dark-gray"></i>
                        </a>
                    </li>
                    <li>
                        <a href="<?= $this->Html->url(array_merge($this->request->params['named'],
                                                                  ['page_type' => 'list'])) ?>">
                            <i class="fa fa-reorder link-dark-gray"></i>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <?php if ($this->request->params['named']['page_type'] == "list"): ?>
            <div class="panel-body">
                <?= $this->element("Feed/posts") ?>

                <?php if (empty($posts)): ?>
                    <div class="panel panel-default">
                        <div class="panel-body ptb_10px plr_11px ">
                            <div class="col col-xxs-12">
                                <?= __d('gl', "投稿がありません。") ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                <?php
                $next_page_num = 2;
                $month_index = 0;
                $more_read_text = __d('gl', "もっと読む ▼");
                $oldest_post_time = 0;
                if ((count($posts) != POST_FEED_PAGE_ITEMS_NUMBER)) {
                    $next_page_num = 1;
                    $month_index = 1;
                    $more_read_text = __d('gl', "さらに投稿を読み込む ▼");
                }

                // ユーザーの登録日以前の投稿は存在しないので読み込まないようにする
                if (isset($user['User']['created']) && $user['User']['created']) {
                    $oldest_post_time = $user['User']['created'];
                }

                ?>
                <div class="panel panel-default feed-read-more" id="FeedMoreRead">
                    <div class="panel-body panel-read-more-body">
                        <span class="none" id="ShowMoreNoData"><?= __d('gl', "これ以上の投稿はありませんでした。") ?></span>
                        <a href="#" class="btn btn-link click-feed-read-more"
                           parent-id="FeedMoreRead"
                           no-data-text-id="ShowMoreNoData"
                           next-page-num="<?= $next_page_num ?>"
                           month-index="<?= $month_index ?>"
                           get-url="<?=
                           $this->Html->url(['controller' => 'posts',
                                             'action'     => 'ajax_get_user_page_post_feed',
                                             'user_id'    => viaIsSet($this->request->params['named']['user_id']),
                                             'author_id'  => viaIsSet($this->request->params['named']['user_id']),
                                             'goal_id'    => viaIsSet($this->request->params['named']['goal_id']),
                                             'type'       => Post::TYPE_ACTION
                                            ]) ?>"
                           id="FeedMoreReadLink"
                           oldest-post-time="<?= $oldest_post_time ?>"
                            >
                            <?= $more_read_text ?></a>
                    </div>
                </div>
            </div>
        <?php elseif ($this->request->params['named']['page_type'] == "image"): ?>
        <?php endif; ?>
    </div>
</div>
<!-- END app/View/Users/view_actions.ctp -->
