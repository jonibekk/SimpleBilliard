<?
/**
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Pages
 * @since         CakePHP(tm) v 0.10.0.1076
 * @var CodeCompletionView $this
 */
?>
<!-- START app/View/Goals/index.ctp -->
<div class="panel panel-default">
    <div class="panel-body">
        <div class="col col-xxs-12 goals-column-head">
            <span class="font_14px goals-column-title"><?= __d('gl', 'みんなのゴール') ?></span>

            <div class="pull-right">
                <div class="dropdown">
                    <a href="#" class="link-gray font_11px" data-toggle="dropdown" id="download">
                        <span class="line-height_20px"><?= __d('gl', "全て") ?></span><i
                            class="fa fa-caret-down gl-feed-arrow line-height_20px"></i>
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
            <div class="col col-xxs-12">
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
                       get-url="<?= $this->Html->url(['controller' => 'goals', 'action' => 'ajax_get_more_index_items']) ?>">
                        <?= __d('gl', "もっと見る ▼") ?></a>
                </div>
            <? endif; ?>
        <? endif ?>
    </div>
</div>
<!-- END app/View/Goals/index.ctp -->
