<?php
/**
 * Created by PhpStorm.
 * User: daikihirakata
 * Date: 5/19/15
 * Time: 02:16
 *
 * @var CodeCompletionView $this
 * @var                    $post
 */
?>
<!-- START app/View/Elements/Feed/goal_sharing_block.ctp -->
<div class="col col-xxs-12 pt_10px">
    <a href="<?= $this->Html->url(['controller' => 'goals', 'action' => 'ajax_get_goal_detail_modal', 'goal_id' => $goal['Goal']['id']]) ?>"
       class="no-line font_verydark modal-ajax-get">
        <div class="site-info bd-radius_4px">
            <div class="media">
                <div class="pull-left">
                    <?=
                    $this->Html->image('ajax-loader.gif',
                                       [
                                           'class'         => 'lazy media-object',
                                           'data-original' => $this->Upload->uploadUrl($post,
                                                                                       "Goal.photo",
                                                                                       ['style' => 'medium_large']),
                                           'width'         => '80px',
                                       ]
                    )
                    ?>
                </div>
                <div class="media-body">
                    <h4 class="media-heading font_18px"><?= mb_strimwidth(h($post['Goal']['name']),
                                                                          0, 50,
                                                                          "...") ?></h4>
                    <?php if (isset($post['Goal']['Purpose']['name'])): ?>
                        <div class="font_12px site-info-txt">
                            <?= mb_strimwidth(h($post['Goal']['Purpose']['name']), 0, 110, "...") ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </a>
</div>
<!-- END app/View/Elements/Feed/goal_sharing_block.ctp -->
