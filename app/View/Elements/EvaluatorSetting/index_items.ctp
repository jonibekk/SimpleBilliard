<?php /**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 3/26/15
 * Time: 3:45 PM
 *
 * @var CodeCompletionView $this
 * @var                    $evaluatees
 * @var                    $eval_term_id
 * @var                    $eval_is_frozen
 */
?>
<?= $this->App->viewStartComment()?>
<?php foreach ($evaluatees as $user): ?>
    <a href="<?= $this->Html->url(['controller'       => 'evaluator_settings',
                                   'action'           => 'detail',
                                   'user_id'          => $user['User']['id'],
    ]) ?>"
       class="font_verydark">
        <div class="eval-list-item col-xxs-12">
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
                <?php if (0 === count($user['flow'])): ?>
                    No evaluators
                <?php else: ?>
                    <?php foreach ($user['flow'] as $key => $evaluateFlow): ?>
                        <?php if ($key !== 0): ?>

                            <?php if (!$isFixedEvaluationOrder && $key > 0):?>
                                ・
                            <?php else:?>
                                &nbsp;<i class="fa fa-long-arrow-right font_lightgray"></i>&nbsp;
                            <?php endif;?>

                        <?php endif ?>
                        <span class="font_bold">
                        <i class="fa fa-user" aria-hidden="true"></i><?= $evaluateFlow['name'] ?>
                    </span>
                    <?php endforeach ?>
                <?php endif ?>
            </div>
            <div class="eval-list-item-right">
                <i class="fa fa-angle-right font_lightgray" aria-hidden="true"></i>
            </div>
        </div>
    </a>
    <hr class="col-xxs-12">
<?php endforeach; ?>
<?= $this->App->viewEndComment()?>
