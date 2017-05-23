<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 7/9/15
 * Time: 3:33 PM
 *
 * @var $goal
 * @var $goalTerm
 * @var $goalLabels
 */
?>
<?= $this->App->viewStartComment() ?>
<div class="col-sm-8 col-sm-offset-2">
    <div class="panel panel-default">
        <?= $this->element('Goal/simplex_top_section', compact('goal', 'goalTerm', 'goalLabels')) ?>
    </div>
</div>
<?= $this->App->viewEndComment() ?>