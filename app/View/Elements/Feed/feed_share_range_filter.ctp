<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 8/3/15
 * Time: 12:07 PM
 *
 * @var CodeCompletionView $this
 * @var                    $current_circle
 * @var                    $user_status
 * @var                    $circle_member_count
 * @var                    $circle_status
 * @var                    $feed_filter
 */
?>
<div class="feed-share-range"
        <?php if (!$current_circle): ?>
     style="display: none"
        <?php endif; ?>
>
    <div class="panel-body ptb_10px plr_11px">
            <div class="col col-xxs-12 font_12px">
                <?= $this->element('Feed/circle_filter_menu',
                                   compact('current_circle', 'user_status', 'circle_member_count', 'circle_status')
                ) ?>
                <span class="font_verydark" id="feed-share-range-public-flg">
                    <?= __('・') ?>
                    <?php if ($current_circle['Circle']['public_flg']): ?>
                        <i class="fa fa-unlock font_14px"></i>
                    <?php else: ?>
                        <i class="fa fa-lock font_14px"></i>
                    <?php endif ?>
                </span>
            </div>
            <div class="col col-xxs-12 font_14px mtb_3px">
                <i class="fa fa-th-list"></i>
                <?php if ($this->request->params['action'] == "feed" || !$current_circle): ?>
                    <span class="feed-current-filter"><?= __('フィード') ?></span>
                <?php else: ?>
                    <?= $this->Html->link(__('フィード'),
                                          ['action' => 'feed', 'circle_id' => $current_circle['Circle']['id']],
                                          ['class' => 'font_lightgray']) ?>
                <?php endif; ?>
                <span>&nbsp;|&nbsp;</span>
                <i class="fa fa-file-o"></i>
                <?php if ($this->request->params['action'] == "attached_file_list"): ?>
                    <span class="feed-current-filter"><?= __('ファイル') ?></span>
                <?php else: ?>
                    <?= $this->Html->link(__('ファイル'),
                                          ['action' => 'attached_file_list', 'circle_id' => $current_circle['Circle']['id']],
                                          ['class' => 'font_lightgray feed-share-range-file-url']) ?>
                <?php endif; ?>

            </div>
    </div>
</div>
