<?php /**
 * Created by PhpStorm.
 * User: daikihirakata
 * Date: 4/24/15
 * Time: 00:43
 *
 * @var CodeCompletionView $this
 */
if (!isset($page_type)) {
    $page_type = 'app';
}

?>
<?= $this->App->viewStartComment() ?>
<?php if (GOOGLE_TAG_MANAGER_ID): ?>
    <!-- Google Tag Manager -->
    <!--suppress JSUnresolvedVariable -->
    <noscript>
        <iframe src="//www.googletagmanager.com/ns.html?id=<?= GOOGLE_TAG_MANAGER_ID ?>>"
                height="0" width="0" style="display:none;visibility:hidden"></iframe>
    </noscript>
    <script>
        sendToGoogleTagManager("<?= $page_type ?>");

        function sendToGoogleTagManager(page_type) {
            dataLayer = [{
                "loggedIn": "<?= $this->Session->read('Auth.User.id') ? "true" : "false"?>",
                "teamId": "<?= $this->Session->read('current_team_id')?>",
                "userId": "<?= $this->Session->read('Auth.User.id')?>",
                "pageType": page_type
            }];
            (function (w, d, s, l, i) {
                w[l] = w[l] || [];
                w[l].push({
                    'gtm.start': new Date().getTime(), event: 'gtm.js'
                });
                var f = d.getElementsByTagName(s)[0],
                    j = d.createElement(s), dl = l != 'dataLayer' ? '&l=' + l : '';
                j.async = true;
                j.src =
                    '//www.googletagmanager.com/gtm.js?id=' + i + dl;
                f.parentNode.insertBefore(j, f);
            })(window, document, 'script', 'dataLayer', "<?= GOOGLE_TAG_MANAGER_ID ?>");
        }
    </script>
    <!-- End Google Tag Manager -->
<?php endif; ?>
<?= $this->App->viewEndComment() ?>
