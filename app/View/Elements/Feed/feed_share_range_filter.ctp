<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 8/3/15
 * Time: 12:07 PM
 *
 * @var $current_circle
 * @var $user_status
 * @var $circle_member_count
 * @var $circle_status
 * @var $feed_filter
 */
?>
<div class="feed-share-range">
    <div class="panel-body ptb_10px plr_11px">
        <div class="col col-xxs-12 font_12px">
            <?php if ($feed_filter == "all"): ?>
                <span class="feed-current-filter"><?= __d('gl', 'すべて') ?></span>
            <?php else: ?>
                <?= $this->Html->link(__d('gl', 'すべて'), "/", ['class' => 'font_lightgray']) ?>
            <?php endif; ?>
            <span> ･ </span>
            <?php if ($feed_filter == "goal"): ?>
                <span class="feed-current-filter"><?= __d('gl', 'ゴール') ?></span>
            <?php else: ?>
                <?= $this->Html->link(__d('gl', 'ゴール'),
                                      ['controller' => 'posts', 'action' => 'feed', 'filter_goal' => true],
                                      ['class' => 'font_lightgray']) ?>
            <?php endif; ?>
            <?php if ($current_circle): ?>
                <?= $this->element('Feed/circle_filter_menu',
                                   compact('current_circle', 'user_status', 'circle_member_count', 'circle_status')
                ) ?>
            <?php endif; ?>
        </div>
    </div>
</div>
