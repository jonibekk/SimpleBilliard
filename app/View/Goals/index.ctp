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
            <? foreach ($goals as $goal): ?>
                <div class="col col-xxs-12 my-goals-item">
                    <div class="col col-xxs-12">
                        <? if (empty($goal['SpecialKeyResult'])): ?>
                            <?= __d('gl', "ゴールなし") ?>
                        <? else: ?>
                            <b class="line-numbers ln_2"><?= h($goal['SpecialKeyResult'][0]['name']) ?></b>
                        <?endif; ?>
                    </div>
                    <div class="col col-xxs-12 font_12px line-numbers ln_1 goals-column-purpose">
                        <?= h($goal['Goal']['purpose']) ?>
                    </div>
                    <div class="col col-xxs-12">
                        <div class="progress gl-progress goals-column-progress-bar">
                            <div class="progress-bar progress-bar-info" role="progressbar"
                                 aria-valuenow="<?= h($goal['Goal']['progress']) ?>" aria-valuemin="0"
                                 aria-valuemax="100" style="width: <?= h($goal['Goal']['progress']) ?>%;">
                                <span class="ml-12px"><?= h($goal['Goal']['progress']) ?>%</span>
                            </div>
                        </div>
                    </div>
                    <div class="col col-xxs-12">
                        <? if (isset($goal['SpecialKeyResult'][0]['end_date']) && !empty($goal['SpecialKeyResult'][0]['end_date'])): ?>
                            <div class="pull-left font_12px">
                                <?=
                                __d('gl', "残り%d日",
                                    ($goal['SpecialKeyResult'][0]['end_date'] - time()) / (60 * 60 * 24)) ?>
                            </div>
                        <? endif; ?>
                        <div class="pull-right font_12px check-status">
                            <? if (isset($goal['SpecialKeyResult'][0]['valued_flg']) && $goal['SpecialKeyResult'][0]['valued_flg']): ?>
                                <i class="fa fa-check-circle icon-green"></i><?= __d('gl', "認定") ?>
                            <? else: ?>
                                <i class="fa fa-check-circle"></i><?= __d('gl', "未認定") ?>
                            <?endif; ?>
                        </div>
                    </div>
                    <div class="col col-xxs-12">
                        <?= $goal['User']['display_username'] ?>
                    </div>
                </div>
            <? endforeach ?>
        <? endif ?>
    </div>
</div>
<!-- END app/View/Goals/index.ctp -->
