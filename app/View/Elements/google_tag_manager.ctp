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
<!-- START app/View/Elements/google_tag_manager.ctp -->
<? if (GOOGLE_TAG_MANAGER_ID): ?>
    <!-- Google Tag Manager -->
    <!--suppress JSUnresolvedVariable -->
    <noscript>
        <iframe src="//www.googletagmanager.com/ns.html?id=<?= GOOGLE_TAG_MANAGER_ID ?>>"
                height="0" width="0" style="display:none;visibility:hidden"></iframe>
    </noscript>
    <script>
        cake.runGoogleTagManager("<? $page_type ?>");
    </script>
    <!-- End Google Tag Manager -->
<? endif; ?>
<!-- END app/View/Elements/google_tag_manager.ctp -->
