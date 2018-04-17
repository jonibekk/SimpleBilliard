<button id="toggleNavigationButton" type="button" action="" onclick="toggleNav()"
        class="<?= $is_mb_app ? "mb-app" : "" ?> mb-to-front-almost header-toggle-icon js-nav-toggle">
    <div class="hamburger-unread-point js-unread-point-on-hamburger is-read"></div>
    <i class="fa fa-navicon toggle-icon header-icons <?= $is_mb_app ? "mb-app-nav-icon" : null ?>"></i>
</button>
<div id="NavbarOffcanvas">
    <ul id="navigationWrapper" class="nav navbar-nav <?= $is_mb_app ? "mb-to-back-most" : "" ?>">
        <li class="<?= $is_mb_app ? "mtb_15px" : "mtb_5px" ?> mtb-sm_0">
            <a class="header-logo header_l-icons hoverPic <?= $current_global_menu == "home" ? "activeColumn" : null ?>"
               href="<?= $this->Html->url('/') ?>"><!--suppress HtmlUnknownTarget -->
                <div class="ta-sm_c">
                    <img src="<?= $this->Html->url('/img/logo_off.png') ?>" class="header-logo-img"
                         alt="Goalous2.0" width="20px" height="20px">

                    <p class="font_11px font_heavyGray header_icon-text hidden-xs js-header-link">
                        <?= __("Home") ?>
                    </p>
                    <span class="visible-xs-inline va_bl ml_5px"><?= __("Home") ?></span>
                </div>
            </a>
        </li>
        <li class="<?= $is_mb_app ? "mtb_15px" : "mtb_5px" ?> mtb-sm_0">
            <a class="header-goal header_l-icons <?= $current_global_menu == "goal" ? "activeColumn" : null ?>"
               href="<?= $this->Html->url(['controller' => 'goals', 'action' => 'index']) ?>">
                <div class="ta-sm_c">
                    <i class="fa fa-flag js-header-link header-icon nav-xxs-icon header-icons"></i>

                    <p class="font_11px header_icon-text hidden-xs js-header-link">
                        <?= __("Goal") ?>
                    </p>
                    <span class="visible-xs-inline ml_5px"><?= __("Goal") ?></span>
                </div>
            </a>
        </li>
        <li class="<?= $is_mb_app ? "mtb_15px" : "mtb_5px" ?> mtb-sm_0 <?= !empty($my_teams) ? null : 'hidden' ?>">
            <a class="header-team header_l-icons <?= $current_global_menu == "team" ? "activeColumn" : null ?>"
               href="<?= $this->Html->url(['controller' => 'teams', 'action' => 'index']) ?>">
                <div class="ta-sm_c">
                    <i class="fa fa-users js-header-link header-icon nav-xxs-icon header-icons"></i>
                    <p class="font_11px header_icon-text hidden-xs js-header-link">
                        <?= __("Team") ?>
                    </p>
                    <span class="visible-xs-inline ml_5px"><?= __("Team") ?></span>
                </div>
            </a>
        </li>
        <li>
            <form class="nav-form-group header-team-select-form" role="search">
                <?php echo $this->Form->input('current_team',
                    array(
                        'type'      => 'select',
                        'options'   => !empty($my_teams) ? $my_teams : [
                            __(
                                'There is no team.')
                        ],
                        'value'     => $this->Session->read('current_team_id'),
                        'id'        => 'SwitchTeam',
                        'label'     => false,
                        'div'       => false,
                        'class'     => 'form-control nav-team-select font_12px disable-change-warning',
                        'wrapInput' => false,
                    ))
                ?>
            </form>
        </li>
        <li class="visible-xxs hidden-xs">
            <?= $this->element('dashboard_saved_item') ?>
        </li>
        <li class="circle-list-in-hamburger visible-xxs hidden-xs">
            <?= $this->element('circle_list_in_hamburger') ?>
        </li>
    </ul>
</div>
