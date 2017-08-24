<?php /**
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Layouts
 * @since         CakePHP(tm) v 0.10.0.1076
 * @var                    $title_for_layout string
 * @var CodeCompletionView $this
 */

?>
<?= $this->App->viewStartComment() ?>
<!DOCTYPE html>
<!--suppress ALL -->
<html lang="<?= $this->Lang->getLangCode() ?>">
<?= $this->element('head') ?>
<body class="body-no-header">
<?php if (extension_loaded('newrelic')) {
    /** @noinspection PhpUndefinedFunctionInspection */
    echo newrelic_get_browser_timing_header();
} ?>
<div id="container" class="container">
    <?= $this->Session->flash(); ?>
    <?= $this->fetch('content'); ?>
    <?php if ($this->App->needDisplayFooter()): ?>
        <?= $this->element('footer') ?>
    <?php endif; ?>
</div>

<?php if (extension_loaded('newrelic')) {
    /** @noinspection PhpUndefinedFunctionInspection */
    echo newrelic_get_browser_timing_footer();
} ?>
</body>
</html>
<?= $this->App->viewEndComment() ?>
