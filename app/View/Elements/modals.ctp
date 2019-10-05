<?php
/**
 * @var CodeCompletionView $this
 */
?>
<?= $this->App->viewStartComment()?>
<?php /**
 * Created by PhpStorm.
 * User: daikihirakata
 * Date: 2014/07/29
 * Time: 23:58
 *
 * @var View $this
 */
echo $this->element('modal_tutorial');
echo "<div id='layerBlack' onclick='toggleNav()'></div>";
echo $this->App->viewEndComment();

