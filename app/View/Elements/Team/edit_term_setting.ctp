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
    <header>
        <h2><?= __("Term settings") ?></h2>
    </header>
    <?php // TODO: システム全体でtimezone, dateデータの持ち方に問題があるため、データの不整合が起きる前に一旦期間設定の変更をできなくしている。 ?>
    <?php //       本来ここには期間設定のformがあるので、上記対応時にrevertする。 ?>
    <div class="panel-body">
        <p><?= __("Changes will take effect after this current term") ?></p>
        <fieldset>
            <label><?= ("Next Term Start") ?>:</label>
            <select name="term_start" id="term_start">
                <option value="2017_07">July 2017</option>
                <option value="2017_08" selected="selected">August 2017 (default)</option>
                <option value="2017_09">September 2017</option>
                <option value="2017_10">October 2017</option>
                <option value="2017_11">November 2017</option>
                <option value="2017_12">December 2017</option>
                <option value="2018_01">January 2018</option>
                <option value="2018_02">February 2018</option>
                <option value="2018_03">March 2018</option>
                <option value="2018_04">April 2018</option>
                <option value="2018_05">May 2018</option>
                <option value="2018_06">June 2018</option>
            </select>
        </fieldset>
        <fieldset>
            <label><?= __("Term Length") ?>:</label>
            <select name="term_length" id="term_length">
                <option value="3">3 months (default)</option>
                <option value="6">6 months</option>
                <option value="12">12 months</option>
            </select>
        </fieldset>
        <div class="term-details current-term">
            <p>
                <strong><?= __("Current") ?></strong>
                <div class="term-range"><?= __("This term") ?>: <span id="currentStart" class="this-start" data-date="2017_5">May 2017</span> - <span class="this-end" data-date="2017_7">Jul 2017</span></div>
                <div class="term-range"><?= ("Next term") ?>: <span class="next-start" data-date="2017_8">Aug 2017</span> - <span class="next-end" data-date="2017_10">Oct 2017</span></div>
            </p>
        </div>
        <i class="fa fa-caret-down"></i>
        <div class="term-details edited-term">
            <p>
                <strong><?= __("After") ?></strong>
                <div class="term-range"><?= __("This term") ?>: <span class="this-start" data-date="2017_5">May 2017</span> - <span class="this-end" data-date="2017_7">Jul 2017</span></div>
                <div class="term-range"><?= ("Next term") ?>: <span class="next-start" data-date="2017_8">Aug 2017</span> - <span class="next-end" data-date="2017_10">Oct 2017</span></div>
            </p>
        </div>
    </div>
    <footer>
        <div class="term-attention">
            <strong>< <?= __("Attention") ?> ></strong>
            <ul>
                <li><?= __("The term has changed as above.") ?></li>
                <li><?= __("According to the changed term, the dates of the goals and KR are automatically updated as follows.") ?></li>
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
                <input type="checkbox" id="term_agreement" name="term_agreement"> <?= __("I confirm these changes.") ?>
            </fieldset>
        </div>
        <a href="#" class="btn btn-primary"><?= __("Save settings") ?></a>
    </footer>
</section>
<?php $this->end() ?>
<?= $this->App->viewEndComment() ?>
