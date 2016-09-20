<?php /**
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Layouts
 * @since         CakePHP(tm) v 0.10.0.1076
 * @var                    $title_for_layout string
 * @var CodeCompletionView $this
 */
if (!isset($with_header_menu)) {
    $with_header_menu = true;
}
?>
<?= $this->App->viewStartComment()?>
<!DOCTYPE html>
<!--suppress ALL -->
<html lang="ja">
<?= $this->element('head') ?>
<body class="body">
<?php if (extension_loaded('newrelic')) {
    /** @noinspection PhpUndefinedFunctionInspection */
    echo newrelic_get_browser_timing_header();
} ?>
<?= $this->element('google_tag_manager', ['page_type' => 'app']) ?>
<?php if ($this->Session->read('Auth.User.id') && $with_header_menu) {
    echo $this->element('header_logged_in');
} else {
    echo $this->element('header_not_logged_in');
}
?>
<div id="container" class="container">
    <div class="row">
        <div class="col-xs-3 <?php if (isset($hidden_sidebar_xxs) && $hidden_sidebar_xxs): ?>hidden-xxs<?php endif ?>">
            <?= $this->fetch('sidebar') ?>
        </div>
        <div
            class="parent-flash <?= (isset($hidden_sidebar_xxs) && $hidden_sidebar_xxs) ? "col-xxs-12 col-xs-9" : "col-xs-9" ?> "
            role="main" id="ScrollSpyContents">
            <?= $this->Session->flash(); ?>
            <?= $this->fetch('content'); ?>
        </div>
    </div>
    <?php if ($this->App->needDisplayFooter()): ?>
        <?= $this->element('footer') ?>
    <?php endif; ?>
</div>
<?= $this->element('common_modules') ?>
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
<?= $this->App->viewEndComment()?>
