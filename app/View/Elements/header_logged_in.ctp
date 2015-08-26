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
 */
?>
<!-- START app/View/Elements/header_logged_in.ctp -->
<header class="header">
    <div class="navbar navbar-fixed-top navbar-default gl-navbar" id="header">
        <div class="nav-container header-container">
            <button type="button" class="header-toggle-icon" data-toggle="offcanvas"
                    data-target=".navbar-offcanvas">
                <i class="fa fa-navicon toggle-icon header-icons"></i>
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
                    <li class="mtb_5px mtb-sm_0">
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
                        <form class="nav-form-group" role="search">
                            <i class="fa fa-search header-icons nav-form-icon"></i>
                            <input type="text"
                                   class="form-control nav-search font_12px disable-change-warning develop--search"
                                   placeholder="Search">
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
