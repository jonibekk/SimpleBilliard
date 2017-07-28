<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 8/3/15
 * Time: 12:21 PM
 *
 * @var $current_circle
 * @var $user_status
 */
?>
<?= $this->App->viewStartComment() ?>
<?php if ($current_circle && $user_status != 'admin'): ?>
    <?php if ($user_status != 'joined') { ?>
        <div class="panel panel-default">
            <div class="panel-body ptb_10px plr_11px ">
                <div class="col col-xxs-12">

                    Join this Circle to post or comment.
                    <a href="<?= $this->Html->url([
                        'controller' => 'posts',
                        'action'     => 'join_circle',
                        'circle_id'  => $current_circle['Circle']['id']
                    ]) ?>"
                       class='btn btn-primary pull-right'
                       onclick="this.classList.add('disabled')">
                        <i class="fa fa-user-plus"></i> <?= __('Join circle') ?>
                    </a>
                </div>
            </div>
        </div>
        <?php if (!empty($current_circle['Circle']['description'])) : ?>
            <div class="panel panel-default">
                <h4 style='margin-left:15px;font-weight:bold'>About </h4>

                <div class="panel-body ptb_10px plr_11px ">
                    <?= h($current_circle['Circle']['description']); ?>
                </div>
            </div>
        <?php endif; ?>
    <?php } ?>
<?php endif; ?>
<?= $this->App->viewEndComment() ?>
