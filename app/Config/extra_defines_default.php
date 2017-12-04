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
// $defines will be define(key,value);
$defines = [
    'PUBLIC_ENV'                                  => false,
    'ENV_NAME'                                    => "local",
    'DEBUG_MODE'                                  => 2,
    'SECURITY_SALT'                               => "wt37PFe4eDzNF5GRdQtqHDGSTyK7uznbXc1s73ea",
    'SECURITY_CIPHER_SEED'                        => "50705400113420924223192670299",
    'ISAO_TEAM_ID'                                => null,
    'ISAO_EMAIL_DOMAIN'                           => 'isao.co.jp',
    'AWS_ACCESS_KEY'                              => null,
    'AWS_SECRET_KEY'                              => null,
    'S3_LOGS_BUCKET'                              => null,
    'S3_ASSETS_BUCKET'                            => null,
    'REDIS_SESSION_HOST'                          => 'localhost',
    'REDIS_CACHE_HOST'                            => 'localhost',
    'REDIS_HOST'                                  => 'localhost',
    'SES_FROM_ADDRESS'                            => 'support@goalous.com',
    'SES_FROM_ADDRESS_CONTACT'                    => 'contact@goalous.com',
    'SES_FROM_ADDRESS_NEWS'                       => 'news@goalous.com',
    'SES_FROM_ADDRESS_NOTICE'                     => 'notice@goalous.com',
    'GOOGLE_TAG_MANAGER_ID'                       => null,
    'MIXPANEL_TOKEN'                              => null,
    'INTERCOM_APP_ID'                             => null,
    'INTERCOM_IDENTITY_VERIFICATION_SECRET'       => null,
    'PUSHER_ID'                                   => '98763',
    'PUSHER_KEY'                                  => 'cfa05829683ced581f02',
    'PUSHER_SECRET'                               => '5f88d7065071f0439be4',
    'BASIC_AUTH_ID'                               => 'test',
    'BASIC_AUTH_PASS'                             => 'test',
    'NOTIFY_AUTO_UPDATE_SEC'                      => 60 * 60,//1 hour
    'PRE_FILE_TTL'                                => 60 * 60 * 3,// 3 hours
    'TWO_FA_TTL'                                  => 60 * 60 * 24 * 30,
    'ACCOUNT_LOCK_TTL'                            => 60 * 5,
    'ACCOUNT_LOCK_COUNT'                          => 5,
    'EMAIL_VERIFY_CODE_LOCK_TTL'                  => 60 * 5,
    'EMAIL_VERIFY_CODE_LOCK_COUNT'                => 5,
    'SESSION_RENEW_TTL'                           => 60 * 60 * 24 * 7,//1 week
    'NCMB_APPLICATION_KEY'                        => "84b78ad6aeec49510a48a07593acf888a845fa3284633e294733a67b9980fc06",
    'NCMB_CLIENT_KEY'                             => "67fb3910e63499765f60fd91ac5b05882a982221884b482a0b492a93fc62f1f4",
    'NCMB_REST_API_FQDN'                          => "mb.api.cloud.nifty.com",
    'NCMB_REST_API_VER'                           => "2013-09-01",
    'NCMB_REST_API_PUSH'                          => "push",
    'NCMB_REST_API_PUSH_METHOD'                   => "POST",
    'NCMB_REST_API_GET_INSTALLATION'              => "installations",
    'NCMB_REST_API_GET_METHOD'                    => "GET",
    'LOG_ENGINE'                                  => "File",
    'LOG_SLACK_TOKEN'                             => null,
    'LOG_SLACK_ERROR_CHANNEL'                     => null,
    'LOG_SLACK_DEBUG_CHANNEL'                     => null,
    'CACHE_HOMEPAGE'                              => false,
    'SETUP_GUIDE_NOTIFY_DAYS'                     => "2,5,10",
    'SETUP_GUIDE_NOTIFY_HOUR'                     => "11",
    'SETUP_GUIDE_NOTIFY_URL'                      => "http://192.168.50.4",
    // it should be comma separated values. It means each num is timing of notify before expired in all statuses.
    'EXPIRE_ALERT_NOTIFY_BEFORE_DAYS'             => "10,5,3,2,1",
    //trueの場合、FORCE_ENABLE_ALL_EXPERIMENTSより優先される。trueの場合は実験の存在の有無にかかわらず強制的に全ての実験が無効化される。
    'FORCE_DISABLE_ALL_EXPERIMENTS'               => false,
    //trueの場合は実験の存在の有無にかかわらず強制的に全ての実験が有効化される
    'FORCE_ENABLE_ALL_EXPERIMENTS'                => false,
    'SENTRY_DSN'                                  => null,
    // Stripe API keys. https://dashboard.stripe.com/account/apikeys
    'STRIPE_PUBLISHABLE_KEY'                      => 'pk_test_9ne6tSqfUbBXSWqn1XwmeHfb',
    'STRIPE_SECRET_KEY'                           => 'sk_test_MNu6mPlFZRW6Y4KYAZwhGBsU',
    'ATOBARAI_API_BASE_URL'                       => 'https://www1.atobarai-dev.jp',
    'ATOBARAI_ENTERPRISE_ID'                      => '11528',
    'ATOBARAI_SITE_ID'                            => '13868',
    'ATOBARAI_API_USER_ID'                        => '10141',
    'MOBILE_APP_IOS_VERSION_SUPPORTING_LEAST'     => "1.1.2",
    'MOBILE_APP_IOS_STORE_URL'                    => "https://itunes.apple.com/app/goalous-business-sns/id1060474459",
    'MOBILE_APP_ANDROID_VERSION_SUPPORTING_LEAST' => "1.0.4",
    'MOBILE_APP_ANDROID_STORE_URL'                => "https://play.google.com/store/apps/details?id=jp.co.isao.android.goalous2",
];

foreach ($defines as $k => $v) {
    if (!defined($k)) {
        define($k, $v);
    }
}