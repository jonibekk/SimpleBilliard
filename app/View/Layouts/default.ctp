<?
/**
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Layouts
 * @since         CakePHP(tm) v 0.10.0.1076
 * @var $title_for_layout string
 * @var $this             View
 * @var $avail_sub_menu
 */
?>
<!-- START app/View/Layouts/default.ctp -->
<!DOCTYPE html>
<!--suppress ALL -->
<html lang="ja">
<?= $this->element('head') ?>
<body class="<?= (isset($avail_sub_menu) && $avail_sub_menu ? 'avail-sub-menu' : null) ?>">
<? if (extension_loaded('newrelic')) {
    /** @noinspection PhpUndefinedFunctionInspection */
    echo newrelic_get_browser_timing_header();
} ?>
<?=$this->element('google_tag_manager')?>
<?
if ($this->Session->read('Auth.User.id')) {
    echo $this->element('header_logged_in');
}
else {
    echo $this->element('header_not_logged_in');
}
?>
<div id="container" class="container">
    <div class="row">
        <div class="col-md-2 col-sm-4 col-xs-4 hidden-xxs layout-sub">
            <?= $this->element('dashboard_profile_card') ?>
            <?= $this->element('circle_list') ?>

        </div>
        <div class="col-md-6 col-sm-8 col-xs-8 col-xxs-12 layout-main" role="main">
            <?= $this->Session->flash(); ?>
            <?= $this->fetch('content'); ?>
        </div>
        <div class="col-md-4 visible-md visible-lg col-sm-8 col-xs-8 col-xxs-12 layout-goal" role="goal_area">
            <?= $this->element('my_goals_area') ?>
        </div>
    </div>

    <?= $this->element('footer') ?>
</div>

<?= $this->element('modals') ?>
<!-- START fetch modal -->
<?= $this->fetch('modal') ?>
<!-- END fetch modal -->
<?= $this->element('gl_common_js') ?>
<!-- START fetch script -->
<?= $this->fetch('script') ?>
<!-- END fetch script -->
<? if (extension_loaded('newrelic')) {
    /** @noinspection PhpUndefinedFunctionInspection */
    echo newrelic_get_browser_timing_footer();
} ?>
</body>
</html>
<!-- END app/View/Layouts/default.ctp -->
