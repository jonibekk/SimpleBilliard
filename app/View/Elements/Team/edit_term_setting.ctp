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
    <div class="panel-body">
        <p>Changes will take effect after this current term</p>
        <fieldgroup>
            <label>Term Start:</label>
            <select name="term_start" id="term_start">
                <option value="05_2017">May 2017 (default)</option>
                <option value="06_2017">June 2017</option>
                <option value="07_2017">July 2017</option>
                <option value="08_2017">August 2017</option>
                <option value="09_2017">September 2017</option>
                <option value="10_2017">October 2017</option>
                <option value="11_2017">November 2017</option>
                <option value="12_2017">December 2017</option>
                <option value="01_2017">January 2018</option>
                <option value="02_2017">February 2018</option>
                <option value="03_2017">March 2018</option>
                <option value="04_2017">April 2018</option>
            </select>
        </fieldgroup>
        <fieldgroup>
            <label>Term Length:</label>
            <select name="term_length" id="term_length">
                <option value="3">3 months (default)</option>
                <option value="6">6 months</option>
                <option value="12">12 months</option>
            </select>
        </fieldgroup>
        <div class="term-details current-term">
            <p>
                <strong>Current</strong>
                <div class="term-range">This term: <span class="this-start">Apr 2017</span> - <span class="this-end">Sep 2017</span></div>
                <div class="term-range">Next term: <span class="next-start">Oct 2017</span> - <span class="next-end">Mar 2018</span></div>
            </p>
        </div>
        <i class="fa fa-caret-down"></i>
        <div class="term-details edited-term">
            <p>
                <strong>After</strong>
                <div class="term-range">This term: <span class="this-start">Apr 2017</span> - <span class="this-end">Sep 2017</span></div>
                <div class="term-range">Next term: <span class="next-start">Oct 2017</span> - <span class="next-end">Mar 2018</span></div>
            </p>
        </div>
    </div>
    <footer>
        <strong>< Attention ></strong>
        <ul>
            <li>上記のように期間が変更されます</li>
            <li>すでに作成されたゴールやKRの開始日と終了日が変更されます。</li>
        </ul>
        <fieldgroup>
            <input type="checkbox" id="term_agreement" name="term_agreement"> I confirm these changes. 
        </fieldgroup>
        <a href="#" class="btn btn-primary">Save settings</a>
    </footer>
</section>  
<?php $this->end() ?>
<?= $this->App->viewEndComment()?>
