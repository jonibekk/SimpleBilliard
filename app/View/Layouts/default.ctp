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
if (!isset($nav_disable)) {
    if ($this->Session->read('Auth.User.id')) {
        $nav_disable = false;
    }
    else {
        $nav_disable = true;
    }
}
?>
<?= $this->element('header', ['nav_disable' => $nav_disable]) ?>
<div id="container" class="container">
    <div class="row">
        <div class="col-sm-2">
            <div class="well">
                <p>test</p>

                <p>test</p>

                <p>test</p>

                <p>test</p>

                <p>test</p>

                <p>test</p>
            </div>
        </div>
        <div class="col-sm-6" role="main">
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
        <div class="col-sm-4">
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
