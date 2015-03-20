<?
/**
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Pages
 * @since         CakePHP(tm) v 0.10.0.1076
 * @var CodeCompletionView $this
 * @var                    $is_admin
 */
?>
<!-- START app/View/Goals/index.ctp -->
<? if ($is_admin): ?>
    <div class="panel panel-default feed-share-range">
        <div class="panel-body ptb_10px plr_11px">
            <div class="col col-xxs-12 font_12px">
                <?= $this->Form
                    ->postLink("<i class='fa fa-download'></i> " . __d('gl', 'CSVの書き出し'),
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
<? endif; ?>
<div class="panel panel-default">
    <div class="panel-body">
        <div class="col col-xxs-12 goals-feed-head">
            <span class="font_14px goals-column-title"><?= __d('gl', 'みんなのゴール') ?></span>
        </div>
        <div class="goal-search-menu">
            <div class="goal-term-search-menu btn-group btn-group-justified" role="group">
                <a href="#" class="btn btn-default goal-search-elm" role="button"><?= __d('gl', '今期') ?></a>
                <a href="#" class="btn btn-default goal-search-elm" role="button"><?= __d('gl', '前期') ?></a>
                <a href="#" class="btn btn-default goal-search-elm" role="button"><?= __d('gl', 'もっと前') ?></a>
            </div>
            <div class="goal-filter-menu btn-group btn-group-justified" role="group">
                <div class=" btn-group" role="group">
                    <a href="#" class="btn btn-default goal-filter-elm dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                        すべて <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu" role="menu">
                        <li><a href="#">すべて</a></li>
                        <li><a href="#">職務</a></li>
                        <li><a href="#">成長</a></li>
                    </ul>
                </div>
                <div class="btn-group" role="group">
                    <a href="#" class="btn btn-default goal-filter-elm dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                        未達成 <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu" role="menu">
                        <li><a href="#">すべて</a></li>
                        <li><a href="#">未達成</a></li>
                        <li><a href="#">達成した</a></li>
                    </ul>
                </div>
                <div class="btn-group " role="group">
                    <a href="#" class="btn btn-default goal-filter-elm dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                        新着順 <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu pull-right" role="menu">
                        <li><a href="#">新着順</a></li>
                        <li><a href="#">アクションが多い順</a></li>
                        <li><a href="#">出した成果が多い順</a></li>
                        <li><a href="#">フォロワーが多い順</a></li>
                        <li><a href="#">コラボレーターが多い順</a></li>
                        <li><a href="#">進捗率が高い順</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <? if (empty($goals)): ?>
            <div class="col col-xxs-12 mt_16px">
                <div class="alert alert-warning fade in" role="alert">
                    <?= __d('gl', "ゴールがありません。") ?>
                </div>
            </div>
        <? else: ?>
            <?= $this->element('Goal/index_items') ?>
            <? if (count($goals) == 300)://TODO 暫定的に300、いずれ20に戻す ?>
                <div class="panel-body panel-read-more-body" id="GoalMoreView">
                    <a href="#" class="btn btn-link click-feed-read-more"
                       parent-id="GoalMoreView"
                       next-page-num="2"
                       month-index="1"
                       get-url="<?= $this->Html->url(['controller' => 'goals', 'action' => 'ajax_get_more_index_items']) ?>">
                        <?= __d('gl', "もっと見る ▼") ?></a>
                </div>
            <? endif; ?>
        <? endif ?>
    </div>
</div>
<!-- END app/View/Goals/index.ctp -->
