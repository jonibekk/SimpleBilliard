<?php /**
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Layouts
 * @since         CakePHP(tm) v 0.10.0.1076
 * @var                    $title_for_layout string
 * @var CodeCompletionView $this
 */
if (!isset($without_footer)) {
    $without_footer = false;
}
if (!isset($with_header_menu)) {
    $with_header_menu = true;
}
?>
<?= $this->App->viewStartComment() ?>
<!DOCTYPE html>
<!--suppress ALL -->
<html lang="<?= $this->Lang->getLangCode() ?>">
<?= $this->element('head') ?>
<body class="<?= $is_mb_app ? 'mb-app-body' : 'body' ?>">
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
<div id="container" class="container">
    <?= $this->Session->flash(); ?>
    <?= $this->fetch('content'); ?>
    <?php if ($this->App->needDisplayFooter()): ?>
        <?= $this->element('footer') ?>
    <?php endif; ?>
</div>
<?= $this->element('common_modules') ?>
<?= $this->element('modals') ?>
<!-- START fetch modal -->
<?= $this->fetch('modal') ?>
<!-- END fetch modal -->

<?php
// in one column, don't need load right kr column
echo $this->element('gl_common_js', ['loadRightColumn' => false]);
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
