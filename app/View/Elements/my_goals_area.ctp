<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 8/7/14
 * Time: 11:36 AM
 *
 * @var CodeCompletionView $this
 * @var                    $my_goals
 * @var                    $collabo_goals
 * @var                    $follow_goals
 * @var                    $vision
 */
?>
<?= $this->App->viewStartComment() ?>
<div class="right-side-container" id="jsRightSideContainer">
    <?php if ($vision): ?>

        <div class="col col-xxs-12 goals-column-head-vision">
        <span class="font_18px mt_5px font_gargoyleGray goals-column-title right-column-vision-title">
            <?= __('Vision') ?> <span class="font_14px"><i class="fa fa-caret-right mr_2px"></i><i
                    class="fa fa-caret-right mr_2px"></i><i
                    class="fa fa-caret-right mr_4px"></i></span> <?= h($vision['target_name']) ?>
        </span>
        </div>
        <div
            class="col col-xxs-12 mt_5px mb_12px font_gargoyleGray right-column-vision-objective goals-column-teamvision-panel-base">
            <?=
            $this->Html->image('ajax-loader.gif',
                [
                    'class'         => 'lazy feed-img-teamvision pull-left',
                    'data-original' => $this->Upload->uploadUrl([$vision['model'] => $vision],
                        "{$vision['model']}.photo",
                        ['style' => 'medium'])
                ]
            )
            ?>
            <span class="goals-column-teamvision-name"><?= h($vision['name']) ?></span>
            <?php
            $method = $vision['model'] === 'TeamVision' ? 'vision_detail' : 'group_vision_detail';
            ?>

            <a class="vision-more-read"
               href="<?= $this->Html->url([
                   'controller' => 'teams',
                   'action'     => 'main',
                   '#'          => "{$method}/{$vision['id']}/true"
               ]) ?>">
                <i class="fa fa-arrow-circle-right mr_5px"></i><?= __('View detail') ?>
            </a>
        </div>

    <?php endif; ?>
    <div class="col col-xxs-12 goals-column-head">
        <span class="font_18px mt_5px font_gargoyleGray goals-column-title">
            <?= __('Your goals') ?>(<?= $my_goals_count + $collabo_goals_count ?>)
        </span>
        <a href="<?= $this->Html->url(['controller' => 'goals', 'action' => 'create', 'step1']) ?>"
           class="font_gargoyleGray-brownRed pull-right col-xxs-5 btn-goals-column-plus">
            <i class="fa fa-plus-circle font_brownRed">
            </i>
            <?= __('Create a goal') ?>
        </a>
    </div>
    <div id="LeaderGoals">
        <div class="col col-xxs-12 mt_16px font_gargoyleGray">
            <i class="fa fa-sun-o"></i><?= __('Leader') ?>(<?= $my_goals_count ?>)
        </div>

        <?php if (empty($my_goals)): ?>
            <div class="col col-xxs-12 goals-column-empty-box">
                <a href="<?= $this->Html->url(['controller' => 'goals', 'action' => 'create', 'step1']) ?>"
                   class="font_lightGray-gray">
                    <div class="goals-column-empty-icon"><i class="fa fa-plus-circle font_33px"></i></div>
                    <div class="goals-column-empty-text font_14px"><?= __('Create a new goal') ?></div>
                </a>
            </div>
        <?php else: ?>
            <?= $this->element('Goal/my_goal_area_items', ['goals' => $my_goals, 'type' => 'leader']) ?>
        <?php endif ?>
    </div>
    <?php if (count($my_goals) < $my_goals_count): ?>
        <a href="#" class="click-my-goals-read-more btn-link" next-page-num="2"
           get-url="<?= $this->Html->url(['controller' => 'goals', 'action' => 'ajax_get_my_goals']) ?>"
           goal-type="leader">
            <i class="fa fa-angle-double-down"><?= __("More") ?></i>
        </a>
    <?php endif; ?>
    <div id="CollaboGoals">
        <div class="col col-xxs-12 mt_16px font_gargoyleGray">
            <i class="fa fa-child"></i><?= __('Collaborator') ?>(<?= $collabo_goals_count ?>)
        </div>
        <?= $this->element('Goal/my_goal_area_items', ['goals' => $collabo_goals, 'type' => 'collabo']) ?>
    </div>
    <?php if (count($collabo_goals) < $collabo_goals_count): ?>
        <a href="#" class="click-collabo-goals-read-more btn-link" next-page-num="2"
           get-url="<?= $this->Html->url(['controller' => 'goals', 'action' => 'ajax_get_my_goals']) ?>"
           goal-type="collabo">
            <i class="fa fa-angle-double-down"><?= __("More") ?></i>
        </a>
    <?php endif; ?>
    <div id="PrevGoals">
        <div class="col col-xxs-12 goals-column-head mt_32px">
            <span class="font_18px font_gargoyleGray goals-column-title">
                <?= __('Unevaluated in the previous term') ?>
                (<?= $my_previous_goals_count ?>)
            </span>

        </div>
        <?= $this->element('Goal/my_goal_area_items', ['goals' => $my_previous_goals, 'type' => 'my_prev']) ?>
    </div>
    <?php if (count($my_previous_goals) < $my_previous_goals_count): ?>
        <a href="#" class="click-collabo-goals-read-more btn-link" next-page-num="2"
           get-url="<?= $this->Html->url(['controller' => 'goals', 'action' => 'ajax_get_my_goals']) ?>"
           goal-type="my_prev">
            <i class="fa fa-angle-double-down"><?= __("More") ?></i>
        </a>
    <?php endif; ?>
</div>
<?= $this->App->viewEndComment() ?>
