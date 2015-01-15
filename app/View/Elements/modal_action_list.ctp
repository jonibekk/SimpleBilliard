<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 6/6/14
 * Time: 3:19 PM
 *
 * @var CodeCompletionView $this
 * @var                    $goal
 * @var                    $priority_list
 */
?>
<!-- START app/View/Elements/modal_action_list.ctp -->
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close font_33px close-design" data-dismiss="modal" aria-hidden="true">
                <span class="close-icon">&times;</span></button>
            <h4 class="modal-title"><?= __d('gl', "アクションリスト") ?></h4>
        </div>
        <div class="modal-body" id="ActionListBody">
            <? if (!empty($posts)): ?>
                <? if (isset($posts[0]['Goal']['id']) && $posts[0]['Goal']['id']): ?>
                    <!--START Goal Post Header -->
                    <div class="panel panel-default">
                        <div class="panel-body pt_10px plr_11px pb_8px bd-b">
                            <div class="col col-xxs-12">
                                <div class="pull-right">
                                    <a href="<?= $this->Html->url(['controller' => 'goals', 'action' => 'ajax_get_goal_detail_modal', $posts[0]['Goal']['id']]) ?>"
                                       class="no-line font_verydark modal-ajax-get">
                                        <?=
                                        $this->Html->image('ajax-loader.gif',
                                                           [
                                                               'class'         => 'lazy media-object',
                                                               'data-original' => $this->Upload->uploadUrl($posts[0],
                                                                                                           "Goal.photo",
                                                                                                           ['style' => 'small']),
                                                               'width'         => '32px',
                                                               'error-img'     => "/img/no-image-link.png",
                                                           ]
                                        )
                                        ?>
                                    </a>
                                </div>
                                <div class="ln_contain w_88per h_28px">
                                    <a href="<?= $this->Html->url(['controller' => 'goals', 'action' => 'ajax_get_goal_detail_modal', $posts[0]['Goal']['id']]) ?>"
                                       class="no-line font_verydark modal-ajax-get">
                                        <i class="fa fa-flag font_gray">&nbsp;<?= h($posts[0]['Goal']['name']) ?></i>
                                    </a>

                                </div>
                            </div>
                        </div>
                    </div>
                    <!--END Goal Post Header -->
                <? endif; ?>
            <? endif; ?>
            <?= $this->element('Feed/action_posts') ?>
            <? if (count($posts) == 20): ?>
                <div class="panel panel-default feed-read-more" id="ActionListMoreRead">
                    <div class="panel-body panel-read-more-body">
                        <span class="none" id="ActionShowMoreNoData"><?= __d('gl', "これ以上のデータがありません。") ?></span>
                        <a href="#" class="btn btn-link click-feed-read-more"
                           parent-id="ActionListMoreRead"
                           next-page-num="2"
                           no-data-text-id="ActionShowMoreNoData"
                           get-url="<?=
                           $this->Html->url($feed_more_read_url) ?>"
                            >
                            <?= __d('gl', "もっと読む ▼") ?></a>
                    </div>
                </div>
            <? endif; ?>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default"
                    data-dismiss="modal"><?= __d('gl', "閉じる") ?></button>
        </div>
    </div>
</div>
<!-- END app/View/Elements/modal_action_list.ctp -->
