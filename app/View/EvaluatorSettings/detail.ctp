<?= $this->App->viewStartComment()?>
    <?=
    $this->Form->create([
        'id'            => 'setEvaluators',
        'method'        => 'post',
        'url'           => '',
        'class'         => 'form',
    ]); ?>
    <input type="hidden" name="evaluatee_user_id" value="<?= $userEvaluatee['User']['id'] ?>">
    <div class="panel panel-default col-sm-8 col-sm-offset-2 clearfix eval-list">
        <div class="panel-heading">
            <?= __('Set evaluators') ?>
        </div>
        <div class="panel-body bg-lightGray">
            <div class="col col-xxs-12 eval-view-panel-body font_verydark p_4px">
                <?php if (empty($latestEvaluatorChangeLog)): ?>
                <p>
                    <span class="font_bold">Last update: </span>-
                </p>
                <?php else: ?>
                <p>
                    <span class="font_bold">Last update: </span><?= $latestEvaluatorChangeLog['display_update_time'] ?>
                </p>
                <p>
                    By <?= $latestEvaluatorChangeLog["User"]["display_username"] ?>
                </p>
                <?php endif; ?>
            </div>
        </div>
        <div class="panel-body eval-view-panel-body">
            <div class="form-group">
                <div for="#" class="col col-xxs-12 eval-index-panel-title font_verydark p_4px mb_8px font_18px">
                    <p class="font_bold"><?= __('Evaluatee') ?></p>
                </div>
                <div class="eval-list-item col-xxs-12">
                    <div class="eval-list-item-left">
                        <?=
                        $this->Upload->uploadImage($userEvaluatee, 'User.photo', ['style' => 'medium'],
                            ['width'  => '48px',
                             'height' => '48px',
                             'alt'    => 'icon',
                             'class'  => 'pull-left img-circle mtb_3px'
                            ]) ?>
                    </div>
                    <div class="eval-list-item-center">
                        <p class="font_bold"><?= h($userEvaluatee['User']['display_username']) ?></p>
                        <span class="font_bold">
                            <?php if (!is_null($userEvaluateeCoach)): ?>
                                Coach: <?=
                                $this->Upload->uploadImage($userEvaluateeCoach, 'User.photo', ['style' => 'medium'],
                                    ['width'  => '24px',
                                     'height' => '24px',
                                     'alt'    => 'icon',
                                     'class'  => 'img-circle mtb_3px'
                                    ]) ?> <?= h($userEvaluateeCoach['User']['display_username']) ?>
                            <?php else: ?>
                                Coach: <i class="fa fa-user" aria-hidden="true"></i> No Coach
                            <?php endif ?>
                        </span>
                    </div>
                </div>
                <div for="#" class="col col-xxs-12 eval-index-panel-title font_verydark p_4px mb_8px font_18px">
                    <p class="font_bold"><?= __('Evaluator(s)') ?></p>
                </div>
            </div>
        </div>
        <ul id="evaluators" class="list-group">
            <?php foreach ($userEvaluators as $evaluatorKeyNumber => $userEvaluator): ?>
            <li class="eval-list-item col-xxs-12 list-group-item">
                <?php
                $evaluatorsInputElementName = sprintf('evaluator_user_ids[]');
                ?>
                <div class="evaluator_sort eval-list-item-left font_bold vertical-center horizontal-center">
                    <i class="fa fa-align-justify"></i>
                </div>
                <div class="evaluator_key_number eval-list-item-left font_bold vertical-center horizontal-center">
                </div>
                <div class="eval-list-item-center vertical-center">
                    <input type="hidden" class="form-input evaluator_select"
                           name="<?= $evaluatorsInputElementName ?>"
                           data-default-id="<?= $userEvaluator['User']['id'] ?>"
                           data-default-text="<?= sprintf("%s (%s)", $userEvaluator['User']['display_username'], $userEvaluator['User']['roman_username']) ?>"
                           data-default-image="<?= $this->Upload->uploadUrl($userEvaluator, 'User.photo', ['style' => 'medium']) ?>"
                    />
                </div>
                <div class="eval-list-item-left vertical-center horizontal-center">
                    <a href="#" class="font_lightGray-gray">
                        <i class="btn_remove fa fa-times-circle fa-2x"></i>
                    </a>
                </div>
            </li>
            <?php endforeach ?>
        </ul>
        <div class="panel-body eval-list-item">
            <div class="eval-list-item-left"></div>
            <div class="eval-list-item-center vertical-center">
                <p id="button_add_evaluator" class="font_bold color-blue pointer">+ <?= __('Add another Evaluator') ?><span id="remaining_to_add" class="p_5px font_normal font_lightgray"></span></p>
            </div>
        </div>
        <div class="panel-body addteam_pannel-footer">
            <div class="row">
                <div class="team-button pull-right">
                    <a class="btn btn-link design-cancel bd-radius_4px" data-dismiss="modal" href="<?= $this->Html->url(['controller'       => 'evaluator_settings', 'action'           => 'index',
                    ]) ?>">
                        <?= __('Cancel') ?>
                    </a>
                    <input class="btn btn-primary" type="submit" value="Save">
                </div>
            </div>
        </div>
    </div>
<?= $this->Form->end(); ?>
<div id="template_evaluator" class="hide">
    <li class="eval-list-item col-xxs-12 list-group-item">
        <div class="evaluator_sort eval-list-item-left font_bold vertical-center horizontal-center">
            <i class="fa fa-align-justify"></i>
        </div>
        <div class="evaluator_key_number eval-list-item-left font_bold vertical-center horizontal-center">
        </div>
        <div class="eval-list-item-center vertical-center">
            <input type="hidden" class="form-input evaluator_select" name="evaluator_user_ids[]" />
        </div>
        <div class="eval-list-item-left vertical-center horizontal-center">
            <a href="#" class="font_lightGray-gray">
                <i class="btn_remove fa fa-times-circle fa-2x"></i>
            </a>
        </div>
    </li>
</div>
<?= $this->App->viewEndComment()?>
