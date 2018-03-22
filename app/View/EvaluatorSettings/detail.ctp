<?= $this->App->viewStartComment()?>
<div class="panel panel-default col-sm-8 col-sm-offset-2 clearfix eval-list">
    <div class="panel-heading">
        Set evaluators
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
            <?php foreach ($userEvaluators as $key => $userEvaluator): ?>
            <div class="eval-list-item col-xxs-12">
                <div class="eval-list-item-left font_bold vertical-center horizontal-center">
                    <?= ($key+1) ?>
                </div>
                <?php if (!is_null($userEvaluator)): ?>
                <div class="eval-list-item-left">
                    <?=
                    $this->Upload->uploadImage($userEvaluator, 'User.photo', ['style' => 'medium'],
                        ['width'  => '48px',
                         'height' => '48px',
                         'alt'    => 'icon',
                         'class'  => 'pull-left img-circle mtb_3px'
                        ]) ?>
                </div>
                <?php else: ?>
                <div class="eval-list-item-left">
                    <?=
                    $this->Upload->uploadImage($userEvaluator, 'User.photo', ['style' => 'medium'],
                        ['width'  => '48px',
                         'height' => '48px',
                         'alt'    => 'icon',
                         'class'  => 'pull-left img-circle mtb_3px'
                        ]) ?>
                </div>
                <?php endif ?>
                <div class="eval-list-item-center vertical-center">
                    <input type="text" value="" placeholder="None">
                </div>
            </div>
            <?php endforeach ?>
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
<?= $this->App->viewEndComment()?>
