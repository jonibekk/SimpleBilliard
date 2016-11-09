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
<div class="modal-dialog eval-kr-modal">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close font_33px close-design" data-dismiss="modal" aria-hidden="true"><span
                    class="close-icon">&times;</span></button>
            <h4 class="modal-title"><?= __("KRs") ?> (<?= $myActionKrCount ?>/<?= $allKrCount ?>)</h4>
        </div>
        <div class="modal-body">
            <ul class="goals-approval-list">
                <?php foreach ($krs as $kr): ?>
                <li class="goals-approval-list-item is-complete">
                    <a class="goals-approval-list-item-link click-show-post-modal pointer"
                       id="ActionListOpen_<?= $kr['goal_id'] ?>"
                       href="<?= $this->Html->url([
                           'controller'       => 'posts',
                           'action'           => 'ajax_get_goal_action_feed',
                           'goal_id'          => $kr['goal_id'],
                           'type'             => Post::TYPE_ACTION,
                           'author_id'        => $this->Session->read('Auth.User.id')
                       ]) ?>">
                        <div class="goal-detail-kr-block-main">
                            <h4 class="goal-detail-kr-card-title">
                                <?= h($kr['name']) ?>
                            </h4>
                            <?php if ($kr['completed']): ?>
                                <?= __('Clear') ?>
                            <?php endif ?>

                            <!--progress bar-->
                            <? // TODO:KR進捗機能がリリースされ次第、進捗率に合わせたバーの塗りつぶしと「{現在の進捗}/{目標値}」の表示に変える ?>
                            <div class="goal-detail-kr-progress-bar-wrap mtb_4px">
                                <span class="goal-detail-kr-progress-text"><?= $kr['display_value'] ?></span>
                                <div class="goal-detail-kr-progress-bar mod-incomplete rate-0"></div>
                            </div>
                            <dl class="goal-detail-kr-info-counts mb_4px">
                                <dt class="goal-detail-kr-info-counts-title"><i class="fa fa-check-circle"></i></dt>
                                <dd class="goal-detail-kr-info-counts-description">
                                    <?= $kr['action_result_count']?>
                                </dd>
                                <dt class="goal-detail-kr-info-counts-title"><i class="fa fa-database"></i></dt>
                                <dd class="goal-detail-kr-info-counts-description">
                                    <?php if ($kr['tkr_flg']):?>
                                        TKR
                                    <?php else:?>
                                        <?=$kr['priority']?>
                                    <?php endif;?>
                                </dd>
                            </dl>
                        </div>
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>
<?= $this->App->viewEndComment()?>
