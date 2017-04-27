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
<div class="col col-xxs-12 pt_10px">
    <a href="<?= $this->Html->url([
        'controller' => 'goals',
        'action'     => 'view_info',
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
        </div>
    </a>
</div>
<?= $this->App->viewEndComment() ?>
