<?
/**
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Layouts
 * @since         CakePHP(tm) v 0.10.0.1076
 * @var $title_for_layout string
 * @var $this             View
 */
?>
<!DOCTYPE html>
<!--suppress ALL -->
<html lang="en">
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
    <div class="row">
        <div class="col-md-2 col-sm-3 col-xs-3 hidden-xxs">
            <div class="well">
            <p>test</p>

                <p>test</p>

                <p>test</p>

                <p>test</p>

                <p>test</p>

                <p>test</p>
            </div>
        </div>
        <div class="col-md-6 col-sm-9 col-xs-9 col-xxs-12" role="main">
            <?= $this->Session->flash(); ?>
            <?= $this->fetch('content'); ?>
            <div class="well">
                <p>test</p>

                <p>test</p>

                <p>test</p>

                <p>test</p>

                <p>test</p>

                <p>test</p>
            </div>
        </div>
        <div class="col-md-4 visible-md visible-lg">
            <div class="well">
            <p>test</p>

                <p>test</p>

                <p>test</p>

                <p>test</p>

                <p>test</p>

                <p>test</p>
            </div>

        </div>
    </div>

    <?= $this->element('footer') ?>
</div>
<? if (extension_loaded('newrelic')) {
    /** @noinspection PhpUndefinedFunctionInspection */
    echo newrelic_get_browser_timing_footer();
} ?>
<?= $this->element('modal_tutorial') ?>
<?= $this->element('gl_common_js') ?>
</body>
</html>
