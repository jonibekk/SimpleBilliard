<?= $this->App->viewStartComment() ?>
<div class="glHeaderPc">
    <div class="glHeaderPc-left">
        <a href="/">
            <img src="/img/svg/logomark_primary01.svg" alt="Goalous" width="32" height="32"/>
        </a>
        <form id="search-input" class="search-input">
            <label class="search-input-label">
                <i class="material-icons search-input-icon">search</i>
                <input id="search-input-input" class="search-input-input" type="text" placeholder="<?= __('Search')?>">
                <i id="search-input-clear" class="material-icons search-input-clear">clear</i>
            </label>
        </form>
    </div>
    <div class="glHeaderPc-right">
        <?= $this->element('Header/logged_in_right'); ?>
    </div>
</div>
<?= $this->App->viewEndComment() ?>
