<?= $this->App->viewStartComment()?>
<form method="post" action="" id="setEvaluators">
    <div class="panel panel-default col-sm-8 col-sm-offset-2 clearfix eval-list">
        <div class="panel-heading">
            <?= __('Set evaluators') ?>
        </div>
        <div class="panel-body eval-view-panel-body">
            <div class="form-group">
                <div for="#" class="col col-xxs-12 eval-index-panel-title bg-lightGray p_8px mb_8px">
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
                <hr class="col-xxs-12">
                <div for="#" class="col col-xxs-12 eval-index-panel-title bg-lightGray p_8px mb_8px">
                    <p class="font_bold"><?= __('Evaluators') ?></p>
                </div>
                <p>
                    TODO: Last update: *********
                </p>
                <div id="evaluators" class="list-group">
                    <?php foreach ($userEvaluators as $evaluatorKeyNumber => $userEvaluator): ?>
                    <div class="eval-list-item col-xxs-12 list-group-item">
                        <?php
                        $evaluatorsImageElementId = sprintf('evaluator_image_%d', $evaluatorKeyNumber);
                        $evaluatorsInputElementName = sprintf('evaluators[]');
                        ?>
                        <div class="evaluator_sort eval-list-item-left font_bold vertical-center horizontal-center">
                            <i class="fa fa-align-justify"></i>
                        </div>
                        <div class="evaluator_key_number eval-list-item-left font_bold vertical-center horizontal-center">
                        </div>
                        <div class="eval-list-item-left">
                            <?=
                            $this->Upload->uploadImage($userEvaluator, 'User.photo', ['style' => 'medium'],
                                ['width'  => '48px',
                                 'height' => '48px',
                                 'alt'    => 'icon',
                                 'class'  => 'pull-left img-circle mtb_3px',
                                 'id'     => $evaluatorsImageElementId,
                                ]) ?>
                        </div>
                        <div class="eval-list-item-center vertical-center">
                            <input type="hidden" class="form-input evaluator_select"
                                   name="<?= $evaluatorsInputElementName ?>"
                                   data-default-id="<?= $userEvaluator['User']['id'] ?>"
                                   data-default-text="<?= $userEvaluator['User']['display_username'] ?>"
                                   data-default-image="<?= $this->Upload->uploadUrl($userEvaluator, 'User.photo', ['style' => 'medium']) ?>"
                            />
                            &nbsp;
                            <button class="btn_remove btn btn-primary "><i class="fa fa-times"></i></button>
                        </div>
                    </div>
                    <?php endforeach ?>
                </div>
                <div class="pull-right">
                    <button id="button_add_evaluator" class="btn btn-primary"><i class="fa fa-plus fa-1x"></i> <?= __('Add') ?> <span class="can_add_left"></span></button>
                </div>
            </div>
        </div>
        <div class="panel-footer addteam_pannel-footer">
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
</form>
<div id="template_evaluator" class="hide">
    <div class="eval-list-item col-xxs-12 list-group-item">
        <div class="evaluator_sort eval-list-item-left font_bold vertical-center horizontal-center">
            <i class="fa fa-align-justify"></i>
        </div>
        <div class="evaluator_key_number eval-list-item-left font_bold vertical-center horizontal-center">
        </div>
        <div class="eval-list-item-left">
            <img src="/img/no-image-user.jpg" width="48px" height="48px" alt="icon" class="pull-left img-circle mtb_3px">
        </div>
        <div class="eval-list-item-center vertical-center">
            <input type="hidden" class="form-input evaluator_select" name="evaluators[]" />
            &nbsp;
            <button class="btn_remove btn btn-primary "><i class="fa fa-times"></i></button>
        </div>
    </div>
</div>
<script type="text/javascript" src="/js/goalous_evaluator_setting.min.js"></script>
<?= $this->App->viewEndComment()?>
