<?php
/**
 * 外部ツールのlicense key等を定義する
 * Created by PhpStorm.
 * User: bigplants
 * Date: 4/22/14
 * Time: 7:01 PM
 */
// extra_defines.php is made by chef recipes
if (file_exists(APP . 'Config/extra_defines.php')) {
    require_once(APP . 'Config/extra_defines.php');
}
var_dump(file_exists(APP . 'Config/extra_defines.php'));
if (!defined('PUBLIC_ENV')) {
    define('PUBLIC_ENV', false);
}
if (!defined('ENV_NAME')) {
    define('ENV_NAME', "local");
}
if (!defined('DEBUG_MODE')) {
    define('DEBUG_MODE', 2);
}

if (!defined('SECURITY_SALT')) {
    define('SECURITY_SALT', "wt37PFe4eDzNF5GRdQtqHDGSTyK7uznbXc1s73ea");
}
if (!defined('SECURITY_CIPHER_SEED')) {
    define('SECURITY_CIPHER_SEED', "50705400113420924223192670299");
}
if (!defined('ISAO_TEAM_ID')) {
    define('ISAO_TEAM_ID', null);
}
if (!defined('ISAO_EMAIL_DOMAIN')) {
    define('ISAO_EMAIL_DOMAIN', 'isao.co.jp');
}
if (!defined('AWS_ACCESS_KEY')) {
    define('AWS_ACCESS_KEY', null);
}
if (!defined('AWS_SECRET_KEY')) {
    define('AWS_SECRET_KEY', null);
}
if (!defined('S3_LOGS_BUCKET')) {
    define('S3_LOGS_BUCKET', null);
}
if (!defined('S3_ASSETS_BUCKET')) {
    define('S3_ASSETS_BUCKET', null);
}

if (!defined('REDIS_SESSION_HOST')) {
    define('REDIS_SESSION_HOST', 'localhost');
}

if (!defined('REDIS_CACHE_HOST')) {
    define('REDIS_CACHE_HOST', 'localhost');
}

if (!defined('REDIS_HOST')) {
    define('REDIS_HOST', 'localhost');
}

if (!defined('SES_FROM_ADDRESS')) {
    define('SES_FROM_ADDRESS', 'support@goalous.com');
}

if (!defined('SES_FROM_ADDRESS_CONTACT')) {
    define('SES_FROM_ADDRESS_CONTACT', 'contact@goalous.com');
}

if (!defined('SES_FROM_ADDRESS_NEWS')) {
    define('SES_FROM_ADDRESS_NEWS', 'news@goalous.com');
}

if (!defined('SES_FROM_ADDRESS_NOTICE')) {
    define('SES_FROM_ADDRESS_NOTICE', 'notice@goalous.com');
}

if (!defined('GOOGLE_TAG_MANAGER_ID')) {
    define('GOOGLE_TAG_MANAGER_ID', null);
}

if (!defined('MIXPANEL_TOKEN')) {
    define('MIXPANEL_TOKEN', null);
}

if (!defined('INTERCOM_APP_ID')) {
    define('INTERCOM_APP_ID', null);
}

if (!defined('INTERCOM_IDENTITY_VERIFICATION_SECRET')) {
    define('INTERCOM_IDENTITY_VERIFICATION_SECRET', null);
}

if (!defined('PUSHER_ID')) {
    define('PUSHER_ID', '98763');
}

if (!defined('PUSHER_KEY')) {
    define('PUSHER_KEY', 'cfa05829683ced581f02');
}

if (!defined('PUSHER_SECRET')) {
    define('PUSHER_SECRET', '5f88d7065071f0439be4');
}

if (!defined('BASIC_AUTH_ID')) {
    define('BASIC_AUTH_ID', 'test');
}

if (!defined('BASIC_AUTH_PASS')) {
    define('BASIC_AUTH_PASS', 'test');
}

//1 hour
if (!defined('NOTIFY_AUTO_UPDATE_SEC')) {
    define('NOTIFY_AUTO_UPDATE_SEC', 60 * 60);
}

// 3 hours
if (!defined('PRE_FILE_TTL')) {
    define('PRE_FILE_TTL', 60 * 60 * 3);
}

if (!defined('TWO_FA_TTL')) {
    define('TWO_FA_TTL', 60 * 60 * 24 * 30);
}

if (!defined('ACCOUNT_LOCK_TTL')) {
    define('ACCOUNT_LOCK_TTL', 60 * 5);
}

if (!defined('ACCOUNT_LOCK_COUNT')) {
    define('ACCOUNT_LOCK_COUNT', 5);
}

if (!defined('EMAIL_VERIFY_CODE_LOCK_TTL')) {
    define('EMAIL_VERIFY_CODE_LOCK_TTL', 60 * 5);
}

if (!defined('EMAIL_VERIFY_CODE_LOCK_COUNT')) {
    define('EMAIL_VERIFY_CODE_LOCK_COUNT', 5);
}

//1 week
if (!defined('SESSION_RENEW_TTL')) {
    define('SESSION_RENEW_TTL', 60 * 60 * 24 * 7);
}

if (!defined('NCMB_APPLICATION_KEY')) {
    define('NCMB_APPLICATION_KEY', "84b78ad6aeec49510a48a07593acf888a845fa3284633e294733a67b9980fc06");
}

if (!defined('NCMB_CLIENT_KEY')) {
    define('NCMB_CLIENT_KEY', "67fb3910e63499765f60fd91ac5b05882a982221884b482a0b492a93fc62f1f4");
}

if (!defined('NCMB_REST_API_FQDN')) {
    define('NCMB_REST_API_FQDN', "mb.api.cloud.nifty.com");
}

if (!defined('NCMB_REST_API_VER')) {
    define('NCMB_REST_API_VER', "2013-09-01");
}

if (!defined('NCMB_REST_API_PUSH')) {
    define('NCMB_REST_API_PUSH', "push");
}

if (!defined('NCMB_REST_API_PUSH_METHOD')) {
    define('NCMB_REST_API_PUSH_METHOD', "POST");
}

if (!defined('NCMB_REST_API_GET_INSTALLATION')) {
    define('NCMB_REST_API_GET_INSTALLATION', "installations");
}

if (!defined('NCMB_REST_API_GET_METHOD')) {
    define('NCMB_REST_API_GET_METHOD', "GET");
}

if (!defined('LOG_ENGINE')) {
    define('LOG_ENGINE', "File");
}

if (!defined('LOG_SLACK_TOKEN')) {
    define('LOG_SLACK_TOKEN', null);
}

if (!defined('LOG_SLACK_ERROR_CHANNEL')) {
    define('LOG_SLACK_ERROR_CHANNEL', null);
}

if (!defined('LOG_SLACK_DEBUG_CHANNEL')) {
    define('LOG_SLACK_DEBUG_CHANNEL', null);
}

if (!defined('CACHE_HOMEPAGE')) {
    define('CACHE_HOMEPAGE', false);
}

if (!defined('SETUP_GUIDE_NOTIFY_DAYS')) {
    define('SETUP_GUIDE_NOTIFY_DAYS', "2,5,10");
}

if (!defined('SETUP_GUIDE_NOTIFY_HOUR')) {
    define('SETUP_GUIDE_NOTIFY_HOUR', "11");
}

if (!defined('SETUP_GUIDE_NOTIFY_URL')) {
    define('SETUP_GUIDE_NOTIFY_URL', "http://192.168.50.4");
}

//trueの場合、FORCE_ENABLE_ALL_EXPERIMENTSより優先される。trueの場合は実験の存在の有無にかかわらず強制的に全ての実験が無効化される。}
if (!defined('FORCE_DISABLE_ALL_EXPERIMENTS')) {
    define('FORCE_DISABLE_ALL_EXPERIMENTS', false);
}

//trueの場合は実験の存在の有無にかかわらず強制的に全ての実験が有効化される}
if (!defined('FORCE_ENABLE_ALL_EXPERIMENTS')) {
    define('FORCE_ENABLE_ALL_EXPERIMENTS', true);
}

if (!defined('SENTRY_DSN')) {
    define('SENTRY_DSN', null);
}

if (!defined('MOBILE_APP_IOS_VERSION_SUPPORTING_LEAST')) {
    define('MOBILE_APP_IOS_VERSION_SUPPORTING_LEAST', "1.0.0");
}

if (!defined('MOBILE_APP_IOS_STORE_URL')) {
    define('MOBILE_APP_IOS_STORE_URL', "https://itunes.apple.com/app/goalous-business-sns/id1060474459");
}

if (!defined('MOBILE_APP_ANDROID_VERSION_SUPPORTING_LEAST')) {
    define('MOBILE_APP_ANDROID_VERSION_SUPPORTING_LEAST', "1.0.4");
}

if (!defined('MOBILE_APP_ANDROID_STORE_URL')) {
    define('MOBILE_APP_ANDROID_STORE_URL', "https://play.google.com/store/apps/details?id=jp.co.isao.android.goalous2");
}

// it should be comma separated values. It means each num is timing of notify before expired in all statuses.
if (!defined('EXPIRE_ALERT_NOTIFY_BEFORE_DAYS')) {
    define('EXPIRE_ALERT_NOTIFY_BEFORE_DAYS', "10,5,3,2,1");
}
// Stripe API keys. https://dashboard.stripe.com/account/apikeys}
if (!defined('STRIPE_PUBLISHABLE_KEY')) {
    define('STRIPE_PUBLISHABLE_KEY', 'pk_test_9ne6tSqfUbBXSWqn1XwmeHfb');
}
if (!defined('STRIPE_SECRET_KEY')) {
    define('STRIPE_SECRET_KEY', 'sk_test_MNu6mPlFZRW6Y4KYAZwhGBsU');
}
if (!defined('ATOBARAI_API_BASE_URL')) {
    define('ATOBARAI_API_BASE_URL', 'https://www1.atobarai-dev.jp');
}
if (!defined('ATOBARAI_ENTERPRISE_ID')) {
    define('ATOBARAI_ENTERPRISE_ID', '11528');
}
if (!defined('ATOBARAI_SITE_ID')) {
    define('ATOBARAI_SITE_ID', '13868');
}
if (!defined('ATOBARAI_API_USER_ID')) {
    define('ATOBARAI_API_USER_ID', '10141');
}
