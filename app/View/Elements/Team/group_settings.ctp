<?php
?>
<?= $this->App->viewStartComment() ?>
<section class="panel panel-default">
    <header>
        <h2><?= __("Group Settings") ?></h2>
    </header>
    <div class="panel-body">
        <?=
            $this->Html->link(
                __("Manage groups"),
                "/settings/groups",
                ['class' => 'btn btn-primary', 'div' => false]
            ) ?>
    </div>
</section>
<?= $this->App->viewEndComment() ?>
