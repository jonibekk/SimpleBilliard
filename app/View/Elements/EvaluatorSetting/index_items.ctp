<?= $this->App->viewStartComment()?>
<?php foreach ($evaluatees as $user): ?>
    <?php if (!$isFixedEvaluationOrder): ?>
    <a href="<?= $this->Html->url(['controller'       => 'evaluator_settings',
                                   'action'           => 'detail',
                                   'user_id'          => $user['User']['id'],
    ]) ?>"
       class="font_verydark">
        <div class="eval-list-item col-xxs-12">
    <?php else: ?>
    <div
       class="font_verydark">
        <div class="col-xxs-12">
    <?php endif ?>
            <div class="eval-list-item-left">
                <?=
                $this->Upload->uploadImage($user, 'User.photo', ['style' => 'medium'],
                    ['width'  => '48px',
                     'height' => '48px',
                     'alt'    => 'icon',
                     'class'  => 'pull-left img-circle mtb_3px'
                    ]) ?>
            </div>
            <div class="eval-list-item-center">
                <p class="font_bold"><?= h($user['User']['display_username']) ?></p>
                <?php if (0 === count($user['evaluators'])): ?>
                    <?= __('No evaluator') ?>
                <?php else: ?>
                    <?php foreach ($user['evaluators'] as $key => $evaluator): ?>
                    <?php if ($key !== 0): ?>
                    ・
                    <?php endif ?>
                    <?=
                    $this->Upload->uploadImage($evaluator['User'], 'User.photo', ['style' => 'medium'],
                        ['width'  => '16px',
                         'height' => '16px',
                         'alt'    => 'icon',
                         'class'  => 'img-circle mtb_3px'
                        ]) ?>
                    <?= ($key + 1) ?>
                    (<?= $evaluator['User']['display_username'] ?>)
                    <?php endforeach ?>
                <?php endif ?>
            </div>
            <?php if (!$isFixedEvaluationOrder): ?>
            <div class="eval-list-item-right">
                <i class="fa fa-angle-right font_lightgray" aria-hidden="true"></i>
            </div>
            <?php endif ?>
        </div>
    <?php if (!$isFixedEvaluationOrder): ?>
    </a>
    <?php else: ?>
    </div>
    <?php endif ?>
    <hr class="col-xxs-12">
<?php endforeach; ?>
<?= $this->App->viewEndComment()?>
