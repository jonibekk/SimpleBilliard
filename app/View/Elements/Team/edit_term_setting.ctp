<?php /**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 6/11/14
 * Time: 11:40 AM
 *
 * @var CodeCompletionView $this
 * @var                    $border_months_options
 * @var                    $start_term_month_options
 * @var                    $current_eval_is_started
 * @var                    $previous_term_start_date
 * @var                    $previous_term_end_date
 * @var                    $previous_term_timezone
 * @var                    $current_term_start_date
 * @var                    $current_term_end_date
 * @var                    $current_term_timezone
 * @var                    $next_term_start_date
 * @var                    $next_term_end_date
 * @var                    $next_term_timezone
 * @var                    $timezones
 */
?>
<?= $this->App->viewStartComment()?>
<section class="panel panel-default" id="editTerm">
    <?=
    $this->Form->create('Team', [
        'novalidate' => true,
        'url'        => ['action' => 'edit_term'],
        'method'     => 'post'
    ]); ?>
    <header>
        <h2><?= __("Term settings") ?></h2>
    </header>
    <div class="panel-body">
        <p><?= __("Changes will take effect after this current term") ?></p>
        <fieldset>
            <label><?= __("Next term start") ?>:</label>
            <?php
            $nextSelectableStartYm[$nextTermStartYm] .= '*';
            echo $this->Form->input('next_start_ym', [
                'label'    => false,
                'type'     => 'select',
                'options'  => $nextSelectableStartYm,
                'selected' => $nextTermStartYm,
                'id'       => 'term_start'
            ]) ?>
        </fieldset>
        <fieldset>
            <label><?= __("Term length") ?>:</label>
            <?php
            $rangeOptions = [
                '3' => __('3 months'),
                '6' => __('6 months'),
                '12' => __('12 months'),
            ];
            $rangeOptions[$termLength] .= '*';
            echo $this->Form->input('term_length', [
                'label'    => false,
                'type'     => 'select',
                'options'  => $rangeOptions,
                'selected' => $termLength,
                'id'       => 'term_length'
            ]) ?>
        </fieldset>
        <div class="term-details current-term">
            <p>
                <strong><?= __("Current") ?></strong>
                <div class="term-range">
                    <?= __("This term") ?>: <span id="currentStart" class="this-start" data-date="<?= $currentTermStartYm ?>"><?= AppUtil::formatYmI18n(strtotime($current_term_start_date)) ?></span> - <span class="this-end" data-date="<?= $currentTermEndYm ?>"><?= AppUtil::formatYmI18n(strtotime($current_term_end_date)) ?></span>
                </div>
                <div class="term-range">
                    <?= __("Next term") ?>: <span class="next-start" data-date="<?= $nextTermStartYm ?>"><?= AppUtil::formatYmI18n(strtotime($next_term_start_date)) ?></span> - <span class="next-end" data-date="<?= $nextTermEndYm ?>"><?= AppUtil::formatYmI18n(strtotime($next_term_end_date)) ?></span>
                </div>
            </p>
        </div>
        <i class="fa fa-caret-down current-next-arrow mod-hide"></i>
        <div class="term-details edited-term mod-hide">
            <p>
                <strong><?= __("After") ?></strong>
                <div class="term-range"><?= __("This term") ?>: <span class="this-start" data-date="<?= $currentTermStartYm ?>"><?= AppUtil::formatYmI18n(strtotime($current_term_start_date)) ?></span> - <span class="this-end" data-date=""></span></div>
                <div class="term-range"><?= __("Next term") ?>: <span class="next-start" data-date=""></span> - <span class="next-end" data-date=""></span></div>
            </p>
        </div>
    </div>
    <footer>
        <div class="term-attention mod-hide">
            <strong>< <?= __("Attention") ?> ></strong>
            <ul>
                <li><?= __("The term has changed as above.") ?></li>
            </ul>
            <ol>
                <li><?= __("The goal that beings in the current term and ends in the next term will be updated to end on the last day of the current term.") ?></li>
                <li><?= __("The goal that begins in the current term and ends beyond the next term will be updated to end on the last day of the current term.") ?></li>
                <li><?= __("The goal that beings in the next term and ends beyond the next term will be updated to end on the last day of the next term.") ?></li>
                <li><?= __("If the start date and end date of a goal is both within the current term, or both within the next term, will not be changed.") ?></li>
                <li><?= __("The goal that begins and ends beyond the the next term will be updated to match the start and end date of the next term.") ?></li>
            </ol>
            <ul>
                <li><?= __("The start date and the end date of KR belonging to the goal are updated like the above goal.") ?></li>
            </ul>
            <fieldset>
                <input type="checkbox" id="term_agreement" name="term_agreement"> <div><?= __("I confirm these changes.") ?></div>
            </fieldset>
        </div>
        <?=
            $this->Form->submit(__("Save settings"),
            ['class' => 'btn btn-primary', 'div' => false, 'disabled' => 'disabled'])
        ?>
    </footer>
    <?php $this->Form->unlockField('Team.next_start_ym') ?>
    <?php $this->Form->unlockField('Team.term_length') ?>
    <?php $this->Form->unlockField('term_agreement') ?>
    <?= $this->Form->end(); ?>
</section>
<?= $this->App->viewEndComment() ?>
