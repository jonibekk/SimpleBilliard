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
    <a href="<?= $this->Html->url(['controller'       => 'evaluations',
                                   'action'           => 'view',
                                   'evaluate_term_id' => $eval_term_id,
                                   'user_id'          => $user['User']['id'],
                                   'class'            => 'block'
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
                <?php foreach ($user['flow'] as $k => $v): ?>
                    <?php if ($k !== 0): ?>&nbsp;<i class="fa fa-long-arrow-right font_lightgray"></i>&nbsp;<?php endif ?>
                    <span class="<?= $v['this_turn'] ? 'font_bold' : 'font_lightgray' ?>">
                        <i class="fa fa-user <?= $v['other_evaluator'] ? '' : 'none' ?>" aria-hidden="true"></i><?= $v['name'] ?>
                    </span>
                <?php endforeach ?>
                <?php if ($user['status_text']['body'] && !$eval_is_frozen): ?>
                    <?php $text_class = h($user['status_text']['your_turn']) ? 'font_brownRed' : 'font_verydark' ?>
                    <p class="<?= $text_class ?>"><?= h($user['status_text']['body']) ?></p>
                <?php else: ?>
                    <p class="font_verydark"></p>
                <?php endif; ?>
            </div>
            <div class="eval-list-item-right">
                <i class="fa fa-angle-right font_lightgray" aria-hidden="true"></i>
            </div>
        </div>
    </a>
    <hr class="col-xxs-12">
<?php endforeach; ?>
<?= $this->App->viewEndComment()?>
