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
            <?= __('Evaluator edit') ?>
        </div>
        <div class="panel-body bg-lightGray">
            <div class="col col-xxs-12 eval-view-panel-body font_verydark p_4px">
                <?php
                $hasLastUpdate = !empty($latestEvaluatorChangeLog);
                ?>
                <p>
                    <span><?= __('Last updated') ?>: </span>
                    <?php if ($hasLastUpdate): ?>
                        <?= $latestEvaluatorChangeLog['display_update_time'] ?>
                    <?php else: ?>
                        -
                    <?php endif; ?>
                </p>
                <?php if ($hasLastUpdate): ?>
                <p>
                    <?= $latestEvaluatorChangeLog["User"]["display_username"] ?>
                </p>
                <?php endif; ?>
            </div>
        </div>
        <div class="panel-body eval-view-panel-body">
            <div class="form-group">
                <div for="#" class="col col-xxs-12 eval-index-panel-title font_verydark p_4px font_18px">
                    <p class="font_bold"><?= __('Evaluatee') ?></p>
                </div>
                <div class="eval-list-item2 col-xxs-12 mb_8px">
                    <div class="eval-list-item-left2">
                        <?=
                        $this->Upload->uploadImage($userEvaluatee, 'User.photo', ['style' => 'medium'],
                            ['width'  => '48px',
                             'height' => '48px',
                             'alt'    => 'icon',
                             'class'  => 'pull-left img-circle mtb_3px'
                            ]) ?>
                    </div>
                    <div class="eval-list-item-center2">
                        <p class="font_bold"><?= h($userEvaluatee['User']['display_username']) ?></p>
                        <span class="font_bold">
                            <?php if (!is_null($userEvaluateeCoach)): ?>
                                <?= __('Coach') ?>: <?=
                                $this->Upload->uploadImage($userEvaluateeCoach, 'User.photo', ['style' => 'medium'],
                                    ['width'  => '24px',
                                     'height' => '24px',
                                     'alt'    => 'icon',
                                     'class'  => 'img-circle mtb_3px'
                                    ]) ?> <?= h($userEvaluateeCoach['User']['display_username']) ?>
                            <?php else: ?>
                                <?= __('Coach') ?>: <i class="fa fa-user" aria-hidden="true"></i> -
                            <?php endif ?>
                        </span>
                    </div>
                </div>
                <div for="#" class="col col-xxs-12 eval-index-panel-title font_verydark p_4px">
                    <p class="font_bold font_18px"><?= __('Evaluators') ?></p>
                    <p class="font_gray"><?= __('Insert team member.') ?></p>
                </div>
            </div>
        </div>
        <ul id="evaluators" class="list-group">
            <?php foreach ($userEvaluators as $evaluatorKeyNumber => $userEvaluator): ?>
                <li class="list-group-item row eval-list-item p_5px plr_11px">
                <?php
                $evaluatorsInputElementName = sprintf('evaluator_user_ids[]');
                ?>
                <div class="col-xxs-1 eval-list-item-parts evaluator_sort pointer vertical-center horizontal-center">
                    <i class="fa fa-align-justify"></i>
                </div>
                <div class="col-xxs-1 eval-list-item-parts evaluator_key_number vertical-center horizontal-center">
                </div>
                <div class="col-xxs-9 pr_12px vertical-center horizontal-center">
                    <input type="hidden" class="form-input evaluator_select"
                           name="<?= $evaluatorsInputElementName ?>"
                           data-default-id="<?= $userEvaluator['User']['id'] ?>"
                           data-default-text="<?= sprintf("%s (%s)", $userEvaluator['User']['display_username'], $userEvaluator['User']['roman_username']) ?>"
                           data-default-image="<?= $this->Upload->uploadUrl($userEvaluator, 'User.photo', ['style' => 'medium']) ?>"
                    />
                </div>
                <div class="col-xxs-1 eval-list-item-parts vertical-center text-align_c">
                    <a href="#" class="font_lightGray-gray">
                        <i class="btn_remove fa fa-times-circle fa-2x"></i>
                    </a>
                </div>
            </li>
            <?php endforeach ?>
        </ul>
        <div class="panel-body row">
            <div class="col-xxs-2"></div>
            <div class="col-xxs-10 vertical-center">
                <p id="button_add_evaluator" class="font_bold color-blue pointer">+ <?= __('Add evaluator') ?><span id="remaining_to_add" class="p_5px font_normal font_lightgray"></span></p>
            </div>
        </div>
        <div class="panel-body addteam_pannel-footer">
            <div class="row">
                <div class="team-button pull-right">
                    <a class="btn btn-link design-cancel bd-radius_4px" data-dismiss="modal" href="<?= $this->Html->url(['controller'       => 'evaluator_settings', 'action'           => 'index',
                    ]) ?>">
                        <?= __('Cancel') ?>
                    </a>
                    <input class="btn btn-primary" type="submit" value="<?= __('Save') ?>">
                </div>
            </div>
        </div>
    </div>
<?= $this->Form->end(); ?>
<div id="template_evaluator" class="hide">
    <li class="list-group-item row eval-list-item p_5px plr_11px">
        <div class="col-xxs-1 eval-list-item-parts evaluator_sort pointer vertical-center horizontal-center">
            <i class="fa fa-align-justify"></i>
        </div>
        <div class="col-xxs-1 eval-list-item-parts evaluator_key_number vertical-center horizontal-center">
        </div>
        <div class="col-xxs-9 pr_12px vertical-center horizontal-center">
            <input type="hidden" class="form-input evaluator_select" name="evaluator_user_ids[]" />
        </div>
        <div class="col-xxs-1 eval-list-item-parts vertical-center text-align_c">
            <a href="#" class="font_lightGray-gray">
                <i class="btn_remove fa fa-times-circle fa-2x"></i>
            </a>
        </div>
    </li>
</div>
<?= $this->App->viewEndComment()?>
