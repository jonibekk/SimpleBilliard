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
                            <?= __d('gl', "ゴール未設定") ?>
                        <? else: ?>
                            <b class="line-numbers ln_2"><?= h($goal['SpecialKeyResult'][0]['name']) ?></b>
                        <?endif; ?>
                    </div>
                    <? if (!empty($goal['SpecialKeyResult'])): ?>
                        <div class="col col-xxs-12">
                            <? if (!empty($goal['SpecialKeyResult'][0]['Leader'])): ?>
                                <?=
                                __d('gl', "リーダー: %s",
                                    h($goal['SpecialKeyResult'][0]['Leader'][0]['User']['display_username'])) ?>
                            <? endif; ?>
                            | <?= __d('gl', "コラボ: ") ?>
                            <? if (count($goal['SpecialKeyResult'][0]['Collaborator']) == 0): ?>
                                <?= __d('gl', "0人") ?>
                            <? else: ?>
                                <? foreach ($goal['SpecialKeyResult'][0]['Collaborator'] as $key => $collaborator): ?>
                                    <?= h($collaborator['User']['display_username']) ?>
                                    <? if (isset($goal['SpecialKeyResult'][0]['Collaborator'][$key + 1])) {
                                        echo ", ";
                                    } ?>
                                    <? if ($key == 1) {
                                        break;
                                    } ?>
                                <? endforeach ?>
                                <? if (($other_count = count($goal['SpecialKeyResult'][0]['Collaborator']) - 2) > 0): ?>
                                    <?= __d('gl', "他%s人", $other_count) ?>
                                <? endif; ?>
                            <?endif; ?>
                        </div>
                    <? endif; ?>
                </div>
            <? endforeach ?>
        <? endif ?>
    </div>
</div>
<!-- END app/View/Goals/index.ctp -->
