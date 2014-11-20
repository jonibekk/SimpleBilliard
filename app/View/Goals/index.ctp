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

            <div class="pull-right">
                <div class="dropdown">
                    <a href="#" class="font_lightgray font_11px" data-toggle="dropdown" id="download">
                        <span class="lh_20px"><?= __d('gl', "全て") ?></span><i
                            class="fa fa-caret-down feed-arrow lh_20px"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-right frame-arrow-icon" role="menu"
                        aria-labelledby="dropdownMenu1">
                        <li role="presentation"><a role="menuitem" tabindex="-1" href="#"><?=
                                __d('gl',
                                    "完了しているゴール") ?></a>
                        </li>
                        <li role="presentation"><a role="menuitem" tabindex="-1" href="#"><?= __d('gl', "今期のゴール") ?></a>
                        </li>
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
            <? if (count($goals) == 20): ?>
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
