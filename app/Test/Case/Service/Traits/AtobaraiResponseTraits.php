<?php
App::uses('XmlAtobaraiResponse', 'AtobaraiCom');

use Goalous\Model\Enum as Enum;

/**
 * register ClassRegistry
 * create
 *
 * AtobaraiCom xml responses
 * Trait AtobaraiResponseTraits
 */
trait AtobaraiResponseTraits
{
    /**
     * @param \GuzzleHttp\Client $client
     */
    protected function registerGuzzleHttpClient(\GuzzleHttp\Client $client)
    {
        $objectKey = \GuzzleHttp\Client::class;
        ClassRegistry::removeObject($objectKey);
        ClassRegistry::addObject($objectKey, $client);
    }

    /**
     * @param string                  $orderId
     * @param string                  $systemOrderId
     * @param Enum\AtobaraiCom\Credit $orderCredit
     *
     * @return \GuzzleHttp\Psr7\Response
     */
    protected function createXmlAtobaraiOrderSucceedResponse(
        string $orderId,
        string $systemOrderId,
        Enum\AtobaraiCom\Credit $orderCredit
    ): \GuzzleHttp\Psr7\Response
    {
        $r = XmlAtobaraiResponse::getOrderSucceed($orderId, $systemOrderId, $orderCredit);
        return new \GuzzleHttp\Psr7\Response($r['status'], [], $r['xml']);
    }

    /**
     * @param array $inquireResults
     *
     * @return \GuzzleHttp\Psr7\Response
     */
    protected function createXmlAtobaraiInquireCreditSucceedResponse(array $inquireResults): \GuzzleHttp\Psr7\Response
    {
        $r = XmlAtobaraiResponse::getInquireCreditStatus($inquireResults);
        return new \GuzzleHttp\Psr7\Response($r['status'], [], $r['xml']);
    }
}
