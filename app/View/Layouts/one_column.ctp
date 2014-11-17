<?
/**
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Layouts
 * @since         CakePHP(tm) v 0.10.0.1076
 * @var $title_for_layout string
 * @var $this             View
 */
?>
<!-- START app/View/Layouts/one_column.ctp -->
<!DOCTYPE html>
<!--suppress ALL -->
<html lang="ja">
<?= $this->element('head') ?>
<body>
<? if (extension_loaded('newrelic')) {
    /** @noinspection PhpUndefinedFunctionInspection */
    echo newrelic_get_browser_timing_header();
} ?>
<?
if ($this->Session->read('Auth.User.id')) {
    echo $this->element('header_logged_in');
}
else {
    echo $this->element('header_not_logged_in');
}
?>
<div id="container" class="container">
    <?= $this->Session->flash(); ?>

    <?= $this->fetch('content'); ?>

    <?= $this->element('footer') ?>
</div>
<? if (extension_loaded('newrelic')) {
    /** @noinspection PhpUndefinedFunctionInspection */
    echo newrelic_get_browser_timing_footer();
} ?>
<?= $this->element('modals') ?>
<?= $this->element('gl_common_js') ?>
<?= $this->fetch('script') ?>
</body>
</html>
<!-- END app/View/Layouts/one_column.ctp -->
