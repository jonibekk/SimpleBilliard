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
<?= $this->element('header_logged_in') ?>
<div id="container" class="container">
    <div class="row">
        <div class="col-xs-3 hidden-xxs">
            <div class="gl-sidebar-setting" role="complementary">
                <ul class="nav">
                    <?= $this->fetch('sidebar') ?>
                </ul>
            </div>
        </div>
        <div class="col-xs-9 col-xxs-12 gl-sections" role="main">
            <?= $this->Session->flash(); ?>
            <?= $this->fetch('content'); ?>
        </div>
        <?= $this->element('footer') ?>
    </div>
    <? if (extension_loaded('newrelic')) {
        /** @noinspection PhpUndefinedFunctionInspection */
        echo newrelic_get_browser_timing_footer();
    } ?>
    <?= $this->element('gl_common_js') ?>
    <?= $this->fetch('script') ?>
    <script type="text/javascript">
        $(document).ready(function () {
            $('body').scrollspy({ target: '.gl-sidebar-setting' });
        });
    </script>
</body>
</html>
