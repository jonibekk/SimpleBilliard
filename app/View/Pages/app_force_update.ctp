<?php
$userAgent = UserAgent::detect(Hash::get($_SERVER, 'HTTP_USER_AGENT'));
$mobileAppStoreUrl = null;
if ($userAgent->isiOSApp()) {
    $mobileAppStoreUrl = MOBILE_APP_IOS_STORE_URL;
} else if ($userAgent->isAndroidApp()) {
    $mobileAppStoreUrl = MOBILE_APP_ANDROID_STORE_URL;
}
?>
<div class="panel panel-default">
    <div class="panel-heading">
        <p class="text-center">
            <?= __("Update to the newest app") ?>
        </p>
    </div>
    <div class="panel-body add-team-panel-body"
        <p class="text-center">
            <?= __("You’re using a version of Goalous that’s no longer supported. Please update to the newest app version to use Goalous. Thanks!") ?>
        </p>
    </div>
    <div class="panel-body add-team-panel-body text-center">
        <?php if(is_string($mobileAppStoreUrl)): ?>
        <a href="<?= $mobileAppStoreUrl ?>" class="btn btn-primary"><?= __("Update") ?></a>
        <?php endif; ?>
    </div>
</div>
