<button id="header-slide-menu" type="button"
        class="<?= $is_mb_app ? "mb-app-header-toggle-icon" : "header-toggle-icon" ?>"
        onclick="window.history.back()">
    <div class="hamburger-unread-point js-unread-point-on-hamburger is-read"></div>
    <i class="fa fa-angle-left mod-larger toggle-icon header-icons <?= $is_mb_app ? "mb-app-nav-icon" : null ?>"></i>
</button>