<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 8/7/14
 * Time: 11:36 AM
 *
 * @var CodeCompletionView $this
 * @var                    $my_goals
 */
?>
<!-- START app/View/Elements/my_goals_area.ctp -->
<div class="panel panel-default">
    <div class="panel-body">
        <div class="col col-xxs-12">
            <span class="font-size_14 goals-column-head"><?= __d('gl', 'あなたのゴール') ?></span>

            <div class="pull-right">
                <div class="dropdown">
                    <a href="#" class="link-gray font-size_11" data-toggle="dropdown" id="download">
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
        <? foreach ($my_goals as $goal): ?>
            <div class="col col-xxs-12 my-goals-item">
                <div class="col col-xxs-12">
                    <div class="pull-right goals-column-function">
                        <div class="dropdown">
                            <a href="#" class="link-gray font-size_14" data-toggle="dropdown" id="download">
                                <i class="fa fa-cog"><i class="fa fa-caret-down goals-column-fa-caret-down"></i></i>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-right frame-arrow-icon" role="menu"
                                aria-labelledby="dropdownMenu1">
                                <li role="presentation"><a role="menuitem" tabindex="-1"
                                                           href="<?= $this->Html->url(['controller' => 'goals', 'action' => 'add', $goal['Goal']['id'], 'mode' => 3]) ?>"><?=
                                        __d('gl',
                                            "編集") ?></a>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <? if (empty($goal['SpecialKeyResult'])): ?>
                        <a href="<?= $this->Html->url(['controller' => 'goals', 'action' => 'add', $goal['Goal']['id'], 'mode' => 2]) ?>"><i
                                class="fa fa-plus-circle"></i><?= __d('gl', 'ゴールを追加する') ?></a>
                    <? else: ?>
                        <b class="line-numbers ln_2"><?= h($goal['SpecialKeyResult'][0]['name']) ?></b>
                    <?endif; ?>
                </div>
                <div class="col col-xxs-12 font-size_12 line-numbers ln_1">
                    <?= h($goal['Goal']['purpose']) ?>
                </div>
                <div class="col col-xxs-12">
                    <div class="progress gl-progress">
                        <div class="progress-bar progress-bar-info" role="progressbar"
                             aria-valuenow="<?= h($goal['Goal']['progress']) ?>" aria-valuemin="0"
                             aria-valuemax="100" style="width: <?= h($goal['Goal']['progress']) ?>%;">
                            <?= h($goal['Goal']['progress']) ?>%
                        </div>
                    </div>
                </div>
                <div class="col col-xxs-12">
                    <? if (isset($goal['SpecialKeyResult'][0]['end_date']) && !empty($goal['SpecialKeyResult'][0]['end_date'])): ?>
                        <div class="pull-left">
                            <?=
                            __d('gl', "残り%d日",
                                ($goal['SpecialKeyResult'][0]['end_date'] - time()) / (60 * 60 * 24)) ?>
                        </div>
                    <? endif; ?>
                    <div class="pull-right font-size_12 check-status">
                        <? if (isset($goal['SpecialKeyResult'][0]['valued_flg']) && $goal['SpecialKeyResult'][0]['valued_flg']): ?>
                            <i class="fa fa-check-circle icon-green"></i><?= __d('gl', "認定") ?>
                        <? else: ?>
                            <i class="fa fa-check-circle"></i><?= __d('gl', "未認定") ?>
                        <?endif; ?>
                    </div>
                </div>
            </div>
        <? endforeach ?>
        <div class="col col-xxs-12 goals-column-empty-box">
            <a href="<?= $this->Html->url(['controller' => 'goals', 'action' => 'add']) ?>" class="link-gray">
                <div class="goals-column-empty-icon"><i class="fa fa-plus-circle font-size_33"></i></div>
                <div class="goals-column-empty-text font-size_14"><?= __d('gl', '新しいゴールをつくる') ?></div>
            </a>
        </div>

    </div>
</div>
<!-- END app/View/Elements/my_goals_area.ctp -->