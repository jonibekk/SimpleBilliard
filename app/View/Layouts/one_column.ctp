<?php /**
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Layouts
 * @since         CakePHP(tm) v 0.10.0.1076
 * @var                    $title_for_layout string
 * @var CodeCompletionView $this
 */
?>
<!-- START app/View/Layouts/one_column.ctp -->
<!DOCTYPE html>
<!--suppress ALL -->
<html lang="ja">
<?= $this->element('head') ?>
<body>
<?php if (extension_loaded('newrelic')) {
    /** @noinspection PhpUndefinedFunctionInspection */
    echo newrelic_get_browser_timing_header();
} ?>
<?= $this->element('google_tag_manager') ?>
<?php if ($this->Session->read('Auth.User.id')) {
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
<?= $this->element('modals') ?>
<!-- START fetch modal -->
<?= $this->fetch('modal') ?>
<!-- END fetch modal -->
<?= $this->element('gl_common_js') ?>
<!-- START fetch script -->
<?= $this->fetch('script') ?>
<!-- END fetch script -->
<?php if (extension_loaded('newrelic')) {
    /** @noinspection PhpUndefinedFunctionInspection */
    echo newrelic_get_browser_timing_footer();
} ?>
</body>
</html>
<!-- END app/View/Layouts/one_column.ctp -->
