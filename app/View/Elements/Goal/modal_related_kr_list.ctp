<?php
/**
 * Created by PhpStorm.
 * User: daikihirakata
 * Date: 2014/07/19
 * Time: 22:11
 *
 * @var                    $krs
 * @var CodeCompletionView $this
 */
?>
<?= $this->App->viewStartComment()?>
<div class="modal-dialog eval-modal">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close font_33px close-design" data-dismiss="modal" aria-hidden="true"><span
                    class="close-icon">&times;</span></button>
            <h4 class="modal-title"><?= __("KRs") ?> (<span class="<?= $myActionKrCount > 0 ? 'modal-title-red' : '' ?>"><?= $myActionKrCount ?></span>/<?= $allKrCount ?>)</h4>
        </div>
        <div class="modal-body">
            <ul class="kr-items">
                <?php foreach ($krs as $kr): ?>
                <?php $actionCount = count($kr['ActionResult']); ?>
                <?php $actioned = $actionCount > 0 ? 'actioned' : ''; ?>
                <li class="kr-item <?= !$actioned ? 'no-action-kr-card' : '' ?>">
                    <a class="kr-item-link click-show-post-modal"
                       id="ActionListOpen_<?= $kr['goal_id'] ?>"
                       href="<?= $this->Html->url([
                           'controller'       => 'posts',
                           'action'           => 'ajax_get_goal_action_feed',
                           'goal_id'          => $kr['goal_id'],
                           'key_result_id'    => $kr['id'],
                           'type'             => Post::TYPE_ACTION,
                           'author_id'        => $userId,
                           'evaluate_term_id' => $evaluateTermId
                       ]) ?>">
                        <div class="kr-card flex">
                            <div class="kr-card-main">
                                <h4 class="kr-card-title">
                                    <?= h($kr['name']) ?>
                                </h4>

                                <!--progress bar-->
                                <? // TODO:KR進捗機能がリリースされ次第、進捗率に合わせたバーの塗りつぶしと「{現在の進捗}/{目標値}」の表示に変える ?>
                                <div class="kr-progress mtb_4px">
                                    <span class="kr-progress-text"><?= $kr['display_value'] ?></span>
                                    <div class="kr-progress-bar mod-incomplete rate-0"></div>
                                </div>
                                <dl class="kr-info-counts mb_0px">
                                    <dt class="kr-info-counts-title <?= $actioned ?>"><i class="fa fa-check-circle"></i></dt>
                                    <dd class="kr-info-counts-description <?= $actioned ?>">
                                        <?= $actionCount ?>
                                    </dd>
                                    <dt class="kr-info-counts-title"><i class="fa fa-database"></i></dt>
                                    <dd class="kr-info-counts-description">
                                        <?php if ($kr['tkr_flg']):?>
                                            TKR
                                        <?php else:?>
                                            <?=$kr['priority']?>
                                        <?php endif;?>
                                    </dd>
                                </dl>
                            </div>
                            <div class="kr-card-side">
                                <i class="fa fa-angle-right"></i>
                            </div>
                        </div>
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>
<?= $this->App->viewEndComment()?>
