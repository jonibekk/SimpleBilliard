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
if ($this->request->params['controller'] === 'topics' && $is_mb_app) {
    $containerClass = "mod-sp";
    $bodyNoScrollClass = "mod-fixed";
} else {
    $containerClass = "";
    $bodyNoScrollClass = "";
}
?>
<?= $this->App->viewStartComment()?>
<!DOCTYPE html>
<!--suppress ALL -->
<html lang="<?= $this->Lang->getLangCode() ?>">
<?= $this->element('head') ?>
<body class="<?= $is_mb_app ? 'mb-app-body' : 'body' ?> <?=$bodyNoScrollClass?>">
<?php if (extension_loaded('newrelic')) {
    /** @noinspection PhpUndefinedFunctionInspection */
    echo newrelic_get_browser_timing_header();
} ?>
<?php if ($this->Session->read('Auth.User.id') && $with_header_menu) {
    echo $this->element('header_logged_in');
} else {
    echo $this->element('header_not_logged_in');
}
?>

<?php // spec: Only other mobile app env, and only feed page, displaying subheader. ?>
<?php
// TODO:Uncomment this after release native app
//if (!$is_mb_app && $this->request->params['controller'] === 'pages' && $this->request->params['action'] === 'display') {
if ($this->request->params['controller'] === 'pages' && $this->request->params['action'] === 'display') {
    echo $this->element('header_sp_feeds_alt');
}
?>


<div id="container" class="container <?= $containerClass?>">
    <div class="col-md-2 col-sm-4 col-xs-4 hidden-xxs layout-sub">
        <div class="<?= !empty($my_teams) ? null : 'hidden' ?> left-side-container" id="jsLeftSideContainer">
            <?= $this->element('dashboard_profile_card') ?>
            <?= $this->element('circle_list') ?>
        </div>
    </div>
    <div class="col-md-6 col-xs-8 col-xxs-12 layout-main" role="main">
        <?= $this->Session->flash(); ?>
        <!-- Remark -->
        <?= $this->fetch('content'); ?>
        <!-- /Remark -->
    </div>
    <div
        class="<?= !empty($my_teams) ? null : 'hidden' ?> right-side-container-wrap col-md-4 visible-md visible-lg col-xs-8 col-xxs-12 layout-goal"
        role="goal_area">
        <div class="right-side-container" id="jsRightSideContainer">
            <div id="kr-column"></div>
        </div>
    </div>
</div>
<?= $this->element('common_modules') ?>

<?= $this->element('modals') ?>
<!-- START fetch modal -->
<?= $this->fetch('modal') ?>
<!-- END fetch modal -->

<?php
// TODO: Should change to not importing this file in mb app.
//       But we should change after changing progress link in mb app footer.
?>
<?= $this->element('gl_common_js', ['display_dashboard' => true]) ?>

<!-- START fetch script -->
<?= $this->fetch('script') ?>
<!-- END fetch script -->
<?php if (extension_loaded('newrelic')) {
    /** @noinspection PhpUndefinedFunctionInspection */
    echo newrelic_get_browser_timing_footer();
} ?>
</body>
</html>
<?= $this->App->viewEndComment()?>
