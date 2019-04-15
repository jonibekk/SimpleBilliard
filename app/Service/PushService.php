<?php
App::import('Service', 'AppService');
App::uses('Device', 'Model');

use Goalous\Enum as Enum;

/**
 * Send Push Notifications thought service providers,
 * manage device tokens.
 * Class PushService
 */
class PushService extends AppService
{
    /**
     * This parameter, when set to true, allows developers to test
     * a request without actually sending a message.
     *
     * @var bool
     */
    public $dryRequest = false;

    /**
     * Send a push notification to a single device using
     * Firebase Cloud Messaging
     *
     * @param array  $deviceTokens
     * @param string $message
     * @param string $postUrl
     *
     * @return bool
     */
    public function sendFirebasePushNotification(array $deviceTokens, string $message, string $postUrl): bool
    {
        $androidTokens = [];
        $iosTokens = [];

        foreach ($deviceTokens as $token) {
            if ($token['os_type'] == Enum\Model\Devices\DeviceType::ANDROID) {
                $androidTokens[] = $token['device_token'];
            } else {
                $iosTokens[] = $token['device_token'];
            }
        }

        // Payload for android and iOS are different,
        // Tokens must be separated by os type
        $res = true;
        if (count($androidTokens) > 0) {
            $res = $this->_sendFirebasePushNotificationForAndroid($androidTokens, $message, $postUrl);
        }

        if (count($iosTokens) > 0) {
            $res = $res & $this->_sendFirebasePushNotificationForIOS($iosTokens, $message, $postUrl);
        }

        return $res;
    }

    /**
     * Send messages to android devices
     *
     * @param array  $deviceTokens
     * @param string $message
     * @param string $postUrl
     *
     * @return bool
     */
    private function _sendFirebasePushNotificationForAndroid(
        array $deviceTokens,
        string $message,
        string $postUrl
    ): bool {
        // Request data
        $data = [
            'data'             => [
                'body' => $message,
                'url'  => $postUrl
            ],
            'registration_ids' => $deviceTokens,
            'dry_run'          => $this->dryRequest
        ];
        return $this->_sendFirebasePush($deviceTokens, $data);
    }

    /**
     * Send messages to iOS devices
     *
     * @param array  $deviceTokens
     * @param string $message
     * @param string $postUrl
     *
     * @return bool
     */
    private function _sendFirebasePushNotificationForIOS(array $deviceTokens, string $message, string $postUrl): bool
    {
        // Request data
        $data = [
            'notification'     => [
                'body'            => $message,
                'mutable_content' => true
            ],
            'data'             => [
                'url' => $postUrl
            ],
            'registration_ids' => $deviceTokens,
            'dry_run'          => $this->dryRequest
        ];
        return $this->_sendFirebasePush($deviceTokens, $data);
    }

    /**
     * Call Firebase Cloud messaging send API
     *
     * @param array $deviceTokens
     * @param array $data
     *
     * @return bool
     */
    private function _sendFirebasePush(array $deviceTokens, array $data): bool
    {
        // Request reader
        $header = [
            'Content-Type'  => 'application/json',
            'Authorization' => 'key=' . FIREBASE_SERVER_KEY,
        ];

        $payload = json_encode($data, JSON_UNESCAPED_UNICODE);
        if (!$payload) {
            GoalousLog::error('Invalid push notification data.', [
                'data'         => $data,
                'deviceTokens' => $deviceTokens
            ]);
            return false;
        }

        // Make request to FCM
        $client = $this->getHttpClient();
        try {
            $response = $client->post(FIREBASE_SEND_URL, [
                'headers' => $header,
                'body'    => $payload
            ]);
        } catch (Exception $e) {
            GoalousLog::error('Failed to call FCM API.', ['Exception' => $e->getMessage()]);
            return false;
        }

        $status = $response->getStatusCode();
        if ($status != 200) {
            GoalousLog::error('Failed to call FCM API.', [
                'StatusCode'   => $status,
                'Reason'       => $response->getReasonPhrase(),
                'deviceTokens' => $deviceTokens
            ]);
            return false;
        }

        $body = $response->getBody()->getContents();
        if (empty($body)) {
            GoalousLog::error('No contents from FCM API call.', ['deviceTokens' => $deviceTokens]);
            return false;
        }

        $result = json_decode($body, true);

        // Failed to send
        // Check if it is an invalid token. If so, remove it from database.
        //
        // The registration token may change when:
        //   The app deletes Instance ID
        //   The app is restored on a new device
        //   The user uninstalls/reinstall the app
        //   The user clears app data.
        if ($result['failure'] >= 1) {
            $invalidTypes = ['MismatchSenderId', 'InvalidRegistration', 'NotRegistered'];
            foreach ($result['results'] as $key => $value) {
                // Check for invalid tokens.
                // Errors are returned on the same order of request
                if (!empty($value['error']) && in_array($value['error'], $invalidTypes, true)) {
                    $invalidToken = $deviceTokens[$key];
                    $this->removeDevice($invalidToken);
                }
            }
        }
        return true;
    }

    /**
     * Send a push notification to a list of devices using
     * Nifty Cloud Mobile Backend
     *
     * @param array  $deviceTokens
     * @param string $message
     * @param string $postUrl
     */
    public function sendNCMBPushNotification(array $deviceTokens, string $message, string $postUrl)
    {
        $app_key = NCMB_APPLICATION_KEY;
        $client_key = NCMB_CLIENT_KEY;

        $timestamp = $this->getNCBTimestamp();
        $signature = $this->getNCMBSignature($timestamp, null, null, $app_key, $client_key);

        $header = array(
            'X-NCMB-Application-Key: ' . $app_key,
            'X-NCMB-Signature: ' . $signature,
            'X-NCMB-Timestamp: ' . $timestamp,
            'Content-Type: application/json'
        );

        $options = array(
            'http' => array(
                'ignore_errors' => true,    // APIリクエストの結果がエラーでもレスポンスボディを取得する
                'max_redirects' => 0,       // リダイレクトはしない
                'method'        => NCMB_REST_API_PUSH_METHOD
            )
        );

        $body = '{
                "immediateDeliveryFlag" : true,
                "target":["ios","android"],
                "searchCondition":{"deviceToken":{ "$inArray":["' . implode('","', $deviceTokens) . '"]}},
                "message":' . $message . ',
                "userSettingValue":{"url":"' . $postUrl . '"}},
                "deliveryExpirationTime":"1 day"
            }';
        $options['http']['content'] = $body;

        $header['content-length'] = 'Content-Length: ' . strlen($body);
        $options['http']['header'] = implode("\r\n", $header);

        $url = "https://" . NCMB_REST_API_FQDN . "/" . NCMB_REST_API_VER . "/" . NCMB_REST_API_PUSH;
        $ret = file_get_contents($url, false, stream_context_create($options));
    }

    /**
     * Save a device token to database.
     *
     * @param int                           $userId
     * @param string                        $deviceToken
     * @param Enum\Model\Devices\DeviceType $deviceType
     * @param string                        $version
     *
     * @return bool
     */
    public function saveDeviceToken(
        int $userId,
        string $deviceToken,
        Enum\Model\Devices\DeviceType $deviceType,
        string $version
    ): bool {
        /** @var Device $Device */
        $Device = ClassRegistry::init('Device');

        // TODO: Delete this logging after solved problem below
        // https://jira.goalous.com/browse/GL-8139
        // Logging Kanko-san's device register on isao env
        if ($userId === 81) {
            GoalousLog::info(
                'Mobile push token register',
                [
                    'user_id'      => $userId,
                    'device_token' => $deviceToken,
                    'os_type'      => $deviceType->getValue(),
                    'version'      => $version,
                ]
            );
        }

        // Check if the device already exists
        $data = $Device->getDeviceByToken($deviceToken);
        if (!empty($data['Device'])) {
            $data['Device']['user_id'] = $userId;
            $data['Device']['os_type'] = $deviceType->getValue();
            $data['Device']['version'] = $version;

            try {
                $Device->save($data, false);
            } catch (Exception $e) {
                GoalousLog::error('Failed to save device info.', [
                    'Exception' => $e->getMessage(),
                    'data'      => $data
                ]);
                return false;
            }
            return true;
        }

        $data['Device'] = [
            'user_id'      => $userId,
            'device_token' => $deviceToken,
            'os_type'      => $deviceType->getValue(),
            'version'      => $version,
        ];

        try {
            $Device->add($data);
        } catch (Exception $e) {
            GoalousLog::error('Failed to save device info.', [
                'Exception' => $e->getMessage(),
                'data'      => $data
            ]);
            return false;
        }
        return true;
    }

    /**
     * Soft delete a device from database.
     * Required cause token can change.
     * The registration token may change when:
     *      The app deletes Instance ID
     *      The app is restored on a new device
     *      The user uninstalls/reinstall the app
     *      The user clears app data.
     *
     * @param string $deviceToken
     *
     * @return bool
     */
    public function removeDevice(string $deviceToken): bool
    {
        /** @var Device $Device */
        $Device = ClassRegistry::init('Device');
        $data = $Device->getDeviceByToken($deviceToken);

        if (empty($data['Device'])) {
            return true;
        }

        try {
            $Device->softDelete($data['Device']['id'], false);
        } catch (Exception $e) {
            GoalousLog::error('Failed remove device.', [
                'Exception' => $e->getMessage(),
                'token'     => $deviceToken
            ]);
            return false;
        }
        return true;
    }

    /**
     * Soft delete device by its NCMB installation id
     *
     * @param string $installationId
     *
     * @return bool
     */
    public function removeInstallationId(string $installationId): bool
    {
        /** @var Device $Device */
        $Device = ClassRegistry::init('Device');
        $data = $Device->getDeviceByInstallationId($installationId);

        if (empty($data['Device'])) {
            return true;
        }

        try {
            $Device->softDelete($data['Device']['id'], false);
        } catch (Exception $e) {
            GoalousLog::error('Failed remove device.', [
                'Exception'      => $e->getMessage(),
                'installationId' => $installationId
            ]);
            return false;
        }
        return true;
    }

    /**
     * NOWなタイムスタンプを生成する。
     * Moved from NotifyBizComponent class
     *
     * @return string
     */
    public function getNCBTimestamp()
    {
        $now = microtime(true);
        $msec = sprintf("%03d", ($now - floor($now)) * 1000);
        return gmdate('Y-m-d\TH:i:s.', floor($now)) . $msec . 'Z';
    }

    /**
     * push通知に必要なパラメータ
     * X-NCMB-SIGNATUREを生成する
     * デフォルトではpush通知用のシグネチャ生成
     * Moved from NotifyBizComponent class
     *
     * @param        $timestamp  シグネチャを生成する時に使うタイムスタンプ
     * @param        $method     シグネチャを生成する時に使うメソッド
     * @param        $path       シグネチャを生成する時に使うパス
     * @param string $app_key    NCMB用 application key
     * @param string $client_key NCMB用 client key
     *
     * @return string X-NCMB-SIGNATUREの値
     */
    public function getNCMBSignature(
        $timestamp,
        $method = null,
        $path = null,
        $app_key = NCMB_APPLICATION_KEY,
        $client_key = NCMB_CLIENT_KEY
    ) {
        $header_string = "SignatureMethod=HmacSHA256&";
        $header_string .= "SignatureVersion=2&";
        $header_string .= "X-NCMB-Application-Key=" . $app_key . "&";
        $header_string .= "X-NCMB-Timestamp=" . $timestamp;

        $signature_string = (($method) ? $method : NCMB_REST_API_PUSH_METHOD) . "\n";
        $signature_string .= NCMB_REST_API_FQDN . "\n";
        if ($path) {
            $signature_string .= $path . "\n";
        } else {
            $signature_string .= "/" . NCMB_REST_API_VER . "/" . NCMB_REST_API_PUSH . "\n";
        }
        $signature_string .= $header_string;

        return base64_encode(hash_hmac("sha256", $signature_string, $client_key, true));
    }
}
