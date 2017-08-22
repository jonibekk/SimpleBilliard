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
    <a class="add-action-link" href="/goals/add_action/goal_id:<?= $goal_id ?>/key_result_id:<?= $key_result_id ?>">
        <div class="profile-user-action-contents-add-image">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16"><path d="M0 7.2h7.2V0h1.5v7.2H16v1.5H8.8V16H7.2V8.8H0V7.2z"/></svg>
        </div>
        <span class="add-action-caption"><?= __('Add Action') ?></span>
    </a>
<?= $this->App->viewEndComment() ?>

