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
<head>
    <?= $this->Html->charset(); ?>
    <title>
        <?= $title_for_layout; ?>
    </title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?
    echo $this->Html->meta('icon');
    //echo $this->Html->css('bootstrap.min.css', array('media' => 'screen'));
    echo $this->Html->css('bw-simplex.min', array('media' => 'screen'));
    echo $this->Html->css('font-awesome.min');
    echo $this->Html->css('bootstrapValidator.min');
    echo $this->Html->css('pnotify.custom.min');
    echo $this->Html->css('style', array('media' => 'screen'));
    echo $this->fetch('css');
    echo $this->fetch('meta');
    ?>
    <!--[if lt IE 9]>
    <?= $this->Html->script('html5shiv')?>
    <?= $this->Html->script('respond.min')?>
    <![endif]-->
    <?
    //公開環境のみタグを有効化
    if (PUBLIC_ENV) {
        /** @noinspection PhpDeprecationInspection */
        echo $this->element('external_service_tags');
    }
    ?>
</head>
<body>
<? if (extension_loaded('newrelic')) {
    /** @noinspection PhpUndefinedFunctionInspection */
    echo newrelic_get_browser_timing_header();
} ?>
<div class="navbar navbar-default navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <a href="/" class="navbar-brand"><?= $title_for_layout ?></a>
            <button class="navbar-toggle" type="button" data-toggle="collapse" data-target="#navbar-main">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
        </div>
        <div class="navbar-collapse collapse" id="navbar-main">
            <ul class="nav navbar-nav">
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#" id="themes">Themes <span
                            class="caret"></span></a>
                    <ul class="dropdown-menu" aria-labelledby="themes">
                        <li><a href="../default/">Default</a></li>
                        <li class="divider"></li>
                        <li><a href="../amelia/">Amelia</a></li>
                        <li><a href="../cerulean/">Cerulean</a></li>
                        <li><a href="../cosmo/">Cosmo</a></li>
                        <li><a href="../cyborg/">Cyborg</a></li>
                        <li><a href="../flatly/">Flatly</a></li>
                        <li><a href="../journal/">Journal</a></li>
                        <li><a href="../lumen/">Lumen</a></li>
                        <li><a href="../readable/">Readable</a></li>
                        <li><a href="../simplex/">Simplex</a></li>
                        <li><a href="../slate/">Slate</a></li>
                        <li><a href="../spacelab/">Spacelab</a></li>
                        <li><a href="../superhero/">Superhero</a></li>
                        <li><a href="../united/">United</a></li>
                        <li><a href="../yeti/">Yeti</a></li>
                    </ul>
                </li>
                <li>
                    <a href="../help/">Help</a>
                </li>
                <li>
                    <a href="http://news.bootswatch.com">Blog</a>
                </li>
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#" id="download">Download <span
                            class="caret"></span></a>
                    <ul class="dropdown-menu" aria-labelledby="download">
                        <li><a href="./bootstrap.min.css">bootstrap.min.css</a></li>
                        <li><a href="./bootstrap.css">bootstrap.css</a></li>
                        <li class="divider"></li>
                        <li><a href="./variables.less">variables.less</a></li>
                        <li><a href="./bootswatch.less">bootswatch.less</a></li>
                    </ul>
                </li>
            </ul>

            <ul class="nav navbar-nav navbar-right">
                <li><a href="http://builtwithbootstrap.com/" target="_blank">Built With Bootstrap</a></li>
                <li><a href="https://wrapbootstrap.com/?ref=bsw" target="_blank">WrapBootstrap</a></li>
            </ul>

        </div>
    </div>
</div>

<div id="container" class="container">
    <?= $this->Session->flash(); ?>

    <?= $this->fetch('content'); ?>
    <footer>
        <div class="row">
            <div class="col-lg-12">

                <ul class="list-unstyled">
                    <li class="pull-right"><a href="#top">Back to top</a></li>
                    <li><a href="http://news.bootswatch.com"
                           onclick="pageTracker._link(this.href); return false;">Blog</a>
                    </li>
                    <li><a href="http://feeds.feedburner.com/bootswatch">RSS</a></li>
                    <li><a href="https://twitter.com/thomashpark">Twitter</a></li>
                    <li><a href="https://github.com/thomaspark/bootswatch/">GitHub</a></li>
                    <li><a href="../help/#api">API</a></li>
                    <li>
                        <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&amp;hosted_button_id=F22JEM3Q78JC2">Donate</a>
                    </li>
                </ul>
                <p>Made by <a href="http://thomaspark.me" rel="nofollow">Thomas Park</a>. Contact him at <a
                        href="mailto:thomas@bootswatch.com">thomas@bootswatch.com</a>.</p>

                <p>Code released under the <a href="https://github.com/thomaspark/bootswatch/blob/gh-pages/LICENSE">MIT
                        License</a>.</p>

                <p>Based on <a href="http://getbootstrap.com" rel="nofollow">Bootstrap</a>. Icons from <a
                        href="http://fortawesome.github.io/Font-Awesome/" rel="nofollow">Font Awesome</a>. Web fonts
                    from <a
                        href="http://www.google.com/webfonts" rel="nofollow">Google</a>.</p>
            </div>
        </div>
        <? if (extension_loaded('newrelic')) {
            /** @noinspection PhpUndefinedFunctionInspection */
            echo newrelic_get_browser_timing_footer();
        } ?>

    </footer>

</div>
<?
echo $this->Html->script('jquery-2.1.0.min');
echo $this->Html->script('bootstrap.min');
echo $this->Html->script('bootstrapValidator.min');
echo $this->Html->script('bvAddition');
echo $this->Html->script('pnotify.custom.min');
echo $this->Html->script('gl_basic');
echo $this->element('gl_common_js');
echo $this->fetch('script');
echo $this->Session->flash('pnotify');
//環境を識別できるようにリボンを表示
switch (ENV_NAME) {
    case 'stg':
        echo $this->Html->script('http://quickribbon.com/ribbon/2014/04/c966588e9495aa7b205aeaaf849d674f.js');
        break;
    case 'local':
        echo $this->Html->script('http://quickribbon.com/ribbon/2014/04/b13dfc8e5d887b8725f256c31cc1dff4.js');
        break;
    default:
        break;
}
?>
</body>
</html>
