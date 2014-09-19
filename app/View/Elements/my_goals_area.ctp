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
            <?= __d('gl', '自分のゴール') ?>
            <div class="pull-right">
                <div class="dropdown">
                    <a href="#" class="link-gray font-size_11" data-toggle="dropdown" id="download">
                        <?= __d('gl', "全て") ?><i class="fa fa-chevron-down gl-feed-arrow"></i>
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
        <? if (empty($my_goals)): ?>
            <div class="col col-xxs-12">
                <a href="<?= $this->Html->url(['controller' => 'goals', 'action' => 'add']) ?>"><i
                        class="fa fa-plus-circle"></i><?= __d('gl', 'ゴールを追加する') ?></a>
            </div>
        <? else: ?>
            <? foreach ($my_goals as $goal): ?>
                <div class="col col-xxs-12">
                    <div class="row">
                        <div class="col col-xxs-12">
                            <? if (empty($goal['KeyResult'])): ?>
                                <a href="<?= $this->Html->url(['controller' => 'goals', 'action' => 'add', $goal['Goal']['id'], 'mode' => 2]) ?>"><i
                                        class="fa fa-plus-circle"></i><?= __d('gl', 'ゴールを追加する') ?></a>
                            <? else: ?>
                                <b><?= h($goal['KeyResult'][0]['name']) ?></b>
                            <?endif; ?>
                        </div>
                        <div class="col col-xxs-12">
                            <?= h($goal['Goal']['purpose']) ?>
                        </div>

                    </div>
                </div>
            <? endforeach ?>
        <?endif; ?>
    </div>
</div>
<!-- END app/View/Elements/my_goals_area.ctp -->