<?php /**
 * Created by PhpStorm.
 * User: bigplants
 * Date: 4/30/14
 * Time: 5:52 PM
 *
 * @var CodeCompletionView $this
 **/
?>
<?= $this->App->viewStartComment() ?>
<!-- start VWO and Mixpanel Integration Code-->
<script type="text/javascript">
    var _vis_opt_queue = window._vis_opt_queue || [], _vis_counter = 0, mixpanel = window.mixpanel || [];
    _vis_opt_queue.push(function () {
        try {
            if (!_vis_counter) {
                var _vis_data = {}, _vis_combination, _vis_id, _vis_l = 0;
                for (; _vis_l < _vwo_exp_ids.length; _vis_l++) {
                    _vis_id = _vwo_exp_ids[_vis_l];
                    if (_vwo_exp[_vis_id].ready) {
                        _vis_combination = _vis_opt_readCookie('_vis_opt_exp_' + _vis_id + '_combi');
                        if (typeof(_vwo_exp[_vis_id].combination_chosen) != "undefined")
                            _vis_combination = _vwo_exp[_vis_id].combination_chosen;
                        if (typeof(_vwo_exp[_vis_id].comb_n[_vis_combination]) != "undefined") {
                            _vis_data['VWO-Test-ID-' + _vis_id] = _vwo_exp[_vis_id].comb_n[_vis_combination];
                            _vis_counter++;
                        }
                    }
                }
                // Use the _vis_data object created above to fetch the data,
                // key of the object is the Test ID and the value is Variation Name
                if (_vis_counter) mixpanel.push(['register_once', _vis_data]);
            }
        }
        catch (err) {
        }
        ;
    });
</script>
<!-- end VWO and Mixpanel Integration Code-->
<?php if (VWO_ID): ?>
    <!-- Start Visual Website Optimizer Asynchronous Code -->
    <script type='text/javascript'>
        var _vwo_code = (function () {
            var account_id =<?= VWO_ID?>,
                settings_tolerance = 2000,
                library_tolerance = 2500,
                use_existing_jquery = false,
// DO NOT EDIT BELOW THIS LINE
                f = false, d = document;
            return {
                use_existing_jquery: function () {
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
                }
            };
        }());
        _vwo_settings_timer = _vwo_code.init();
    </script>
    <!-- End Visual Website Optimizer Asynchronous Code -->
<?php endif; ?>
<?php if (MIXPANEL_TOKEN): ?>
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
        mixpanel.init("<?= MIXPANEL_TOKEN?>");</script>
    <!-- end Mixpanel -->
<?php endif; ?>
<?php if (INTERCOM_APP_ID): ?>

    <?php
    $fa_secret = 'OFF';
    if (!empty($this->Session->read('Auth.User.2fa_secret'))):
        $fa_secret = 'ON';
    endif;
    ?>
    <!-- start Intercom -->
    <script>
        enabled_intercom_icon = true;
        if (window.innerWidth < 992) {
            enabled_intercom_icon = false;
        }
        window.intercomSettings = {
            app_id: "<?=INTERCOM_APP_ID?>",
            <?php if ($this->Session->read('Auth.User.id')): ?>
            name: "<?= h($this->Session->read('Auth.User.display_username')) ?>", // Full name
            email: "<?= h($this->Session->read('Auth.User.PrimaryEmail.email')) ?>", // Email address
            created_at: <?= h($this->Session->read('Auth.User.created')) ?>, // Signup date as a Unix timestamp
            user_id: <?= h(intval($this->Session->read('Auth.User.id'))) ?>, // User Id
            setup: <?= h(intval($this->Session->read('Auth.User.setup_complete_flg'))) ?>, // Setup Complete Flag
            user_del: <?= h(intval($this->Session->read('Auth.User.del_flg'))) ?>, // User del Flag
            default_team: <?= h(intval($this->Session->read('Auth.User.default_team_id'))) ?>, // User DEFAULT TEAM id
            user_timezone: <?= h(intval($this->Session->read('Auth.User.timezone'))) ?>, // User timezone
            language: "<?= h($this->Session->read('Auth.User.language')) ?>", // Language
            "2SV": "<?= h($fa_secret) ?>", // 2fa Secret
            <?php endif ?>
            <?php if (isset($my_member_status) && $my_member_status): ?>
            "team_id": <?= h(intval($my_member_status['TeamMember']['team_id'])) ?>,
            "team_name": "<?= h($my_member_status['Team']['name']) ?>",
            "team_admin": <?= h(intval($my_member_status['TeamMember']['admin_flg'])) ?>,
            "teams_belong": <?= isset($my_teams) ? h(intval(count($my_teams))) : 0 ?>, // Teams count that user belongs to
            <?php endif ?>
        };
        if (!enabled_intercom_icon) {
            window.intercomSettings.hide_default_launcher = true;
            window.intercomSettings.custom_launcher_selector = "#Intercom";
        }
    </script>
    <script>(function () {
            var w = window;
            var ic = w.Intercom;
            if (typeof ic === "function") {
                ic('reattach_activator');
                ic('update', intercomSettings);
            } else {
                var d = document;
                var i = function () {
                    i.c(arguments)
                };
                i.q = [];
                i.c = function (args) {
                    i.q.push(args)
                };
                w.Intercom = i;
                function l() {
                    var s = d.createElement('script');
                    s.type = 'text/javascript';
                    s.async = true;
                    s.src = 'https://widget.intercom.io/widget/<?=INTERCOM_APP_ID?>';
                    var x = d.getElementsByTagName('script')[0];
                    x.parentNode.insertBefore(s, x);
                }

                if (w.attachEvent) {
                    w.attachEvent('onload', l);
                } else {
                    w.addEventListener('load', l, false);
                }
            }
        })()</script>
    <!-- end Intercom -->
<?php endif; ?>
<?= $this->App->viewEndComment() ?>
