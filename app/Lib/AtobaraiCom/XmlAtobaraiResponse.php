<?php

use Goalous\Model\Enum as Enum;

/**
 * the atobarai.com test api response is fixed values (only to be succeed)
 * we need to test about other case like invoice is failed
 *
 * this class create failed xml response from atobarai.com
 * so easy to be mock
 *
 * Class XmlAtobaraiResponse
 */
class XmlAtobaraiResponse
{
    private static function getCdString(Enum\AtobaraiCom\Credit $orderCredit): string
    {
        $map = [
            Enum\AtobaraiCom\Credit::CANCELED        => 'キャンセル',
            Enum\AtobaraiCom\Credit::OK              => '与信OK',
            Enum\AtobaraiCom\Credit::NG              => '与信NG',
            Enum\AtobaraiCom\Credit::IN_JUDGE        => '与信中',
            Enum\AtobaraiCom\Credit::ORDER_NOT_FOUND => 'ID不正',
        ];
        return $map[$orderCredit->getValue()] ?? '';
    }

    /**
     * simulate response of 注文登録API (new order API)
     *
     * @param string $orderId
     * @param string $systemOrderId
     * @param Enum\AtobaraiCom\Credit $orderCreditStatus
     *
     * @return array
     */
    public static function getOrderSucceed(
        string $orderId,
        string $systemOrderId,
        Enum\AtobaraiCom\Credit $orderCreditStatus): array
    {
        $creditString = self::getCdString($orderCreditStatus);
        return [
            'status' => 200,
            'xml' => <<< XML
<?xml version="1.0" encoding="UTF-8"?>
<response>
	<status>success</status>
	<orderId>{$orderId}</orderId>
	<systemOrderId>{$systemOrderId}</systemOrderId>
	<messages />
	<orderStatus cd="{$orderCreditStatus->getValue()}">{$creditString}</orderStatus>
</response>
XML
        ];
    }

    /**
     * simulate response of 与信状況問い合わせAPI (check invoice API)
     *
     * @param array $results [
     *      [
     *          'orderId'     => string,
     *          'entOrderId'  => string,
     *          'orderCreditStatus' => Enum\AtobaraiCom\Credit,
     *      ],
     *      ...
     * ]
     *
     * @return array
     */
    public static function getInquireCreditStatusSucceed(array $results): array
    {
        $resultsXml = '';
        foreach ($results as $result) {
            $orderId = $result['orderId'];
            $entOrderId = $result['entOrderId'];
            $orderCreditStatus = $result['orderCreditStatus'];
            $creditString = self::getCdString($orderCreditStatus);
            $resultsXml .= <<< XML
		<result>
			<orderId>{$orderId}</orderId>
			<entOrderId>{$entOrderId}</entOrderId>
			<orderStatus cd="{$orderCreditStatus->getValue()}">{$creditString}</orderStatus>
		</result>
XML;
        }
        return [
            'status' => 200,
            'xml' => <<< XML
<?xml version="1.0" encoding="UTF-8"?>
<response>
	<status>success</status>
	<messages />
	<results>
	    {$resultsXml}
	</results>
</response>
XML
        ];
    }


}
