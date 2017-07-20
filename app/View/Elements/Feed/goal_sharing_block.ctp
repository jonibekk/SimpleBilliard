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
<?= $this->App->viewStartComment() ?>
<div class="col pt_10px">
    <a href="<?= $this->Html->url([
        'controller' => 'goals',
        'action'     => 'view_krs',
        'goal_id'    => $post['Goal']['id']
    ]) ?>"
       data-url="<?= $this->Html->url([
           'controller' => 'goals',
           'action'     => 'ajax_get_goal_description_modal',
           'goal_id'    => $post['Goal']['id']
       ]) ?>"
       class="no-line font_verydark modal-ajax-get"
    >
        <div class="site-info bd-radius_4px">
            <div class="media">
                <div class="pull-left">
                    <?=
                    $this->Html->image('pre-load.svg',
                        [
                            'class'         => 'lazy media-object',
                            'data-original' => $this->Upload->uploadUrl($post,
                                "Goal.photo",
                                ['style' => 'medium_large']),
                            'width'         => '80px',
                            'height'        => '80px'
                        ]
                    )
                    ?>
                </div>
                <div class="media-body">
                    <h4 class="media-heading font_18px"><?= mb_strimwidth(h($post['Goal']['name']),
                            0, 50,
                            "...") ?></h4>
                </div>
            </div>
            <?php if ($this->Post->isDisplayableGoalButtons($post['Post'], $post['Goal'], $current_term)) : ?>
                <?php $follow_opt = $this->Goal->getFollowOption($post['Goal']) ?>
                <?php $collabo_opt = $this->Goal->getCollaboOption($post['Goal']) ?>
                <div class="col feeds-post-goal-buttons">
                    <div class="col col-xxs-6 col-xs-4 w_50per">
                        <a goal-id="<?= $post['Goal']['id'] ?>" data-class="toggle-follow" href="#"
                           class="btn btn-white font_verydark bd-circle_22px toggle-follow p_8px width100_per <?= h($follow_opt['class']) ?>"
                        <?= h($follow_opt['disabled']) ?>="<?= h($follow_opt['disabled']) ?>">
                        <i class="fa fa-heart font_rougeOrange" style="<?= h($follow_opt['style']) ?>"></i>
                        <span class="ml_5px"><?= h($follow_opt['text']) ?></span>
                        </a>
                    </div>
                    <div class="col col-xxs-5 col-xs-4 w_50per">
                        <a href="#"
                           data-url="<?= $this->Html->url([
                               'controller' => 'goals',
                               'action'     => 'ajax_get_collabo_change_modal',
                               'goal_id'    => $post['Goal']['id']
                           ]) ?>"
                           data-target="#ModalCollabo_<?= $post['Goal']['id'] ?>" data-toggle="modal"
                           class="btn btn-white bd-circle_22px font_verydark collaborate-button modal-ajax-get-collab p_8px width100_per <?= h($collabo_opt['class']) ?>">
                            <i style="" class="fa fa-child font_rougeOrange font_18px"></i>
                            <span class="ml_5px font_14px"><?= h($collabo_opt['text']) ?></span>
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </a>
</div>
<?= $this->App->viewEndComment() ?>
