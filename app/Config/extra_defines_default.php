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

// https://confluence.goalous.com/x/Z4LT
// $defines will be define(key,value);
$defines = [
    'PUBLIC_ENV'                                    => false,
    'ENV_NAME'                                      => "local",
    'DEBUG_MODE'                                    => 2,
    'SECURITY_SALT'                                 => "wt37PFe4eDzNF5GRdQtqHDGSTyK7uznbXc1s73ea",
    'SECURITY_CIPHER_SEED'                          => "50705400113420924223192670299",
    'ISAO_TEAM_ID'                                  => null,
    'ISAO_EMAIL_DOMAIN'                             => 'isao.co.jp',
    'AWS_ACCESS_KEY'                                => 'AKIAIHP7DNBNP6HADHMA',
    'AWS_SECRET_KEY'                                => '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',
    'S3_LOGS_BUCKET'                                => null,
    'S3_ASSETS_BUCKET'                              => 'goalous-local-assets',
    'REDIS_SESSION_HOST'                            => 'localhost',
    'REDIS_CACHE_HOST'                              => 'localhost',
    'REDIS_HOST'                                    => 'localhost',
    'SES_FROM_ADDRESS'                              => 'support@goalous.com',
    'SES_FROM_ADDRESS_CONTACT'                      => 'contact@goalous.com',
    'SES_FROM_ADDRESS_NEWS'                         => 'news@goalous.com',
    'SES_FROM_ADDRESS_NOTICE'                       => 'notice@goalous.com',
    'GOOGLE_TAG_MANAGER_ID'                         => null,
    'MIXPANEL_TOKEN'                                => null,
    'INTERCOM_APP_ID'                               => null,
    'INTERCOM_IDENTITY_VERIFICATION_SECRET'         => null,
    'PUSHER_ID'                                     => '98763',
    'PUSHER_KEY'                                    => 'cfa05829683ced581f02',
    'PUSHER_SECRET'                                 => '5f88d7065071f0439be4',
    'BASIC_AUTH_ID'                                 => 'test',
    'BASIC_AUTH_PASS'                               => 'test',
    'NOTIFY_AUTO_UPDATE_SEC'                        => 60 * 60,//1 hour
    'PRE_FILE_TTL'                                  => 60 * 60 * 3,// 3 hours
    'TWO_FA_TTL'                                    => 60 * 60 * 24 * 30,
    'ACCOUNT_LOCK_TTL'                              => 60 * 5,
    'ACCOUNT_LOCK_COUNT'                            => 5,
    'EMAIL_VERIFY_CODE_LOCK_TTL'                    => 60 * 5,
    'EMAIL_VERIFY_CODE_LOCK_COUNT'                  => 5,
    'SESSION_RENEW_TTL'                             => 60 * 60 * 24 * 7,//1 week
    'FIREBASE_SERVER_KEY'                           => "AAAAL6eMdaw:APA91bG8YbMd_OD59h3N_UgYQb8GVOqNzCGOhiPaYdvs6Br1rFAuiFcmGNCbEqpCDxPlGUZP0rcrqy-ZH_7jv75VkWjOIoSldxXhvDNOy-IQIgX5X5yHrO1Hv3rya8Maiyrf1G5PddCj",
    'FIREBASE_SEND_URL'                             => "https://fcm.googleapis.com/fcm/send",
    'NCMB_APPLICATION_KEY'                          => "bf4a9d5e9b501822ba8bbac08d81468d1b4f88f97187220607bc91d77d13441d",
    'NCMB_CLIENT_KEY'                               => "e1dc2a372918258f188855b9717d202acbbafdc6d489ee65d2a2fd413711a084",
    'NCMB_REST_API_FQDN'                            => "mb.api.cloud.nifty.com",
    'NCMB_REST_API_VER'                             => "2013-09-01",
    'NCMB_REST_API_PUSH'                            => "push",
    'NCMB_REST_API_PUSH_METHOD'                     => "POST",
    'NCMB_REST_API_GET_INSTALLATION'                => "installations",
    'NCMB_REST_API_GET_METHOD'                      => "GET",
    'LOG_ENGINE'                                    => "File",
    'LOG_SLACK_TOKEN'                               => null,
    'LOG_SLACK_ERROR_CHANNEL'                       => null,
    'LOG_SLACK_DEBUG_CHANNEL'                       => null,
    'CACHE_HOMEPAGE'                                => false,
    'SETUP_GUIDE_NOTIFY_DAYS'                       => "2,5,10",
    'SETUP_GUIDE_NOTIFY_HOUR'                       => "11",
    'SETUP_GUIDE_NOTIFY_URL'                        => "http://192.168.50.4",
    // it should be comma separated values. It means each num is timing of notify before expired in all statuses.
    'EXPIRE_ALERT_NOTIFY_BEFORE_DAYS'               => "10,5,3,2,1",
    //trueの場合、FORCE_ENABLE_ALL_EXPERIMENTSより優先される。trueの場合は実験の存在の有無にかかわらず強制的に全ての実験が無効化される。
    'FORCE_DISABLE_ALL_EXPERIMENTS'                 => false,
    //trueの場合は実験の存在の有無にかかわらず強制的に全ての実験が有効化される
    'FORCE_ENABLE_ALL_EXPERIMENTS'                  => false,
    'SENTRY_DSN'                                    => null,
    // Stripe API keys. https://dashboard.stripe.com/account/apikeys
    'STRIPE_PUBLISHABLE_KEY'                        => 'pk_test_9ne6tSqfUbBXSWqn1XwmeHfb',
    'STRIPE_SECRET_KEY'                             => 'sk_test_MNu6mPlFZRW6Y4KYAZwhGBsU',
    'ATOBARAI_API_BASE_URL'                         => 'https://www1.atobarai-dev.jp',
    'ATOBARAI_ENTERPRISE_ID'                        => '11528',
    'ATOBARAI_SITE_ID'                              => '13868',
    'ATOBARAI_API_USER_ID'                          => '10141',
    // TODO: Remove reference after NCMB apps been discontinued
    'MOBILE_APP_IOS_VERSION_NEW_HEADER'             => "1.2",
    'MOBILE_APP_IOS_VERSION_SUPPORTING_LEAST'       => "1.1.2",
    'MOBILE_APP_IOS_STORE_URL'                      => "https://itunes.apple.com/app/goalous-business-sns/id1060474459",
    'MOBILE_APP_ANDROID_VERSION_SUPPORTING_LEAST'   => "1.0.4",
    'MOBILE_APP_ANDROID_STORE_URL'                  => "https://play.google.com/store/apps/details?id=jp.co.isao.android.goalous2",
    // Should be bool value
    // OpsWorks Custom JSON output true/false as string
    'ENABLE_VIDEO_POST_TRANSCODING'                 => '1',
    'ENABLE_VIDEO_POST_PLAY'                        => '1',
    'AWS_ELASTIC_TRANSCODER_KEY'                    => 'AKIAJWRB3ISRYGDYHV5A',
    'AWS_ELASTIC_TRANSCODER_SECRET_KEY'             => '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',
    'AWS_ELASTIC_TRANSCODER_PIPELINE_ID'            => null,
    'AWS_S3_BUCKET_VIDEO_ORIGINAL'                  => null,
    'AWS_S3_BUCKET_VIDEO_TRANSCODED'                => null,
    // For local env
    // Used for storage key name sharing AWS ElasticTranscode PipeLine on
    // Local and development env
    'AWS_S3_BUCKET_VIDEO_TRANSCODE_LOCAL_SEPARATOR' => null,
    'JWT_TOKEN_SECRET_KEY_AUTH'                     => 'jwt_secret_key_auth',
    //For local S3 user name
    //https://confluence.goalous.com/x/agMQAQ
    'AWS_S3_BUCKET_USERNAME'                        => null,
    //For temporary files, such as upload buffering
    'AWS_S3_BUCKET_TMP'                             => 'goalous-local-tmp',
    'ES_API_BASE_URL'                               => 'dev-search.goalous.com',
    // Default translation limit for paid team. 1 million chars
    'TRANSLATION_DEFAULT_LIMIT_PAID_TEAM'           => 1000000
];
// for local
if (file_exists(APP . 'Config/extra_defines_local.php')) {
    require_once(APP . 'Config/extra_defines_local.php');
    $defines = array_merge($defines, $definesForLocal);
}

//If on docker, use redis container
if (!empty(getenv('DOCKER_ENV'))) {
    $defines['REDIS_SESSION_HOST'] = 'redis';
    $defines['REDIS_CACHE_HOST'] = 'redis';
    $defines['REDIS_HOST'] = 'redis';
}
//If on Travis, set S3 Username
if (!empty(getenv('CI_TEST'))) {
    $defines['AWS_S3_BUCKET_USERNAME'] = 'travis';
}

foreach ($defines as $k => $v) {
    if (!defined($k)) {
        define($k, $v);
    }
}
