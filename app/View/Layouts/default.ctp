<?php /**
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Layouts
 * @since         CakePHP(tm) v 0.10.0.1076
 * @var string             $title_for_layout
 * @var CodeCompletionView $this
 * @var                    $action_name
 * @var                    $is_mb_app
 */
if (!isset($with_header_menu)) {
    $with_header_menu = true;
}
if (in_array($this->request->params['controller'], ['topics', 'saved_items']) && $is_mb_app) {
    $containerClass = "mod-sp";
    $bodyNoScrollClass = "mod-fixed";
} else {
    $containerClass = "";
    $bodyNoScrollClass = "";
}
?>
<?= $this->App->viewStartComment() ?>
<!DOCTYPE html>
<!--suppress ALL -->
<html lang="<?= $this->Lang->getLangCode() ?>">
<?= $this->element('head') ?>
<body class="<?= $is_mb_app ? 'mb-app-body' : 'body' ?> <?= $bodyNoScrollClass ?> read-only">
<?php if (extension_loaded('newrelic')) {
    /** @noinspection PhpUndefinedFunctionInspection */
    echo newrelic_get_browser_timing_header();
} ?>
<?php if ($this->Session->read('Auth.User.id') && $with_header_menu) {
    echo $this->element('Header/logged_in');
} else {
    echo $this->element('Header/not_logged_in');
}
?>

<?php
// TODO: .container is too general of a class for the main body container.
//       We should update .container styles to .body-container styles.
?>
<div class="container body-container <?= $containerClass?> <?= $displayMobileAppFooter ? 'mod-mobile-app' : '' ?>" >
    <div id="containerSubDiv" class="col-md-2 col-sm-4 col-xs-4 hidden-xxs layout-sub">
        <?php if (!$is_mb_app || $isTablet): ?>
        <div class="<?= !empty($my_teams) ? null : 'hidden' ?> left-side-container" id="jsLeftSideContainer">
            <div id="leftSideContainerInner" class="">
                <?= $this->element('dashboard_profile_card') ?>
                <?= $this->element('dashboard_menu_list') ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
    <div class="col-md-6 col-xs-8 col-xxs-12 layout-main" role="main">
        <?= $this->Session->flash(); ?>
        <!-- Remark -->
        <?= $this->fetch('content'); ?>
        <!-- /Remark -->
    </div>

    <?php
        if ($is_mb_app) {
            $loadKR = false;
        } else if ($isMobileBrowser && !$isTablet){
            $loadKR = false;
        } else{
            $loadKR = true;
        }
    ?>

    <?php if ($loadKR): ?>
        <div
            class="<?= !empty($my_teams) ? null : 'hidden' ?> right-side-container-wrap col-md-4 visible-md visible-lg col-xs-8 col-xxs-12 layout-goal"
            role="goal_area">
            <div class="right-side-container" id="jsRightSideContainer">
                <div id="kr-column"></div>
            </div>
        </div>
            <?= $this->Html->script('/js/react_kr_column_app.min', ['defer' => 'defer']);?>
<?php endif; ?>
</div>

<?= $displayMobileAppFooter ? $this->element('Footer/mobile_app_footer') : '' ?>


<?= $this->element('common_modules') ?>

<?= $this->element('modals') ?>
<!-- START fetch modal -->
<?= $this->fetch('modal') ?>
<!-- END fetch modal -->

<?php
// Only from mobile app, don't load dashboard
$displayDashboard = !$is_mb_app;
echo $this->element('gl_common_js', ['loadRightColumn' => $displayDashboard]);
?>

<!-- START fetch script -->
<?= $this->fetch('script') ?>
<!-- END fetch script -->
<?php if (extension_loaded('newrelic')) {
    /** @noinspection PhpUndefinedFunctionInspection */
    echo newrelic_get_browser_timing_footer();
} ?>
</body>
</html>
<?= $this->App->viewEndComment() ?>
