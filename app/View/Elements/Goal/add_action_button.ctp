<?php
/**
 * Created by IntelliJ IDEA.
 * User: bigplants
 * Date: 2017/05/25
 * Time: 3:13
 */
$goal_id = $goal_id??null;
$key_result_id = $key_result_id??null;
?>
<?= $this->App->viewStartComment() ?>
<div class="profile-user-action-contents-add-image">
    <span><a href="/goals/add_action/goal_id:<?= $goal_id ?>/key_result_id:<?= $key_result_id ?>">+</a></span>
</div>
<a href="/goals/add_action/goal_id:<?= $goal_id ?>/key_result_id:<?= $key_result_id ?>">
    <?= __('Add Action') ?></a>
<?= $this->App->viewEndComment() ?>

