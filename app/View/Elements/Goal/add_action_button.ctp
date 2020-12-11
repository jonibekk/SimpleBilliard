<?php

/**
 * Created by IntelliJ IDEA.
 * User: bigplants
 * Date: 2017/05/25
 * Time: 3:13
 */
$url = '/goals/add-action';
if (!empty($goal_id)) {
    $url .= '/goal/' . $goal_id;
    if (!empty($key_result_id)) {
        $url .= '/key-result/' . $key_result_id;
    }
}

?>
<?= $this->App->viewStartComment() ?>
<a class="add-action-link" href="<?= $url ?>">
    <div class="profile-user-action-contents-add-image">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16">
            <path d="M0 7.2h7.2V0h1.5v7.2H16v1.5H8.8V16H7.2V8.8H0V7.2z" /></svg>
    </div>
    <span class="add-action-caption"><?= __('Add Action') ?></span>
</a>
<?= $this->App->viewEndComment() ?>