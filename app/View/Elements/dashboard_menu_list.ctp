<?= $this->App->viewStartComment() ?>
<div class="dashboard-menu mod-border">
    <a href="/goals">
        <div class="dashboard-menu-icon"><i class="fa fa-flag"></i></div>
        <span><?= __("Goals") ?></span>
    </a>
</div>
<div class="dashboard-menu">
    <a href="/saved_items">
        <div class="dashboard-menu-icon"><i class="fa fa-bookmark"></i></div>
        <span><?= __("Saved") ?></span>
    </a>
</div>
<?= $this->App->viewEndComment() ?>
