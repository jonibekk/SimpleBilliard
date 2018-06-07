<?php

use Goalous\Enum as Enum;

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
     * simulate response of 注文登録API error (new order API)
     *
     * @param string $orderId
     *
     * @return array
     */
    public static function getOrderError(string $orderId): array
    {
        return [
            'status' => 200, // not defined on .pdf
            'xml' => <<< XML
<?xml version="1.0" encoding="UTF-8"?>
<response>
	<status>error</status>
	<orderId>{$orderId}</orderId>
	<messages>
		<message cd="E00201">
			請求金額合計 : データを0または空にすることはできません
		</message>
		<message cd="E00202">
			有効な注文IDが指定されていません
		</message>
	</messages>
</response>

XML
        ];
    }

    /**
     * simulate response of 与信状況問い合わせAPI (check credit invoice API)
     *
     * if passed 'orderCreditStatus' is all Enum\AtobaraiCom\Credit::ORDER_NOT_FOUND()
     * xml status returns <status>error</status>
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
     * @see 後払い.com API 外部インターフェイス仕様書 与信状況問い合わせAPI
     * https://confluence.goalous.com/pages/viewpage.action?pageId=9371952&preview=/9371952/9371959/%E5%A4%96%E9%83%A8%EF%BC%A9%EF%BC%A6%E4%BB%95%E6%A7%98%E6%9B%B8%EF%BC%8802%EF%BC%9A%E4%B8%8E%E4%BF%A1%E7%8A%B6%E6%B3%81%E5%95%8F%E3%81%84%E5%90%88%E3%82%8F%E3%81%9B%EF%BC%A1%EF%BC%B0%EF%BC%A9%EF%BC%89.pdf
     * @return array
     */
    public static function getInquireCreditStatus(array $results): array
    {
        $resultsXml = '';
        $isOrderIdFound = false;
        foreach ($results as $result) {
            $orderId = $result['orderId'];
            $entOrderId = $result['entOrderId'];
            /** @var Enum\AtobaraiCom\Credit $orderCreditStatus */
            $orderCreditStatus = $result['orderCreditStatus'];
            $creditString = self::getCdString($orderCreditStatus);
            if (!$orderCreditStatus->equals(Enum\AtobaraiCom\Credit::ORDER_NOT_FOUND())) {
                $isOrderIdFound = true;
            }
            $resultsXml .= <<< XML
		<result>
			<orderId>{$orderId}</orderId>
			<entOrderId>{$entOrderId}</entOrderId>
			<orderStatus cd="{$orderCreditStatus->getValue()}">{$creditString}</orderStatus>
		</result>
XML;
        }
        $status = $isOrderIdFound ? 'success' : 'error';
        $messages = $isOrderIdFound ? '<messages />' : '<message cd="E00202">有効な注文IDが指定されていません</message>';
        return [
            'status' => 200,
            'xml' => <<< XML
<?xml version="1.0" encoding="UTF-8"?>
<response>
	<status>{$status}</status>
	{$messages}
	<results>
	    {$resultsXml}
	</results>
</response>
XML
        ];
    }


}
