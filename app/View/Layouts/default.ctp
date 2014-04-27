<?php
/**
 *
 *
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Layouts
 * @since         CakePHP(tm) v 0.10.0.1076
 * @var $title_for_layout string
 */

/*
 * @var $this View
 */
$cakeDescription = __d('cake_dev', 'CakePHP: the rapid development php framework');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php echo $this->Html->charset(); ?>
    <title>
        <?php echo $cakeDescription ?>:
        <?php echo $title_for_layout; ?>
    </title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php
    echo $this->Html->meta('icon');

    //echo $this->Html->css('cake.generic');
    echo $this->Html->css('bootstrap.min.css', array('media' => 'screen'));
    echo $this->Html->css('bw-united.min', array('media' => 'screen'));
    echo $this->Html->css('style', array('media' => 'screen'));
    echo $this->Html->css('font-awesome.min.css');
    echo $this->fetch('css');

    echo $this->fetch('meta');
    ?>
    <!--[if lt IE 9]>
    <?php echo $this->Html->script('html5shiv')?>
    <?php echo $this->Html->script('respond.min')?>
    <![endif]-->
    <!-- start Mixpanel -->
    <script type="text/javascript">(function (e, b) {
            if (!b.__SV) {
                var a, f, i, g;
                window.mixpanel = b;
                b._i = [];
                b.init = function (a, e, d) {
                    function f(b, h) {
                        var a = h.split(".");
                        2 == a.length && (b = b[a[0]], h = a[1]);
                        b[h] = function () {
                            b.push([h].concat(Array.prototype.slice.call(arguments, 0)))
                        }
                    }

                    var c = b;
                    "undefined" !== typeof d ? c = b[d] = [] : d = "mixpanel";
                    c.people = c.people || [];
                    c.toString = function (b) {
                        var a = "mixpanel";
                        "mixpanel" !== d && (a += "." + d);
                        b || (a += " (stub)");
                        return a
                    };
                    c.people.toString = function () {
                        return c.toString(1) + ".people (stub)"
                    };
                    i = "disable track track_pageview track_links track_forms register register_once alias unregister identify name_tag set_config people.set people.set_once people.increment people.append people.track_charge people.clear_charges people.delete_user".split(" ");
                    for (g = 0; g < i.length; g++)f(c, i[g]);
                    b._i.push([a, e, d])
                };
                b.__SV = 1.2;
                a = e.createElement("script");
                a.type = "text/javascript";
                a.async = !0;
                a.src = ("https:" === e.location.protocol ? "https:" : "http:") + '//cdn.mxpnl.com/libs/mixpanel-2.2.min.js';
                f = e.getElementsByTagName("script")[0];
                f.parentNode.insertBefore(a, f)
            }
        })(document, window.mixpanel || []);
        mixpanel.init("2486fb1ff5a70a2e0c1abf3e0bc7788d");
    </script>
    <!-- end Mixpanel -->
    <!-- Start Visual Website Optimizer Asynchronous Code -->
    <script type='text/javascript'>
        var _vwo_code = (function () {
            var account_id = 69255,
                settings_tolerance = 2000,
                library_tolerance = 2500,
                use_existing_jquery = false,
// DO NOT EDIT BELOW THIS LINE
                f = false, d = document;
            return{use_existing_jquery: function () {
                return use_existing_jquery;
            }, library_tolerance: function () {
                return library_tolerance;
            }, finish: function () {
                if (!f) {
                    f = true;
                    var a = d.getElementById('_vis_opt_path_hides');
                    if (a)a.parentNode.removeChild(a);
                }
            }, finished: function () {
                return f;
            }, load: function (a) {
                var b = d.createElement('script');
                b.src = a;
                b.type = 'text/javascript';
                b.innerText;
                b.onerror = function () {
                    _vwo_code.finish();
                };
                d.getElementsByTagName('head')[0].appendChild(b);
            }, init: function () {
                settings_timer = setTimeout('_vwo_code.finish()', settings_tolerance);
                this.load('//dev.visualwebsiteoptimizer.com/j.php?a=' + account_id + '&u=' + encodeURIComponent(d.URL) + '&r=' + Math.random());
                var a = d.createElement('style'), b = 'body{opacity:0 !important;filter:alpha(opacity=0) !important;background:none !important;}', h = d.getElementsByTagName('head')[0];
                a.setAttribute('id', '_vis_opt_path_hides');
                a.setAttribute('type', 'text/css');
                if (a.styleSheet)a.styleSheet.cssText = b; else a.appendChild(d.createTextNode(b));
                h.appendChild(a);
                return settings_timer;
            }};
        }());
        _vwo_settings_timer = _vwo_code.init();
    </script>
    <!-- End Visual Website Optimizer Asynchronous Code -->
</head>
<body>
<?php if (extension_loaded('newrelic')) {
    echo newrelic_get_browser_timing_header();
} ?>
<div class="navbar navbar-default navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <a href="#" class="navbar-brand">CakeDevTest</a>
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
    <?php echo $this->Session->flash(); ?>

    <?php echo $this->fetch('content'); ?>
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
        <?php if (extension_loaded('newrelic')) {
            /** @noinspection PhpUndefinedFunctionInspection */
            echo newrelic_get_browser_timing_footer();
        } ?>

    </footer>

</div>
<?php
echo $this->fetch('script');
echo $this->Html->script('jquery-2.1.0.min');
echo $this->Html->script('bootstrap.min');
?>
</body>
</html>
