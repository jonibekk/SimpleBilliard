<?php
App::import('Service', 'AppService');

/**
 * Class InvoiceService
 * Invoice using atobarai.com
 * API documentation -> http://bit.ly/2wcSTwT
 */
class InvoiceService extends AppService
{
    const API_URL_REGISTER_ORDER = ATOBARAI_API_BASE_URL . '/api/order/rest';
    const API_URL_INQUIRY_CREDIT_STATUS = ATOBARAI_API_BASE_URL . "/api/status/rest";

    function registerOrder(int $teamId, array $chargeHistories)
    {
        $data = [
            'O_ReceiptOrderDate'     => '2017/08/17',
            'O_EnterpriseId'         => ATOBARAI_ENTERPRISE_ID,
            'O_SiteId'               => ATOBARAI_SITE_ID,
            'O_ApiUserId'            => ATOBARAI_API_USER_ID . "abc",
            'O_UseAmount'            => 2000,
            'C_PostalCode'           => '1720003',
            'C_UnitingAddress'       => '東京都台東区浅草橋1-1-1',
            'C_NameKj'               => '佐藤太郎',
            'C_Phone'                => '03-3333-3333',
            //            'C_MailAddress'          => 'test@aaa.com',
            'C_EntCustId'            => '1234',
            'I_ItemNameKj_1'         => 'Goalous利用料金',
            'O_ServicesProvidedDate' => '2017/08/17',
            'I_UnitPrice_1'          => 2000,
            'I_ItemNum_1'            => 1,
        ];

        $retXmlObj = $this->_postRequestForAtobaraiDotCom(self::API_URL_REGISTER_ORDER, $data);
        var_dump((string)$retXmlObj->status);
        var_dump((string)$retXmlObj->messages->message);
        var_dump((string)$retXmlObj->messages->message->attributes()->cd);
    }

    /**
     * @param string $requestUrl
     * @param array  $data key value array
     *
     * @return SimpleXMLElement
     */
    private function _postRequestForAtobaraiDotCom(string $requestUrl, array $data): SimpleXMLElement
    {
        $data = http_build_query($data);

        // header
        $header = [
            "Content-Type: application/x-www-form-urlencoded",
            "Content-Length: " . strlen($data)
        ];

        $context = [
            "http" => [
                "method"  => "POST",
                "header"  => implode("\r\n", $header),
                "content" => $data
            ]
        ];
        $ret = file_get_contents($requestUrl, false, stream_context_create($context));
        return simplexml_load_string($ret);
    }

}
