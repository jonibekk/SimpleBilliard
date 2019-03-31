<?= $this->App->viewStartComment() ?>
<?php if ($this->BackBtn->checkPage()):?>
    <a
       class="nav-back-btn"
       onclick="window.history.back();">
        <i class="material-icons">chevron_left</i>
    </a>
<?php endif;?>
<?= $this->App->viewEndComment() ?>
