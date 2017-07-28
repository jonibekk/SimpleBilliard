<?php

$langCode = $this->Lang->getLangCode();
$mobileAppStoreUrl = null;
if ($userAgent->isiOSApp()) {
    if ('ja' === $langCode) {
        $mobileAppStoreUrl = MOBILE_APP_IOS_STORE_URL_JA;
    } else if ('en' === $langCode) {
        $mobileAppStoreUrl = MOBILE_APP_IOS_STORE_URL_EN;
    }
} else if ($userAgent->isAndroidApp()) {
    if ('ja' === $langCode) {
        $mobileAppStoreUrl = MOBILE_APP_ANDROID_STORE_URL_JA;
    } else if ('en' === $langCode) {
        $mobileAppStoreUrl = MOBILE_APP_ANDROID_STORE_URL_EN;
    }
}
?>
<p>
現在ご利用のアプリのバージョンは対応していません
</p>

<?php if(is_string($mobileAppStoreUrl)): ?>
  <p>
      <a href="<?= $mobileAppStoreUrl ?>">こちらから最新をダウンロード</a>
  </p>
<?php endif; ?>
