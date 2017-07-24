<?php
/**
 * 外部ツールのlicense key等を定義する
 * Created by PhpStorm.
 * User: bigplants
 * Date: 4/22/14
 * Time: 7:01 PM
 */
define('PUBLIC_ENV', false);
define('ENV_NAME', "local");
define('DEBUG_MODE', 2);
define('SECURITY_SALT', "wt37PFe4eDzNF5GRdQtqHDGSTyK7uznbXc1s73ea");
define('SECURITY_CIPHER_SEED', "50705400113420924223192670299");
define('USERVOICE_API_KEY', null);
define('USERVOICE_SUBDOMAIN', null);
define('USERVOICE_FORUM_ID_PRIVATE', null);
define('USERVOICE_FORUM_ID_PUBLIC', null);
define('ISAO_TEAM_ID', null);
define('ISAO_EMAIL_DOMAIN', 'isao.co.jp');
define('AWS_ACCESS_KEY', null);
define('AWS_SECRET_KEY', null);
define('S3_LOGS_BUCKET', null);
define('S3_ASSETS_BUCKET', null);
define('REDIS_SESSION_HOST', 'localhost');
define('REDIS_CACHE_HOST', 'localhost');
define('REDIS_HOST', 'localhost');
define('SES_FROM_ADDRESS', 'support@goalous.com');
define('SES_FROM_ADDRESS_CONTACT', 'contact@goalous.com');
define('SES_FROM_ADDRESS_NEWS', 'news@goalous.com');
define('SES_FROM_ADDRESS_NOTICE', 'notice@goalous.com');
define('GOOGLE_TAG_MANAGER_ID', null);
define('MIXPANEL_TOKEN', null);
define('VWO_ID', null);
define('INTERCOM_APP_ID', null);
define('FACEBOOK_APP_ID', null);
define('FACEBOOK_SECRET_ID', null);
define('GOOGLE_CLIENT_ID', null);
define('GOOGLE_CLIENT_SECRET_ID', null);
define('PUSHER_ID', '98763');
define('PUSHER_KEY', 'cfa05829683ced581f02');
define('PUSHER_SECRET', '5f88d7065071f0439be4');
define('BASIC_AUTH_ID', 'test');
define('BASIC_AUTH_PASS', 'test');
define('NOTIFY_AUTO_UPDATE_SEC', 60 * 60);//1 hour
define('PRE_FILE_TTL', 60 * 60 * 3);// 3 hours
define('TWO_FA_TTL', 60 * 60 * 24 * 30);
define('ACCOUNT_LOCK_TTL', 60 * 5);
define('ACCOUNT_LOCK_COUNT', 5);
define('EMAIL_VERIFY_CODE_LOCK_TTL', 60 * 5);
define('EMAIL_VERIFY_CODE_LOCK_COUNT', 5);
define('SESSION_RENEW_TTL', 60 * 60 * 24 * 7);//1 week
define('NCMB_APPLICATION_KEY', "84b78ad6aeec49510a48a07593acf888a845fa3284633e294733a67b9980fc06");
define('NCMB_CLIENT_KEY', "67fb3910e63499765f60fd91ac5b05882a982221884b482a0b492a93fc62f1f4");
define('NCMB_REST_API_FQDN', "mb.api.cloud.nifty.com");
define('NCMB_REST_API_VER', "2013-09-01");
define('NCMB_REST_API_PUSH', "push");
define('NCMB_REST_API_PUSH_METHOD', "POST");
define('NCMB_REST_API_GET_INSTALLATION', "installations");
define('NCMB_REST_API_GET_METHOD', "GET");
define('LOG_ENGINE', "File");
define('LOG_SLACK_TOKEN', null);
define('LOG_SLACK_ERROR_CHANNEL', null);
define('LOG_SLACK_DEBUG_CHANNEL', null);
define('CACHE_HOMEPAGE', false);
define('SETUP_GUIDE_NOTIFY_DAYS', "2,5,10");
define('SETUP_GUIDE_NOTIFY_HOUR', "11");
define('SETUP_GUIDE_NOTIFY_URL', "http://192.168.50.4");
define('EXPIRE_ALERT_NOTIFY_BEFORE_DAYS', "10,5,3,2,1"); // it should be comma separated values. It means each num is timing of notify before expired in all statuses.
define('FORCE_DISABLE_ALL_EXPERIMENTS',
    false);//trueの場合、FORCE_ENABLE_ALL_EXPERIMENTSより優先される。trueの場合は実験の存在の有無にかかわらず強制的に全ての実験が無効化される。
define('FORCE_ENABLE_ALL_EXPERIMENTS', true);//trueの場合は実験の存在の有無にかかわらず強制的に全ての実験が有効化される
define('SENTRY_DSN', null);
