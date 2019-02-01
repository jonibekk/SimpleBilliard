<?= $this->App->viewStartComment() ?>
<div class="glHeaderPc">
    <div class="glHeaderPc-left">
        <a href="/">
            <img src="/img/svg/logomark_primary01.svg" alt="Goalous" width="32" height="32"/>
        </a>
    </div>
    <div class="glHeaderPc-right">
        <?= $this->element('Header/logged_in_right'); ?>
    </div>
</div>
<?= $this->App->viewEndComment() ?>
