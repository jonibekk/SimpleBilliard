<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 6/30/14
 * Time: 9:59 AM
 *
 * @var CodeCompletionView $this
 * @var array              $team
 * @var                    $from_setting
 */
?>
<?= $this->App->viewStartComment()?>
<section class="panel panel-default">
    <header>
        <h2><?= __("Invite members") ?></h2>
    </header>
    <div class="panel-body">
        <?=
        $this->Html->link(__("Invite members"), "/users/invite",
            ['class' => 'btn btn-primary', 'div' => false]) ?>
    </div>
</section>
<?= $this->App->viewEndComment()?>
