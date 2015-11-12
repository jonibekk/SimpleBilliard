<?php
/**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 5/28/14
 * Time: 5:04 PM
 *
 * @var CodeCompletionView $this
 * @var                    $title_for_layout string
 * @var                    $nav_disable
 * @var array              $my_teams
 * @var                    $current_global_menu
 * @var                    $my_member_status
 * @var                    $is_evaluation_available
 * @var                    $evaluable_cnt
 * @var                    $unapproved_cnt
 * @var                    $all_alert_cnt
 * @var                    $is_mb_app
 */
?>
<!-- START app/View/Elements/header_logged_in.ctp -->
<header class="header">
    <div class="navbar navbar-fixed-top navbar-default gl-navbar <?= $is_mb_app ? "mb-app-nav" : null ?>"
         id="header">
        <div class="nav-container header-container">
            <button id="header-slide-menu" type="button"
                    class="<?= $is_mb_app ? "mb-app-header-toggle-icon" : "header-toggle-icon" ?>"
                    data-toggle="offcanvas"
                    data-target=".navbar-offcanvas">
                <i class="fa fa-navicon toggle-icon header-icons <?= $is_mb_app ? "mb-app-nav-icon" : null ?>"></i>
            </button>
            <div class="navbar-offcanvas offcanvas navmenu-fixed-left top_50px">
                <ul class="nav navbar-nav">
                    <li class="mtb_5px mtb-sm_0">
                        <a class="header-logo header_l-icons hoverPic <?= $current_global_menu == "home" ? "activeColumn" : null ?>"
                           href="<?= $this->Html->url('/') ?>"><!--suppress HtmlUnknownTarget -->
                            <div class="ta-sm_c">
                                <img src="<?= $this->Html->url('/img/logo_off.png') ?>" class="header-logo-img"
                                     alt="Goalous2.0" width="20px" height="20px">

                                <p class="font_11px font_heavyGray header_icon-text hidden-xs js-header-link">
                                    <?= __d('gl', "ホーム") ?>
                                </p>
                                <span class="visible-xs-inline va_bl ml_5px"><?= __d('gl', "ホーム") ?></span>
                            </div>
                        </a>
                    </li>
                    <li class="mtb_5px mtb-sm_0">
                        <a class="header-goal header_l-icons <?= $current_global_menu == "goal" ? "activeColumn" : null ?>"
                           href="<?= $this->Html->url(['controller' => 'goals', 'action' => 'index']) ?>">
                            <div class="ta-sm_c">
                                <i class="fa fa-flag js-header-link header-icon nav-xxs-icon header-icons"></i>

                                <p class="font_11px header_icon-text hidden-xs js-header-link">
                                    <?= __d('gl', "ゴール") ?>
                                </p>
                                <span class="visible-xs-inline ml_5px"><?= __d('gl', "ゴール") ?></span>
                            </div>
                        </a>
                    </li>
                    <li class="mtb_5px mtb-sm_0 <?= !empty($my_teams) ? null : 'hidden' ?>">

                        <a class="header-team header_l-icons <?= $current_global_menu == "team" ? "activeColumn" : null ?>"
                           href="<?= $this->Html->url(['controller' => 'teams', 'action' => 'main']) ?>">
                            <div class="ta-sm_c">
                                <i class="fa fa-users js-header-link header-icon nav-xxs-icon header-icons"></i>

                                <p class="font_11px header_icon-text hidden-xs js-header-link">
                                    <?= __d('gl', "チーム") ?>
                                </p>
                                <span class="visible-xs-inline ml_5px"><?= __d('gl', "チーム") ?></span>
                            </div>
                        </a>
                    </li>
                    <li>
                        <form class="nav-form-group" role="search">
                            <?php echo $this->Form->input('current_team',
                                                          array(
                                                              'type'      => 'select',
                                                              'options'   => !empty($my_teams) ? $my_teams : [__d('gl',
                                                                                                                  'チームがありません')],
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
                    <li class="header-search-group">
                        <form id="NavSearchForm" class="nav-form-group nav-search-form-group" role="search"
                              autocomplete="off">
                            <div class="input-group nav-search-form-input-group">
                                <div class="input-group-btn nav-search-button-group">
                                    <button type="button" id="NavSearchButton"
                                            class="btn nav-search-button dropdown-toggle" data-toggle="dropdown"
                                            aria-haspopup="true" aria-expanded="false">
                                        <i class="fa fa-user header-icons nav-form-icon nav-search-category-icon"
                                           data-category="user"></i>
                                        <i class="fa fa-flag header-icons nav-form-icon nav-search-category-icon none"
                                           data-category="goal"></i>
                                        <i class="fa fa-circle-o header-icons nav-form-icon nav-search-category-icon none"
                                           data-category="circle"></i>

                                        <span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu nav-search-form-dropdown" role="menu">
                                        <li><a href="#" style="" class="nav-search-category-item" data-category="user">
                                                <i class="fa fa-user header-drop-icons"></i>
                                                <?= __d('gl', 'ユーザー'); ?>
                                            </a>
                                        </li>
                                        <li><a href="#" class="nav-search-category-item" data-category="goal">
                                                <i class="fa fa-flag header-drop-icons"></i>
                                                <?= __d('gl', 'ゴール'); ?>
                                            </a>
                                        </li>
                                        <li><a href="#" class="nav-search-category-item" data-category="circle">
                                                <i class="fa fa-circle-o header-drop-icons"></i>
                                                <?= __d('gl', 'サークル'); ?>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                                <input type="text"
                                       id="NavSearchInput"
                                       class="form-control nav-search font_12px disable-change-warning">
                            </div>
                            <div id="NavSearchResults" class="nav-search-result"></div>
                        </form>
                    </li>
                    <li class="circle-list-in-hamburger visible-xxs hidden-xs">
                        <?= $this->element('circle_list_in_hamburger') ?>
                    </li>
                </ul>
            </div>
            <?php
            echo $this->element('header_logged_in_right')
            ?>
        </div>
    </div>
</header>
<!-- END app/View/Elements/header_logged_in.ctp -->
