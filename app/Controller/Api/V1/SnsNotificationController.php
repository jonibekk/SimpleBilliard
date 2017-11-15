<?php
App::uses('ApiController', 'Controller/Api');
App::uses('TranscodeNotificationAwsSns', 'Model/Video/Stream');

use Goalous\Model\Enum as Enum;

class SnsNotificationController extends ApiController
{
    public function beforeFilter()
    {
        $this->Auth->allow();
        parent::beforeFilter();
    }

    public function callback_notify()
    {
        $jsonBody = $this->request->input();
        $jsonData = json_decode($jsonBody, true);
        $headers = iterator_to_array($this->getRequestHeaders());
        CakeLog::info(sprintf('log video callback: %s', AppUtil::jsonOneLine([
            'headers' => $headers,
            'jsonBody' => $jsonData,
            'message' => json_decode($jsonData['Message']),
        ])));
        $result = [
            'meta' => [
                'status' => '200',
                'message' => 'ok',
            ],
            'data' => [
                'header' => $headers,
                'body'   => $jsonData,
                'message' => json_decode($jsonData['Message']),
            ],
        ];

        ///** @var TranscodeNotificationAwsSns $TranscodeNotificationAwsSns */
        //$TranscodeNotificationAwsSns = ClassRegistry::init('TranscodeNotificationAwsSns');
        $transcodeNotificationAwsSns = TranscodeNotificationAwsSns::parseJsonString($jsonBody);

        $progressState = $transcodeNotificationAwsSns->getProgressState();
        if ($progressState->equals(Enum\Video\VideoTranscodeProgress::PROGRESS())) {

        } else if ($progressState->equals(Enum\Video\VideoTranscodeProgress::ERROR())) {

        } else if ($progressState->equals(Enum\Video\VideoTranscodeProgress::COMPLETE())) {

        }

        return $this->_getResponseSuccess($result);
    }

    private function getRequestHeaders(): Generator
    {
        foreach ($_SERVER as $k => $v) {
            if (0 === strpos($k, 'HTTP_')) {
                yield $k => $v;
            }
        }
    }
}