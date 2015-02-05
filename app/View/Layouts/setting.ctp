<?
/**
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Layouts
 * @since         CakePHP(tm) v 0.10.0.1076
 * @var $title_for_layout string
 * @var $this             View
 */
?>
<!-- START app/View/Layouts/setting.ctp -->
<!DOCTYPE html>
<!--suppress ALL -->
<html lang="ja">
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
            <div class="sidebar-setting" role="complementary">
                <ul class="nav">
                    <?= $this->fetch('sidebar') ?>
                </ul>
            </div>
        </div>
        <div class="col-xs-9 col-xxs-12 parent-flash" role="main">
            <?= $this->Session->flash(); ?>
            <?= $this->fetch('content'); ?>
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
<script type="text/javascript">
    $(document).ready(function () {
        $('body').scrollspy({target: '.sidebar-setting'});
    });
</script>
<? if (extension_loaded('newrelic')) {
    /** @noinspection PhpUndefinedFunctionInspection */
    echo newrelic_get_browser_timing_footer();
} ?>
</body>
</html>
<!-- END app/View/Layouts/setting.ctp -->
