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

<?php if (defined('INTERCOM_APP_ID') && INTERCOM_APP_ID): ?>

    <?php
    $fa_secret = 'OFF';
    if (!empty($this->Session->read('Auth.User.2fa_secret'))) {
        $fa_secret = 'ON';
    }
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
            default_team: <?= h(intval($this->Session->read('Auth.User.default_team_id'))) ?>, // User DEFAULT TEAM id
            user_timezone: <?= h(intval($this->Session->read('Auth.User.timezone'))) ?>, // User timezone
            language: "<?= h($this->Session->read('Auth.User.language')) ?>", // Language
            <?php if(defined("INTERCOM_IDENTITY_VERIFICATION_SECRET") and INTERCOM_IDENTITY_VERIFICATION_SECRET):?>
            user_hash: "<?= hash_hmac(
                'sha256',
                h(intval($this->Session->read('Auth.User.id'))),
                INTERCOM_IDENTITY_VERIFICATION_SECRET
            );?>", // HMAC using SHA-256
            <?php endif;?>
            "2SV": "<?= h($fa_secret) ?>", // 2fa Secret
            <?php endif ?>
            <?php if (isset($my_member_status) && $my_member_status): ?>
            team_id: <?= h(intval($my_member_status['TeamMember']['team_id'])) ?>,
            team_name: "<?= h($my_member_status['Team']['name']) ?>",
            team_admin: <?= h(intval($my_member_status['TeamMember']['admin_flg'])) ?>,
            teams_belong: <?= isset($my_teams) ? h(intval(count($my_teams))) : 0 ?>, // Teams count that user belongs to
            <?php endif ?>
        };
        if (!enabled_intercom_icon) {
            window.intercomSettings.hide_default_launcher = true;
        }

        window.intercomSettings.custom_launcher_selector = ".intercom-launcher";

        (function () {
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
        })()
    </script>
    <!-- end Intercom -->
    <script>
        //intercomのリンクを非表示にする
        if (enabled_intercom_icon) {
            var intercomLink = document.getElementById("IntercomLink");
            if (intercomLink) {
                intercomLink.style.display = 'none';
            }
        }
    </script>
<?php endif; ?>
<?= $this->App->viewEndComment() ?>
